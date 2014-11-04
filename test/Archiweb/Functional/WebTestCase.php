<?php


namespace Archiweb\Functional;


use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use GuzzleHttp\Client;
use GuzzleHttp\Message\ResponseInterface;
use GuzzleHttp\Ring\Client\CurlHandler;
use PHPUnit_Framework_TestCase;

abstract class WebTestCase extends \PHPUnit_Framework_TestCase {

    /**
     * @var string[]
     */
    protected static $createSchemaSQL;

    /**
     * @var ResponseInterface
     */
    protected static $lastRequest;

    public static function setUpBeforeClass () {

        parent::setUpBeforeClass();
        self::resetDatabase();

    }

    public static function resetDatabase () {

        require __DIR__ . '/../../../doctrine/config.php';
        /**
         * @var array         $conn
         * @var EntityManager $entityManager
         */

        $schemaTool = new SchemaTool($entityManager);
        $entityManager->getConnection()->query('PRAGMA foreign_keys = OFF');
        $schemaTool->dropDatabase();

        if (!isset(self::$createSchemaSQL)) {
            self::$createSchemaSQL =
                $schemaTool->getCreateSchemaSql($entityManager->getMetadataFactory()->getAllMetadata());
        }
        /**
         * @var Connection $conn
         */
        $conn = $entityManager->getConnection();
        foreach (self::$createSchemaSQL as $sql) {
            $conn->executeQuery($sql);
        }

    }

    /**
     * @param       $service
     * @param       $method
     * @param array $params
     * @param null  $entity
     * @param array $fields
     * @param null  $auth
     *
     * @throws \Exception
     */
    public static function get ($service, $method, array $params = [], $entity = NULL, array $fields = [],
                                $auth = NULL) {

        $config = ['base_url' => 'http://localhost/archipad-proto/run.php/jsonp/archipad-cloud+1+fr/'];
        if (version_compare(PHP_VERSION, '5.5.0')) {
            $config['handler'] = new CurlHandler();
        }
        $client = new Client($config);

        $url = '';
        if (isset($service)) {
            $url .= urlencode($service) . '/';
        }

        $url .= '?';

        if (isset($method)) {
            $url .= 'method=' . urlencode($method) . '&';
        }

        $params['auth'] = $auth;
        $paramsQuery = http_build_query(['params' => $params]);
        if ($paramsQuery) {
            $url .= $paramsQuery . '&';
        }

        if (isset($entity)) {
            $url .= 'entity=' . urlencode($entity) . '&';
        }

        $url .= http_build_query(['fields' => $fields]);

        self::$lastRequest = $client->get($url, ['cookies' => ['XDEBUG_SESSION' => 'PHPSTORM'],]);

        try {
            return self::$lastRequest->json(['object' => true, /*'big_int_strings' => true*/]);
        }
        catch (\Exception $e) {
            self::fail('mal formated response to the request : ' . self::$lastRequest->getEffectiveUrl() . "\n"
                       . self::$lastRequest->getBody());
        }

    }

} 
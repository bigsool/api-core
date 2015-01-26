<?php


namespace Core\Functional;


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
     * @param null  $id
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public static function get ($service, $method, array $params = [], $entity = NULL, array $fields = [],
                                $auth = NULL, $id = NULL) {

        $config = ['base_url' => 'http://localhost/api/core/run.php/JSON/archipad-cloud+1+fr/'];
        if (version_compare(PHP_VERSION, '5.5.0') >= 0) {
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

        if (!isset($id)) {
            $id = uniqid();
        }
        $url .= 'id=' . urlencode($id) . '&';

        $url .= http_build_query(['fields' => $fields]);


        self::$lastRequest = $client->get($url, ['cookies' => ['XDEBUG_SESSION' => 'PHPSTORM'],]);

        try {
            return [$id => self::$lastRequest->json(['object' => true, /*'big_int_strings' => true*/])];
        }
        catch (\Exception $e) {
            self::fail('mal formated response to the request : ' . self::$lastRequest->getEffectiveUrl() . "\n"
                       . self::$lastRequest->getBody());
        }

    }

    /**
     * @param mixed       $result
     * @param string|null id
     * @param bool        $hasMoreProperties
     */
    public function assertSuccess ($result, $id = NULL, $hasMoreProperties = false) {

        $this->assertInstanceOf('\stdClass', $result);

        $properties = get_object_vars($result);
        if ($hasMoreProperties) {
            $this->assertGreaterThan(3, count($properties));
        }
        else {
            $this->assertCount(3, $properties);
        }
        $this->assertArrayHasKey('jsonrpc', $properties);
        $this->assertArrayHasKey('id', $properties);
        $this->assertArrayHasKey('result', $properties);
        $this->assertArrayNotHasKey('error', $properties);
        $this->assertSame('2.0', $properties['jsonrpc']);
        $this->assertSame($id, $properties['id']);

    }

    /**
     * @param mixed       $result
     * @param string|null id
     * @param int         $errorCode
     * @param array       $childErrorCodes
     * @param string      $field
     * @param bool        $hasMoreProperties
     */
    public function assertFail ($result, $id, $errorCode, array $childErrorCodes = [], $field = NULL,
                                $hasMoreProperties = false) {

        $this->assertInstanceOf('\stdClass', $result);

        $properties = get_object_vars($result);
        if ($hasMoreProperties) {
            $this->assertGreaterThan(3, count($properties));
        }
        else {
            $this->assertCount(3, $properties);
        }

        $this->assertArrayNotHasKey('result', $properties);
        $this->assertSame('2.0', $properties['jsonrpc']);
        $this->assertSame($id, $properties['id']);

        $this->assertArrayHasKey('error', $properties);
        $error = $properties['error'];
        $this->assertInstanceOf('\stdClass', $error);
        $errorProperties = get_object_vars($error);

        if (is_null($errorCode)) {
            $this->assertArrayNotHasKey('code', $errorProperties);
        }
        else {
            $this->assertArrayHasKey('code', $errorProperties);
            $this->assertEquals($errorCode, $errorProperties['code']);
        }

        if (count($childErrorCodes)) {
            $this->assertArrayHasKey('errors', $errorProperties);
            $errors = $errorProperties['errors'];
            $this->assertInternalType('array', $errors);
            $this->assertRecursiveErrorCodes($errors, $childErrorCodes);
        }
        else {
            $this->assertArrayNotHasKey('errors', $errorProperties);
        }

        if (is_null($field)) {
            $this->assertArrayNotHasKey('field', $errorProperties);
        }
        else {
            $this->assertArrayHasKey('field', $errorProperties);
            $this->assertEquals($field, $errorProperties['field']);
        }

    }

    /**
     * @param array $errors
     * @param array $errorCodes
     */
    protected function assertRecursiveErrorCodes (array $errors, array $errorCodes) {

        $this->assertCount(count($errors), $errorCodes);

        $reformattedErrors = [];
        foreach ($errors as $error) {
            $this->assertInstanceOf('\stdClass', $error);
            $properties = get_object_vars($error);
            $this->assertArrayHasKey('code', $properties);
            $code = $properties['code'];
            if (isset($reformattedErrors[$code])) {
                $this->fail("Duplicated error code '{$code}' in childErrors");
            }
            $reformattedErrors[$code] = $properties;
        }

        foreach ($errorCodes as $key => $value) {
            $errorCode = is_array($value) ? $key : $value;
            $this->assertArrayHasKey($errorCode, $reformattedErrors);
            if (is_array($value)) { // if child errors
                $reformattedError = $reformattedErrors[$errorCode];
                $this->assertArrayHasKey('errors', $reformattedError);
                $childErrors = $reformattedError['errors'];
                $this->assertInternalType('array', $childErrors);
                $this->assertRecursiveErrorCodes($childErrors, $value);
            }
        }

    }

} 
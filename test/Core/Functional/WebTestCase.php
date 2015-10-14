<?php


namespace Core\Functional;


use Core\Context\ApplicationContext;
use Doctrine\DBAL\Driver\PDOSqlite\Driver as SqliteDriver;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use GuzzleHttp\Message\ResponseInterface;
use GuzzleHttp\Ring\Client\CurlHandler;
use GuzzleHttp\Subscriber\Cookie;
use PHPUnit_Framework_TestCase;

abstract class WebTestCase extends \PHPUnit_Framework_TestCase {

    /**
     * @var EntityManager
     */
    protected static $entityManager;

    /**
     * @var Client
     */
    protected static $client;

    /**
     * @var CookieJar
     */
    protected static $cookies;

    /**
     * @var string[]
     */
    protected static $createSchemaSQL;

    /**
     * @var ResponseInterface
     */
    protected static $lastRequest;

    /**
     *
     */
    public static function setUpBeforeClass () {

        parent::setUpBeforeClass();

        self::resetDatabase();

        self::createClient();

    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public static function resetDatabase () {

        if (!isset(self::$entityManager)) {

            require static::getRootFolder() . '/doctrine/config.php';

            /**
             * @var EntityManager $entityManager
             */

            self::$entityManager = $entityManager;

        }

        $schemaTool = new SchemaTool(self::$entityManager);
        $conn = self::$entityManager->getConnection();

        if ($conn->getDriver() instanceof SqliteDriver) {
            $conn->query('PRAGMA foreign_keys = OFF');
        }
        else {
            $conn->query('SET FOREIGN_KEY_CHECKS=0');
        }

        $schemaTool->dropDatabase();

        // use a static property instead of a var to keep the result which is expensive to construct
        if (!isset(self::$createSchemaSQL)) {
            self::$createSchemaSQL =
                $schemaTool->getCreateSchemaSql(self::$entityManager->getMetadataFactory()->getAllMetadata());
        }

        foreach (self::$createSchemaSQL as $sql) {
            $conn->executeQuery($sql);
        }

        if ($conn->getDriver() instanceof SqliteDriver) {
            $conn->query('PRAGMA foreign_keys = ON');
        }
        else {
            $conn->query('SET FOREIGN_KEY_CHECKS=1');
        }

    }

    /**
     * @return string
     */
    protected static function getRootFolder () {

        return __DIR__ . '/../../..';

    }

    /**
     *
     */
    protected static function createClient () {

        $wwwPath = static::getWWWPath();
        $config = [
            'base_url' => "http://localhost/{$wwwPath}/jsonrpc/",
            'handler'  => new CurlHandler(),
            'defaults' => [
                'headers' => [
                    'User-Agent' => ApplicationContext::UNIT_TESTS_USER_ARGENT
                ]
            ]
        ];
        self::$client = new Client($config);
        self::$cookies = CookieJar::fromArray(['XDEBUG_SESSION' => 'PHPSTORM'], 'localhost');
        self::$client->getEmitter()->attach(new Cookie(self::$cookies));

    }

    /**
     * @return string
     */
    public static function getWWWPath () {

        return 'api/core/www/run.php';

    }

    /**
     * @param string     $service
     * @param string     $method
     * @param array      $params
     * @param string[]   $fields
     * @param string     $auth
     * @param string|int $id
     * @param string     $clientName
     * @param string     $clientVersion
     * @param string     $clientLang
     *
     * @return mixed
     */
    public function post ($service, $method, array $params = [], array $fields = [], $auth = NULL, $id = NULL,
                          $clientName = NULL, $clientVersion = NULL, $clientLang = NULL) {

        if (!is_string($clientName)) {
            $clientName = 'archipad-cloud';
        }

        if (!is_string($clientVersion)) {
            $clientVersion = '1.0';
        }

        if (!is_string($clientLang)) {
            $clientLang = 'fr';
        }

        $url = "{$clientName}+{$clientVersion}+{$clientLang}/";
        if (isset($service)) {
            $url .= urlencode($service) . '/';
        }

        $postData = ['jsonrpc' => '2.0',
                     'method'  => $method
        ];
        if (!is_null($id)) {
            $postData['id'] = $id;
        }
        if (count($params)) {
            $postData['params'] = $params;
        }
        if (count($fields)) {
            $postData['fields'] = $fields;
        }

        if (is_string($auth)) {
            self::$cookies->setCookie(new SetCookie([
                                                        'Domain'  => 'localhost',
                                                        'Name'    => 'authToken',
                                                        'Value'   => $auth,
                                                        'Discard' => true
                                                    ]));
        }

        self::$lastRequest = self::$client->post($url, ['json' => $postData, 'cookies' => self::$cookies]);

        try {
            return self::$lastRequest->json(['object' => false, /*'big_int_strings' => true*/]);
        }
        catch (\Exception $e) {
            self::fail('mal formated response to the request : ' . self::$lastRequest->getEffectiveUrl() . "\n"
                       . self::$lastRequest->getBody());
        }

    }

    /**
     * @param mixed       $response
     * @param string|null id
     * @param bool        $hasMoreProperties
     */
    public function assertSuccess ($response, $id = NULL, $hasMoreProperties = false) {

        $this->assertInternalType('array', $response, json_encode($response));

        if ($hasMoreProperties) {
            $this->assertGreaterThan(3, count($response), json_encode($response));
        }
        else {
            $this->assertCount(3, $response, json_encode($response));
        }
        $this->assertArrayHasKey('jsonrpc', $response, json_encode($response));
        $this->assertArrayHasKey('id', $response, json_encode($response));
        $this->assertArrayHasKey('result', $response, json_encode($response));
        $this->assertArrayNotHasKey('error', $response, json_encode($response));
        $this->assertSame('2.0', $response['jsonrpc'], json_encode($response));
        $this->assertSame($id, $response['id'], json_encode($response));

    }

    /**
     * @param mixed       $response
     * @param int         $errorCode
     * @param null|string $id
     * @param array       $childErrorCodes
     * @param string      $field
     * @param bool        $hasMoreProperties
     */
    public function assertFail ($response, $errorCode = NULL, $id = NULL, array $childErrorCodes = [], $field = NULL,
                                $hasMoreProperties = false) {

        $this->assertInternalType('array', $response);

        if ($hasMoreProperties) {
            $this->assertGreaterThan(3, count($response), json_encode($response));
        }
        else {
            $this->assertCount(3, $response, json_encode($response));
        }

        $this->assertArrayNotHasKey('result', $response, json_encode($response));
        $this->assertSame('2.0', $response['jsonrpc'], json_encode($response));
        $this->assertSame($id, $response['id'], json_encode($response));

        $this->assertArrayHasKey('error', $response, json_encode($response));
        $error = $response['error'];
        $this->assertInternalType('array', $error, json_encode($response));
        $this->assertArrayHasKey('code', $error, json_encode($response));

        if (!is_null($errorCode)) {
            $this->assertEquals($errorCode, $error['code'], json_encode($response));
        }

        if (count($childErrorCodes)) {
            $this->assertArrayHasKey('errors', $error, json_encode($response));
            $errors = $error['errors'];
            $this->assertInternalType('array', $errors, json_encode($response));
            $this->assertRecursiveErrorCodes($errors, $childErrorCodes);
        }
        else {
            $this->assertArrayNotHasKey('errors', $error);
        }

        if (is_null($field)) {
            $this->assertArrayNotHasKey('field', $error);
        }
        else {
            $this->assertArrayHasKey('field', $error);
            $this->assertEquals($field, $error['field']);
        }

    }

    /**
     * @param array       $errors
     * @param array       $errorCodes
     * @param string|null $message
     */
    protected function assertRecursiveErrorCodes (array $errors, array $errorCodes, $message = NULL) {

        $this->assertCount(count($errors), $errorCodes, $message);

        $reformattedErrors = [];
        foreach ($errors as $error) {
            $this->assertInternalType('array', $error, $message);
            $this->assertArrayHasKey('code', $error, $message);
            $code = $error['code'];
            if (isset($reformattedErrors[$code])) {
                $this->fail("Duplicated error code '{$code}' in childErrors");
            }
            $reformattedErrors[$code] = $error;
        }

        foreach ($errorCodes as $key => $value) {
            $errorCode = is_array($value) ? $key : $value;
            $this->assertArrayHasKey($errorCode, $reformattedErrors, $message);
            if (is_array($value)) { // if child errors
                $reformattedError = $reformattedErrors[$errorCode];
                $this->assertArrayHasKey('errors', $reformattedError, $message);
                $childErrors = $reformattedError['errors'];
                $this->assertInternalType('array', $childErrors, $message);
                $this->assertRecursiveErrorCodes($childErrors, $value, $message);
            }
        }

    }

} 
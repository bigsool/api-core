<?php


namespace Core\Functional;


use Doctrine\DBAL\Connection;
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

    public static function setUpBeforeClass () {

        parent::setUpBeforeClass();

        self::resetDatabase();

        self::createClient();

    }

    public static function resetDatabase () {

        require static::getRootFolder() . '/doctrine/config.php';

        /**
         * @var array         $conn
         * @var EntityManager $entityManager
         */

        $schemaTool = new SchemaTool($entityManager);
        $entityManager->getConnection()->query('SET FOREIGN_KEY_CHECKS=0');
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
        $entityManager->getConnection()->query('SET FOREIGN_KEY_CHECKS=1');

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
        ];
        self::$client = new Client($config);
        self::$cookies = CookieJar::fromArray(['XDEBUG_SESSION' => 'PHPSTORM'], 'localhost');
        self::$client->getEmitter()->attach(new Cookie(self::$cookies));

    }

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

        $this->assertInternalType('array', $response);

        if ($hasMoreProperties) {
            $this->assertGreaterThan(3, count($response));
        }
        else {
            $this->assertCount(3, $response);
        }
        $this->assertArrayHasKey('jsonrpc', $response);
        $this->assertArrayHasKey('id', $response);
        $this->assertArrayHasKey('result', $response);
        $this->assertArrayNotHasKey('error', $response);
        $this->assertSame('2.0', $response['jsonrpc']);
        $this->assertSame($id, $response['id']);
        $this->assertInternalType('array', $response['result']);
        $this->assertArrayHasKey('success', $response['result']);
        $this->assertArrayHasKey('data', $response['result']);
        $this->assertTrue($response['result']['success']);

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
            $this->assertGreaterThan(3, count($result));
        }
        else {
            $this->assertCount(3, $result);
        }

        $this->assertArrayNotHasKey('result', $result);
        $this->assertSame('2.0', $result['jsonrpc']);
        $this->assertSame($id, $result['id']);

        $this->assertArrayHasKey('error', $result);
        $error = $result['error'];
        $this->assertInternalType('array', $error);

        if (is_null($errorCode)) {
            $this->assertArrayNotHasKey('code', $error);
        }
        else {
            $this->assertArrayHasKey('code', $error);
            $this->assertEquals($errorCode, $error['code']);
        }

        if (count($childErrorCodes)) {
            $this->assertArrayHasKey('errors', $error);
            $errors = $error['errors'];
            $this->assertInternalType('array', $errors);
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
     * @param array $errors
     * @param array $errorCodes
     */
    protected function assertRecursiveErrorCodes (array $errors, array $errorCodes) {

        $this->assertCount(count($errors), $errorCodes);

        $reformattedErrors = [];
        foreach ($errors as $error) {
            $this->assertInternalType('array', $error);
            $this->assertArrayHasKey('code', $error);
            $code = $error['code'];
            if (isset($reformattedErrors[$code])) {
                $this->fail("Duplicated error code '{$code}' in childErrors");
            }
            $reformattedErrors[$code] = $error;
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
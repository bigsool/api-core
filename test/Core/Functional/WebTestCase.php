<?php


namespace Core\Functional;


use Core\Context\ApplicationContext;
use Doctrine\DBAL\Driver\PDOSqlite\Driver as SqliteDriver;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use PHPUnit\Framework\TestCase;

abstract class WebTestCase extends TestCase {

    /**
     * @var EntityManager
     */
    protected static $archiwebEntityManager;

    /**
     * @var EntityManager
     */
    protected static $patchesEntityManager;

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
     * @var array
     */
    protected static $config;

    public static function getConfig() {
        if (self::$config === null) {
            self::$config = ApplicationContext::getInstance()->getConfigManager()->getConfig();
        }

        return self::$config;
    }

    /**
     *
     */
    public static function setUpBeforeClass () {

        echo "\n".get_called_class();

        parent::setUpBeforeClass();

        self::resetDatabase();

        self::createClient();

    }

    protected function setUp () {
        parent::setUp();
        self::createClient(); // reset connection status
    }


    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public static function resetDatabase () {

        if (!isset(self::$archiwebEntityManager)) {

            require static::getRootFolder() . '/doctrine/config.php';

            /**
             * @var EntityManager $entityManager
             */

            self::$archiwebEntityManager = $entityManager;

        }

        if (!isset(self::$patchesEntityManager)) {

            require static::getRootFolder() . '/its/config.php';

            /**
             * @var EntityManager $entityManager
             */

            self::$patchesEntityManager = $entityManager;

        }

        $archiwebConn = self::$archiwebEntityManager->getConnection();
        $patchesConn = self::$patchesEntityManager->getConnection();

        echo "\nDropping databases\n";

        $archiwebConn->exec(sprintf('DROP DATABASE %1$s; CREATE DATABASE %1$s; USE %1$s;',
                                    self::getConfig()['db']['dbname']));
        $patchesConn->exec(sprintf('DROP DATABASE %1$s; CREATE DATABASE %1$s; USE %1$s;',
                                   self::getConfig()['patchDb']['dbname']));

        echo "Dropped databases\n";

        $dirs = [
            static::getArchiwebRootFolder() . '/doctrine/',
            realpath(static::getRootFolder() . '/its/')
        ];

        foreach ($dirs as $dir) {
            echo "Executing migrations at '$dir'\n";
            chdir($dir);
            passthru('php doctrine.php m:m -n -q 1>/dev/null 2>&1');
        }

    }

    /**
     * @return string
     */
    protected static function getRootFolder () {

        return realpath(__DIR__ . '/../../..');

    }

    /**
     * @return string
     */
    protected static function getArchiwebRootFolder() {

        return realpath(
            static::getRootFolder().'/'.self::getConfig()['v1']['path']
            ?? static::getRootFolder() . '/../../archiweb'
        );

    }

    /**
     *
     */
    protected static function createClient () {

        self::$cookies = CookieJar::fromArray(['XDEBUG_SESSION' => 'PHPSTORM'], 'localhost');

        $baseURL = ApplicationContext::getInstance()->getConfigManager()->getConfig()['APIBaseURL'];

        $config = [
            'base_uri' => "{$baseURL}/jsonrpc/",
            'headers'  => [
                'User-Agent' => ApplicationContext::UNIT_TESTS_USER_ARGENT
            ],
            'cookies'  => self::$cookies
        ];
        self::$client = new Client($config);

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
            $clientName = 'architestcloud';
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
            return json_decode(self::$lastRequest->getBody(), true);
        }
        catch (\Exception $e) {
            self::fail('mal formated response to the request : ' . $url . "\n" . self::$lastRequest->getBody());
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

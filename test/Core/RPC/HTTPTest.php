<?php


namespace Core\RPC;


use Core\Context\ApplicationContext;
use Core\Context\RequestContext;
use Core\Serializer;
use Core\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HTTPTest extends TestCase {

    public static function setUpBeforeClass () {

        self::getApplicationContext();

    }

    public function testParse () {

        /**
         * @var Request $req
         */
        $req = $this->getMock('\Symfony\Component\HttpFoundation\Request', ['getPathInfo', 'getClientIp']);
        $req->method('getPathInfo')->willReturn('/protocol/client+version+locale/service/method/');
        $req->method('getClientIp')->willReturn('10.0.1.104');
        $params = ['param1' => 'value1', 'param2'];
        $req->query->add($params);

        $HTTP = new HTTP();
        $HTTP->parse($req);

        $this->assertSame('/service/method', $HTTP->getPath());
        $this->assertSame($params, $HTTP->getParams());
        $this->assertSame('client', $HTTP->getClientName());
        $this->assertSame('version', $HTTP->getClientVersion());
        $this->assertSame('en', $HTTP->getLocale());
        $this->assertSame([], $HTTP->getReturnedFields());
        $this->assertSame('service', $HTTP->getService());
        $this->assertSame('method', $HTTP->getMethod());
        $this->assertSame('method', $HTTP->getMethod());
        $this->assertSame('10.0.1.104', $HTTP->getIpAddress());

        $response = $HTTP->getSuccessResponse(new Serializer(new RequestContext()), 'qwe');

        $this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response);

    }

    /**
     * @expectedException \Exception
     */
    public function testClientNotFound () {

        /**
         * @var Request $req
         */
        $req = $this->getMock('\Symfony\Component\HttpFoundation\Request', ['getPathInfo']);
        $req->method('getPathInfo')->willReturn('/protocol/');
        (new HTTP())->parse($req);

    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidClient () {

        /**
         * @var Request $req
         */
        $req = $this->getMock('\Symfony\Component\HttpFoundation\Request', ['getPathInfo']);
        $req->method('getPathInfo')->willReturn('/protocol/clientversion+locale/');
        (new HTTP())->parse($req);

    }

    /**
     * @expectedException \Exception
     */
    public function testServiceNotFound () {

        /**
         * @var Request $req
         */
        $req = $this->getMock('\Symfony\Component\HttpFoundation\Request', ['getPathInfo']);
        $req->method('getPathInfo')->willReturn('/protocol/client+version+locale/');
        (new HTTP())->parse($req);

    }

    /**
     * @expectedException \Exception
     */
    public function testMethodNotFound () {

        /**
         * @var Request $req
         */
        $req = $this->getMock('\Symfony\Component\HttpFoundation\Request', ['getPathInfo']);
        $req->method('getPathInfo')->willReturn('/protocol/client+version+locale/service');
        (new HTTP())->parse($req);

    }

    public function testGetErrorResponse () {

        $error = ApplicationContext::getInstance()->getErrorManager()->getFormattedError(ERROR_PERMISSION_DENIED);
        $response = (new HTTP())->getErrorResponse($error);
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertSame(strval($error), $response->getContent());

    }
} 
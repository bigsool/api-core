<?php


namespace Core\RPC;


use Core\Context\RequestContext;
use Core\Serializer;
use Core\TestCase;
use Symfony\Component\HttpFoundation\Request;

class JSONTest extends TestCase {

    public static function setUpBeforeClass () {

        self::getApplicationContext();

    }

    public function testParse () {

        /**
         * @var Request $req
         */
        $req = $this->getMock('\Symfony\Component\HttpFoundation\Request', ['getPathInfo','getClientIp']);
        $req->method('getPathInfo')->willReturn('/protocol/client+version+locale/service/');
        $req->method('getClientIp')->willReturn('10.0.1.104');
        $params = ['param1' => 'value1', 'param2'];
        $req->query->add(['method' => 'method', 'params' => $params]);
        $JSON = new JSON();
        $JSON->parse($req);

        $this->assertSame('/service/method', $JSON->getPath());
        $this->assertSame($params, $JSON->getParams());
        $this->assertSame('client', $JSON->getClientName());
        $this->assertSame('version', $JSON->getClientVersion());
        $this->assertSame('en', $JSON->getLocale());
        $this->assertNull($JSON->getReturnedRootEntity());
        $this->assertSame([], $JSON->getReturnedFields());
        $this->assertSame('service', $JSON->getService());
        $this->assertSame('method', $JSON->getMethod());
        $this->assertSame('10.0.1.104', $JSON->getIpAddress());

        $req = $this->getMock('\Symfony\Component\HttpFoundation\Request', ['getPathInfo']);
        $req->method('getPathInfo')->willReturn('/protocol/client+version+fr/service/');
        $params = [];
        $req->query->add(['method' => 'method',
                          'entity' => ($entity = 'entity'),
                          'fields' => ($fields = ['field1', 'field2.subField1'])
                         ]);
        $JSON = new JSON();
        $JSON->parse($req);

        $this->assertSame('/service/method', $JSON->getPath());
        $this->assertSame($params, $JSON->getParams());
        $this->assertSame('client', $JSON->getClientName());
        $this->assertSame('version', $JSON->getClientVersion());
        $this->assertSame('fr', $JSON->getLocale());
        $this->assertSame($entity, $JSON->getReturnedRootEntity());
        $this->assertSame($fields, $JSON->getReturnedFields());
        $this->assertSame('service', $JSON->getService());
        $this->assertSame('method', $JSON->getMethod());

        $response = $JSON->getSuccessResponse(new Serializer(new RequestContext()), 'qwe');
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
        (new JSON())->parse($req);

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
        (new JSON())->parse($req);

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
        (new JSON())->parse($req);

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
        (new JSON())->parse($req);

    }

} 
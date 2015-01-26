<?php


namespace Core\RPC;


use Core\Context\RequestContext;
use Core\Serializer;
use Core\TestCase;
use Symfony\Component\HttpFoundation\Request;

class JSONTest extends TestCase {

    public function testParse () {

        /**
         * @var Request $req
         */
        $req = $this->getMock('\Symfony\Component\HttpFoundation\Request', ['getPathInfo']);
        $req->method('getPathInfo')->willReturn('/protocol/client+version+locale/service/');
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

    /**
     * @expectedException \Exception
     */
    public function testStarFieldAsKeyPath () {

        /**
         * @var Request $req
         */
        $req = $this->getMock('\Symfony\Component\HttpFoundation\Request', ['getPathInfo']);
        $req->method('getPathInfo')->willReturn('/protocol/client+version+locale/service');
        $req->query->add(['method' => 'method']);
        $req->query->add(['entity' => 'entity']);
        $req->query->add(['fields' => ['*']]);
        (new JSON())->parse($req);

    }

} 
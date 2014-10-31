<?php


namespace Archiweb\RPC;


use Archiweb\TestCase;
use Symfony\Component\HttpFoundation\Request;

class JSONPTest extends TestCase {

    public function testConstructor () {

        /**
         * @var Request $req
         */
        $req = $this->getMock('\Symfony\Component\HttpFoundation\Request', ['getPathInfo']);
        $req->method('getPathInfo')->willReturn('/protocol/client+version+locale/service/');
        $params = ['param1' => 'value1', 'param2'];
        $req->query->add(['method' => 'method', 'params' => $params]);
        $JSONP = new JSONP($req);

        $this->assertSame('/service/method', $JSONP->getPath());
        $this->assertSame($params, $JSONP->getParams());
        $this->assertSame('client', $JSONP->getClientName());
        $this->assertSame('version', $JSONP->getClientVersion());
        $this->assertSame('en', $JSONP->getLocale());


        $req = $this->getMock('\Symfony\Component\HttpFoundation\Request', ['getPathInfo']);
        $req->method('getPathInfo')->willReturn('/protocol/client+version+fr/service/');
        $params = [];
        $req->query->add(['method' => 'method']);
        $req->query->add(['entity' => ($entity = 'entity')]);
        $req->query->add(['fields' => ($fields = ['field1','field2.subField1'])]);
        $JSONP = new JSONP($req);

        $this->assertSame('/service/method', $JSONP->getPath());
        $this->assertSame($params, $JSONP->getParams());
        $this->assertSame('client', $JSONP->getClientName());
        $this->assertSame('version', $JSONP->getClientVersion());
        $this->assertSame('fr', $JSONP->getLocale());
        $this->assertSame($entity, $JSONP->getReturnedRootEntity());
        $this->assertSame($fields, $JSONP->getReturnedFields());

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
        new JSONP($req);

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
        new JSONP($req);

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
        new JSONP($req);

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
        new JSONP($req);

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
        new JSONP($req);

    }

} 
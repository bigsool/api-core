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
        $JSONP = new JSONP($req);

        $this->assertSame('/service/method', $JSONP->getPath());
        $this->assertSame($params, $JSONP->getParams());
        $this->assertSame('client', $JSONP->getClientName());
        $this->assertSame('version', $JSONP->getClientVersion());
        $this->assertSame('fr', $JSONP->getLocale());

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

} 
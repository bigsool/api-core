<?php


namespace Core\RPC;


use Core\Context\ActionContext;
use Core\Context\RequestContext;
use Core\Error\Error;
use Core\Error\FormattedError;
use Core\Serializer;
use Core\TestCase;
use Symfony\Component\HttpFoundation\Request;

class JSONPTest extends TestCase {

    public static function setUpBeforeClass () {

        self::getApplicationContext();

    }

    public function testParse () {

        /**
         * @var Request $req
         */
        $req = $this->getMock('\Symfony\Component\HttpFoundation\Request', ['getPathInfo', 'getClientIp']);
        $req->method('getPathInfo')->willReturn('/protocol/client+version+locale/service/');
        $req->method('getClientIp')->willReturn('10.0.1.104');
        $params = ['param1' => 'value1', 'param2'];
        $req->query->add(['method' => 'method', 'params' => json_encode($params)]);
        $JSONP = new JSONP();
        $JSONP->parse($req);

        $this->assertSame('/service/method', $JSONP->getPath());
        $this->assertSame($params, $JSONP->getParams());
        $this->assertSame('client', $JSONP->getClientName());
        $this->assertSame('version', $JSONP->getClientVersion());
        $this->assertSame('en', $JSONP->getLocale());
        $this->assertSame([], $JSONP->getReturnedFields());
        $this->assertSame('service', $JSONP->getService());
        $this->assertSame('method', $JSONP->getMethod());
        $this->assertSame('10.0.1.104', $JSONP->getIpAddress());

        $req = $this->getMock('\Symfony\Component\HttpFoundation\Request', ['getPathInfo']);
        $req->method('getPathInfo')->willReturn('/protocol/client+version+fr/service/');
        $params = [];
        $req->query->add(['method' => 'method',
                          'entity' => ($entity = 'entity'),
                          'fields' => ($fields = ['field1', 'field2.subField1'])
                         ]);
        $JSONP = new JSONP();
        $JSONP->parse($req);

        $this->assertSame('/service/method', $JSONP->getPath());
        $this->assertSame($params, $JSONP->getParams());
        $this->assertSame('client', $JSONP->getClientName());
        $this->assertSame('version', $JSONP->getClientVersion());
        $this->assertSame('fr', $JSONP->getLocale());
        $this->assertSame($fields, $JSONP->getReturnedFields());
        $this->assertSame('service', $JSONP->getService());
        $this->assertSame('method', $JSONP->getMethod());

        $response = $JSONP->getSuccessResponse(new Serializer(new ActionContext(new RequestContext())), 'qwe');
        $this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response);

        $response = $JSONP->getErrorResponse(new FormattedError(new Error(1, '', '')));
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
        (new JSONP())->parse($req);

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
        (new JSONP())->parse($req);

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
        (new JSONP())->parse($req);

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
        (new JSONP())->parse($req);

    }

} 
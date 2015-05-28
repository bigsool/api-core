<?php

namespace Core\Context;


use Core\Field\RelativeField;
use Core\TestCase;
use Symfony\Component\HttpFoundation\Response;

class RequestContextTest extends TestCase {

    /**
     *
     */
    public function testParams () {

        $ctx = $this->getRequestContext();

        $array = ['a', 'b' => 2, ['c']];

        $ctx->setParams($array);

        $this->assertSame($array, $ctx->getParams());
        $this->assertSame($array[0], $ctx->getParam(0));
        $this->assertSame($array['b'], $ctx->getParam('b'));
        $this->assertSame(NULL, $ctx->getParam('qwe'));

    }

    public function testResponse () {

        $ctx = $this->getRequestContext();

        $response = new Response();

        $ctx->setResponse($response);
        $this->assertSame($response, $ctx->getResponse());

    }

    public function testLocale () {

        $ctx = $this->getRequestContext();
        $locale = 'fr';
        $ctx->setLocale($locale);
        $this->assertSame($locale, $ctx->getLocale());

    }

    public function testClientVersion () {

        $ctx = $this->getRequestContext();
        $version = '1.2.3';
        $ctx->setClientVersion($version);
        $this->assertSame($version, $ctx->getClientVersion());

    }

    public function testFilter () {

        $ctx = $this->getRequestContext();
        $filter = $this->getMockFilter();
        $ctx->setFilter($filter);
        $this->assertSame($filter, $ctx->getFilter());

    }

    public function testClientName () {

        $ctx = $this->getRequestContext();
        $name = 'archipad';
        $ctx->setClientName($name);
        $this->assertSame($name, $ctx->getClientName());

    }

    public function testIpAddress () {

        $ctx = $this->getRequestContext();
        $ip = '10.0.1.123';
        $ctx->setIpAddress($ip);
        $this->assertSame($ip, $ctx->getIpAddress());

    }

    public function testKeyPaths () {

        $ctx = $this->getRequestContext();
        $keyPaths = [new RelativeField('qwe'), new RelativeField('aze')];
        $ctx->setReturnedFields($keyPaths);
        $this->assertSame($keyPaths, $ctx->getReturnedFields());

    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidKeyPathsType () {

        $this->getRequestContext()->setReturnedFields([new \stdClass()]);

    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidKeyPathsValue () {

        $this->getRequestContext()->setReturnedFields(['*']);

    }

} 
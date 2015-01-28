<?php

namespace Core\Context;


use Core\Parameter\UnsafeParameter;
use Core\TestCase;

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

    public function testGetNewActionContextWithoutParameter () {

        $ctx = $this->getRequestContext();
        $actionContext = $ctx->getNewActionContext();
        $expected = new ActionContext($ctx);

        $this->assertEquals($expected, $actionContext);

    }

    public function testGetNewActionContextWithParameters () {

        $ctx = $this->getRequestContext();

        $params = ['a', 'b' => 1, ['c'], new \stdClass()];
        $expectedParams = [];
        foreach ($params as $key => $param) {
            $expectedParams[$key] = new UnsafeParameter($param);
        }

        $ctx->setParams($params);
        $actionContext = $ctx->getNewActionContext();

        $this->assertCount(count($expectedParams), $actionContext->getParams());
        foreach ($expectedParams as $key => $value) {
            $this->assertArrayHasKey($key, $actionContext->getParams());
            $this->assertSame($actionContext->getParams()[$key], $actionContext->getParam($key));
            $this->assertInstanceOf('\Core\Parameter\UnsafeParameter', $actionContext->getParam($key));
            $this->assertFalse($actionContext->getParam($key)->isSafe());
            $this->assertSame($value->getValue(), $actionContext->getParam($key)->getValue());
        }

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

    public function testRootEntity () {

        $ctx = $this->getRequestContext();
        $entity = 'TestCompany';
        $ctx->setReturnedRootEntity($entity);
        $this->assertSame($entity, $ctx->getReturnedRootEntity());

    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidRootEntityType () {

        $this->getRequestContext()->setReturnedRootEntity(new \stdClass());

    }

    public function testKeyPaths () {

        $ctx = $this->getRequestContext();
        $keyPaths = [$this->getMockFieldKeyPath(), $this->getMockFieldKeyPath()];
        $ctx->setReturnedKeyPaths($keyPaths);
        $this->assertSame($keyPaths, $ctx->getReturnedKeyPaths());

    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidKeyPathsType () {

        $this->getRequestContext()->setReturnedKeyPaths([new \stdClass()]);

    }

} 
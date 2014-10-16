<?php


namespace Archiweb\Error;

use Archiweb\TestCase;

class ErrorManagerTest extends TestCase {

    /**
     * @var Error
     */
    protected $error1;

    /**
     * @var Error
     */
    protected $error10;

    /**
     * @var Error
     */
    protected $error100;

    /**
     * @var Error
     */
    protected $error101;

    /**
     * @var Error
     */
    protected $error1000;

    /**
     * @var Error
     */
    protected $error11;

    /**
     * @var ErrorManager
     */
    protected $errorManager;

    public function setUp () {

        self::resetApplicationContext();
        $this->errorManager = self::getApplicationContext()->getErrorManager();

        @define('__TEST__ERR_1', rand());
        $this->errorManager->defineError(
            $this->error1 = new Error(__TEST__ERR_1, 'message fr 1', 'message en 1', NULL, 'field1'));

        @define('__TEST__ERR_10', rand());
        $this->errorManager->defineError(
            $this->error10 = new Error(__TEST__ERR_10, 'message fr 10', 'message en 10', __TEST__ERR_1, 'field10'));

        @define('__TEST__ERR_100', rand());
        $this->errorManager->defineError(
            $this->error100 =
                new Error(__TEST__ERR_100, 'message fr 100', 'message en 100', __TEST__ERR_10, 'field100'));
        @define('__TEST__ERR_101', rand());
        $this->errorManager->defineError(
            $this->error101 =
                new Error(__TEST__ERR_101, 'message fr 101', 'message en 101', __TEST__ERR_10, 'field101'));

        @define('__TEST__ERR_1000', rand());
        $this->errorManager->defineError(
            $this->error1000 =
                new Error(__TEST__ERR_1000, 'message fr 1000', 'message en 1000', __TEST__ERR_101, 'field1000'));

        @define('__TEST__ERR_11', rand());
        $this->errorManager->defineError(
            $this->error11 = new Error(__TEST__ERR_11, 'message fr 11', 'message en 11', __TEST__ERR_1, 'field300'));

    }

    public function testAddError () {

        $errorManager = $this->getMockErrorManager();
        $error = new Error(rand(), '', '');
        $errorManager->defineError($error);
        $this->assertCount(0, $errorManager->getErrors());

        $errorManager->addError($error->getCode());
        $errors = $errorManager->getErrors();
        $this->assertCount(1, $errors);
        $this->assertContains($error, $errors);

    }

    /**
     * @expectedException \Exception
     */
    public function testAddDefinedErrorWithSameError () {

        $this->errorManager->defineError($this->error1);

    }

    public function testGetDefinedError () {

        $definedError = $this->errorManager->getDefinedError($this->error1->getCode());
        $this->assertSame($this->error1, $definedError);

    }

    public function testGetDefinedErrorWithBadErrorCode () {

        $error = $this->errorManager->getDefinedError(99999999);
        $this->assertEquals(NULL, $error);

    }

    /**
     * @expectedException \Exception
     */
    public function testGetErrorForErrorCode () {

        $meth = new \ReflectionMethod($this->errorManager, 'getErrorForErrorCode');
        $meth->setAccessible(true);
        $meth->invokeArgs($this->errorManager, [99999999]);

    }

    public function testGetFormattedErrorWithError () {

        $formattedError = $this->errorManager->getFormattedError($this->error1000->getCode());
        $this->assertEquals($formattedError->getCode(), __TEST__ERR_1);
        $this->assertCount(1, $formattedError->getChildErrors());

        $formattedChildErrors = $formattedError->getChildErrors();

        $this->assertEquals($formattedChildErrors[0]->getCode(), __TEST__ERR_10);

        $formattedChildErrors = $formattedChildErrors[0]->getChildErrors();

        $this->assertEquals($formattedChildErrors[0]->getCode(), __TEST__ERR_101);
        $this->assertCount(1, $formattedChildErrors);

        $formattedChildErrors = $formattedChildErrors[0]->getChildErrors();

        $this->assertEquals($formattedChildErrors[0]->getCode(), __TEST__ERR_1000);
        $this->assertCount(1, $formattedChildErrors);

    }

    public function testGetFormattedError () {

        $this->errorManager->addError($this->error1000->getCode());
        $formattedError = $this->errorManager->getFormattedError();
        $this->assertEquals($formattedError->getCode(), __TEST__ERR_1);
        $this->assertCount(1, $formattedError->getChildErrors());

        $this->errorManager->addError($this->error11->getCode());
        $formattedError = $this->errorManager->getFormattedError();
        $this->assertEquals($formattedError->getCode(), __TEST__ERR_1);
        $this->assertCount(2, $formattedError->getChildErrors());

        $formattedChildErrors = $formattedError->getChildErrors();

        $this->assertEquals($formattedChildErrors[0]->getCode(), __TEST__ERR_10);
        $this->assertEquals($formattedChildErrors[1]->getCode(), __TEST__ERR_11);

        $formattedChildErrors = $formattedChildErrors[0]->getChildErrors();

        $this->assertEquals($formattedChildErrors[0]->getCode(), __TEST__ERR_101);
        $this->assertCount(1, $formattedChildErrors);

        $formattedChildErrors = $formattedChildErrors[0]->getChildErrors();

        $this->assertEquals($formattedChildErrors[0]->getCode(), __TEST__ERR_1000);
        $this->assertCount(1, $formattedChildErrors);

        $this->errorManager->addError($this->error1->getCode(), "fieldModified");
        $formattedError = $this->errorManager->getFormattedError();
        $this->assertEquals($formattedError->getField(), "fieldModified");

    }

}
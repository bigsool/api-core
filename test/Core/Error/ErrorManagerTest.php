<?php


namespace Core\Error;

use Core\TestCase;

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

        defined('__TEST__ERR_1') || define('__TEST__ERR_1', rand());
        $this->errorManager->defineError(
            $this->error1 = new LocalizedError(__TEST__ERR_1, 'message fr 1', 'message en 1', NULL, 'field1'));

        defined('__TEST__ERR_10') || define('__TEST__ERR_10', rand());
        $this->errorManager->defineError(
            $this->error10 =
                new LocalizedError(__TEST__ERR_10, 'message fr 10', 'message en 10', __TEST__ERR_1, 'field10'));

        defined('__TEST__ERR_100') || define('__TEST__ERR_100', rand());
        $this->errorManager->defineError(
            $this->error100 =
                new LocalizedError(__TEST__ERR_100, 'message fr 100', 'message en 100', __TEST__ERR_10, 'field100'));
        defined('__TEST__ERR_101') || define('__TEST__ERR_101', rand());
        $this->errorManager->defineError(
            $this->error101 =
                new LocalizedError(__TEST__ERR_101, 'message fr 101', 'message en 101', __TEST__ERR_10, 'field101'));

        defined('__TEST__ERR_1000') || define('__TEST__ERR_1000', rand());
        $this->errorManager->defineError(
            $this->error1000 =
                new LocalizedError(__TEST__ERR_1000, 'message fr 1000', 'message en 1000', __TEST__ERR_101,
                                   'field1000'));

        defined('__TEST__ERR_11') || define('__TEST__ERR_11', rand());
        $this->errorManager->defineError(
            $this->error11 =
                new LocalizedError(__TEST__ERR_11, 'message fr 11', 'message en 11', __TEST__ERR_1, 'field300'));

    }

    public function testAddError () {

        $errorManager = $this->getMockErrorManager();
        $error = new Error(rand(), '');
        $errorManager->defineError($error);
        $this->assertCount(0, $errorManager->getErrors());

        $errorManager->addError($error->getCode());
        $errors = $errorManager->getErrors();
        $this->assertCount(1, $errors);
        $this->assertEquals($error, $errors[0]);

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

        $field = 'field';
        $formattedError = $this->errorManager->getFormattedError($this->error1000->getCode(), $field);
        $this->assertEquals($formattedError->getCode(), __TEST__ERR_1);
        $this->assertCount(1, $formattedError->getChildErrors());
        $this->assertSame($field,
                          $formattedError->getChildErrors()[0]->getChildErrors()[0]->getChildErrors()[0]->getField());

        $formattedChildErrors = $formattedError->getChildErrors();

        $this->assertEquals($formattedChildErrors[0]->getCode(), __TEST__ERR_10);

        $formattedChildErrors = $formattedChildErrors[0]->getChildErrors();

        $this->assertEquals($formattedChildErrors[0]->getCode(), __TEST__ERR_101);
        $this->assertCount(1, $formattedChildErrors);

        $formattedChildErrors = $formattedChildErrors[0]->getChildErrors();

        $this->assertEquals($formattedChildErrors[0]->getCode(), __TEST__ERR_1000);
        $this->assertCount(1, $formattedChildErrors);

    }

    /**
     * @expectedException \Exception
     */
    public function testGetFormattedErrorWithDifferentParents () {

        $this->errorManager->addError($this->error1000->getCode());
        $this->errorManager->addError($this->error1000->getParentCode());

        $this->errorManager->getFormattedError();

    }

    public function testGetFormattedError () {

        $this->errorManager->addError($this->error100->getCode());
        $formattedError = $this->errorManager->getFormattedError();
        $this->assertEquals($formattedError->getCode(), __TEST__ERR_1);
        $this->assertCount(1, $formattedError->getChildErrors());

        $this->errorManager->addError($this->error101->getCode(), "fieldModified");
        $formattedError = $this->errorManager->getFormattedError();
        $this->assertEquals($formattedError->getCode(), __TEST__ERR_1);
        $this->assertCount(1, $formattedError->getChildErrors());

        $formattedChildErrors = $formattedError->getChildErrors();

        $this->assertEquals($formattedChildErrors[0]->getCode(), __TEST__ERR_10);
        $this->assertCount(2, $formattedChildErrors[0]->getChildErrors());

        $formattedChildErrors = $formattedChildErrors[0]->getChildErrors();

        $this->assertEquals($formattedChildErrors[0]->getCode(), __TEST__ERR_100);
        $this->assertEquals($formattedChildErrors[1]->getCode(), __TEST__ERR_101);
        $this->assertEquals($formattedChildErrors[1]->getField(), "fieldModified");

    }

}
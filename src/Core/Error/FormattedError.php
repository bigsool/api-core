<?php


namespace Core\Error;


class FormattedError extends \Exception {

    /**
     * @var int
     */
    protected $code;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var string
     */
    protected $localizedMessage;

    /**
     * @var string
     */
    protected $field;

    /**
     * @var FormattedError[]
     */
    protected $childErrors = [];

    /**
     * @param Error|array $error
     * @param string|null $field
     */
    public function __construct ($error, $field = NULL) {

        if ($error instanceof Error) {
            $this->buildWithError($error, $field);
        }
        elseif (is_array($error)) {
            $this->buildWithArray($error, $field);
        }
        else {
            throw new \RuntimeException('unexpected error type');
        }

    }

    /**
     * @param Error       $error
     * @param string|null $field
     */
    protected function buildWithError (Error $error, $field) {

        $this->code = $error->getCode();
        $this->message = $error->getMessage();
        $this->localizedMessage = $error->getLocalizedMessage();
        $this->field = $field ? $field : $error->getField();

    }

    /**
     * @param array       $error
     * @param string|null $field
     */
    protected function buildWithArray (array $error, $field) {

        if (!isset($error['code'])) {
            throw new \RuntimeException('code not found in the error array');
        }
        $this->code = $error['code'];

        if (isset($error['message'])) {
            $this->message = $error['message'];
        }

        if (!empty($error['localizedMessage'])) {
            $this->localizedMessage = $error['localizedMessage'];
        }

        if (!$this->message) {
            throw new \RuntimeException('message not found in the error array');
        }

        $this->field = isset($error['field']) ? $error['field'] : $field;

        if (isset($error['errors']) && is_array($error['errors'])) {
            foreach ($error['errors'] as $errorArray) {
                $this->addChildError(new FormattedError($errorArray));
            }
        }

    }

    /**
     * @param FormattedError $childError
     */
    public function addChildError (FormattedError $childError) {

        $this->childErrors[] = $childError;

    }

    /**
     * @return FormattedError[]
     */
    public function getChildErrors () {

        return $this->childErrors;

    }

    /**
     * @return string
     */
    public function getField () {

        return $this->field;

    }

    /**
     * @return string
     */
    public function __toString () {

        return json_encode($this->toArray());

    }

    /**
     * @return array
     */
    public function toArray () {

        $childErrors = array();
        foreach ($this->childErrors as $childError) {
            $childErrors[] = $childError->toArray();
        }

        $result = ["code" => $this->code, "message" => $this->message];

        if (!empty($this->localizedMessage)) {
            $result['localizedMessage'] = $this->localizedMessage;
        }

        if ($this->field) {
            $result["field"] = $this->field;
        }

        if (count($childErrors)) {
            $result["errors"] = $childErrors;
        }

        return $result;

    }

}
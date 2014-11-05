<?php


namespace Archiweb\Error;


class FormattedError extends \Exception {

    /**
     * @var string
     */
    static protected $lang = 'en';

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
    protected $field;

    /**
     * @var FormattedError[]
     */
    protected $childErrors = [];

    /**
     * @param Error       $error
     * @param string|null $field
     */
    public function __construct ($error, $field = NULL) {

        $this->code = $error->getCode();
        $this->message = self::$lang == "fr" ? $error->getFrMessage() : $error->getEnMessage();
        $this->field = $field ? $field : $error->getField();

    }

    /**
     * @param string $lang
     */
    static public function setLang ($lang) {

        self::$lang = $lang;

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
     * @return array
     */
    public function toArray() {

        $childErrors = array();
        foreach ($this->childErrors as $childError) {
            $childErrors[] = $childError->toArray();
        }

        $result = ["code" => $this->code, "message" => $this->message];

        if ($this->field) {
            $result["field"] = $this->field;
        }

        if (count($childErrors)) {
            $result["errors"] = $childErrors;
        }

        return $result;

    }

    /**
     * @return string
     */
    public function __toString () {

        return json_encode($this->toArray());

    }

}
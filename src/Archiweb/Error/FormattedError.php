<?php


namespace Archiweb\Error;


class FormattedError extends \Exception {

    /**
     * @var string
     */
    static protected $lang;

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
     * @param Error $error
     */
    public function __construct ($error) {

        $this->code = $error->getCode();
        $this->message = self::$lang == "fr" ? $error->getFrMessage() : $error->getEnMessage();
        $this->field = $error->getField();

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
     * @return string
     */
    public function __toString () {

        $childErrors = array();
        foreach ($this->childErrors as $childError) {
            $childErrors[] = json_decode($childError);
        }

        if (count($childErrors)) {
            $childErrors = ["childErrors" => $childErrors];
        }

        $result = array_merge(["code"    => $this->code,
                               "message" => $this->message,
                               "field"   => $this->field
                              ], $childErrors);

        return json_encode($result);

    }

}
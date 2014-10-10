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
     * @var array
     */
    protected $childErrors;


    /**
     * @param Error $error
     */
    public function __construct ($error) {

        $this->code = $error->getCode();
        $this->message = self::$lang == "fr" ? $error->getFrMessage() : $error->getEnMessage();
        $this->field = $error->getField();

    }

    /**
     * @param FormattedError $childError
     */
    public function addChildError ($childError) {

        $this->childErrors[] = $childError;

    }

    /**
     * @return array
     */
    public function getChildErrors() {

        return $this->childErrors;

    }

    /**
     * @param string $lang
     */
    static public function setLang($lang) {

        return self::$lang = $lang;

    }


    /**
     * @return string
     */
    public function getField() {

        return $this->field;

    }

    /**
     * @return string
     */
    public function __toString () {

        $childErrorsStr = "";
        foreach ($this->childErrors as $childError) {
            $childErrorsStr .= $childError;
        }

        $result = array("code" => $this->code,
                        "message" => $this->message,
                        "field" => $this->field,
                        "childErrors" => json_decode($childErrorsStr));

        return json_encode($result);

    }

}
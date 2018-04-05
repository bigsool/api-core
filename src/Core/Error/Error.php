<?php


namespace Core\Error;


use Symfony\Component\Translation\Translator;

class Error {
    /**
     * @var Translator
     */
    protected $translator;

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
     * @var int
     */
    protected $parentCode;

    /**
     * @param int    $code
     * @param string $message
     * @param int    $parentCode
     * @param string $field
     */
    public function __construct (Translator $translator, $code, $message, $parentCode = NULL, $field = NULL) {

        $this->translator = $translator;
        $this->code = $code;
        $this->message = $message;
        $this->field = $field;
        $this->parentCode = $parentCode;

    }

    /**
     * @return int
     */
    public function getCode () {

        return $this->code;

    }

    /**
     * @return string
     */
    public function getMessage () {

        $trans = $this->translator->trans($this->message);

        if (empty($trans)) {
            $fallback = $this->translator->getFallbackLocales()[0] ?? null;

            if (!!$fallback) {
                $trans = $this->translator->trans($this->message, [], null, $fallback);
            } else $trans = $this->message;
        }

        return $trans;

    }

    /**
     * @return string
     */
    public function getField () {

        return $this->field;

    }

    /**
     * @param string $field
     */
    public function setField ($field) {

        $this->field = strval($field);
    }

    /**
     * @return int
     */
    public function getParentCode () {

        return $this->parentCode;

    }

}
<?php

namespace Core\Error;


use Symfony\Component\Translation\Translator;

class UntranslatedError extends Error {
    public function __construct(Translator $tr, int $code, string $message, ?int $parentCode = NULL, ?string $field = NULL) {
        parent::__construct($tr, $code, $message, $parentCode, $field);
    }


    /**
     * The untranslated errors are simply returning the original error message.
     * In that sense, we simply can return the original error message, and define it by filling the $message
     * (third constructor argument) on creation-time.
     *
     * @return string raw error message
     */
    public function getMessage() {
        return $this->message;
    }
}

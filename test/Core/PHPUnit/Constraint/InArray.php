<?php

namespace Core\PHPUnit\Constraint;


class InArray extends \PHPUnit_Framework_Constraint {

    /**
     * @var array
     */
    protected $key;

    /**
     * @var bool
     */
    protected $strict;

    /**
     * @param array $key
     * @param bool  $strict
     */
    public function __construct (array $key, $strict = true) {

        parent::__construct();
        $this->key = $key;
        $this->strict = $strict;

    }

    /**
     * Returns the description of the failure
     *
     * The beginning of failure messages is "Failed asserting that" in most
     * cases. This method should return the second part of that sentence.
     *
     * @param  mixed $other Evaluated value or object.
     *
     * @return string
     */
    protected function failureDescription ($other) {

        return 'the value ' . $this->exporter->export($other) . ' ' . $this->toString();

    }

    /**
     * Evaluates the constraint for parameter $other. Returns true if the
     * constraint is met, false otherwise.
     *
     * @param  mixed $other Value or object to evaluate.
     *
     * @return bool
     */
    protected function matches ($other) {

        return in_array($other, $this->key, true);

    }

    /**
     * Returns a string representation of the constraint.
     *
     * @return string
     */
    public function toString () {

        return 'is ' . ($this->strict ? 'strictly' : '') . ' one of these values '
               . $this->exporter->export($this->key);

    }
}

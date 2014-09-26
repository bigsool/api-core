<?php


namespace Archiweb\Rule;


use Archiweb\Context\FindQueryContext;
use Archiweb\Context\QueryContext;

interface Rule {

    /**
     * @param QueryContext $ctx
     *
     * @return bool
     */
    public function shouldApply (QueryContext $ctx);

    /**
     * @return Rule[]
     */
    public function listChildRules ();

    /**
     * @param FindQueryContext $ctx
     *
     * @return
     */
    public function apply (FindQueryContext $ctx);

    /**
     * @return string
     */
    public function getName ();

} 
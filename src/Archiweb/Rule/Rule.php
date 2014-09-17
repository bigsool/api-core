<?php


namespace Archiweb\Rule;


use Archiweb\ActionContext;

interface Rule {

    /**
     * @param ActionContext $ctx
     *
     * @return bool
     */
    public function shouldApply (ActionContext $ctx);

    /**
     * @return Rule[]
     */
    public function listChildRules ();

    /**
     * @param ActionContext $ctx
     */
    public function apply (ActionContext $ctx);

    /**
     * @return string
     */
    public function getName ();

    /**
     * @return string
     */
    public function getEntity ();

} 
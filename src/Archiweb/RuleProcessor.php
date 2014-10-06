<?php


namespace Archiweb;


use Archiweb\Context\QueryContext;
use Archiweb\Rule\Rule;
use Doctrine\ORM\Query;

class RuleProcessor {

    /**
     * @param QueryContext $ctx
     */
    public function apply (QueryContext $ctx) {

        $appliedRules = [];
        while (true) {
            $rules = $this->findRules($ctx);

            if ($rules === $appliedRules) {
                break;
            }

            foreach ($rules as $rule) {
                if (!in_array($rule, $appliedRules)) {
                    $rule->apply($ctx);
                }
            }

            $appliedRules = $rules;

        }
    }

    /**
     * @param QueryContext $ctx
     *
     * @return array
     */
    public function findRules (QueryContext $ctx) {

        $appCtx = $ctx->getApplicationContext();

        $rulesAndFlatten = [];

        foreach ($appCtx->getRules() as $rule) {
            $rulesAndFlatten[] = [$rule, $this->flatten($rule, $ctx)];
        }

        $rules = [];
        foreach ($rulesAndFlatten as $key => $flatten) {
            list($rule, $flattenRules) = $flatten;
            if (empty($flattenRules)) {
                continue;
            }
            for ($i = $key + 1; $i < count($rulesAndFlatten); ++$i) {
                if (in_array($rule, $rulesAndFlatten[$i][1], true)) {
                    continue 2; // the rule is a child of another rule, so pass to the next rule (foreach)
                }
            }
            $rules[] = $rule;
        }

        return $rules;

    }

    protected function flatten (Rule $rule, QueryContext $context) {

        $result = [];
        if ($rule->shouldApply($context)) {
            $this->flattenRec($result, $rule);
        }

        return $result;

    }

    protected function flattenRec (array &$accumulator, Rule $rule) {

        if (!in_array($rule, $accumulator)) {
            $accumulator[] = $rule;
        }
        foreach ($rule->listChildRules() as $childRule) {
            $this->flattenRec($accumulator, $childRule);
        }
        
    }

} 
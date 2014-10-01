<?php


namespace Archiweb;


use Archiweb\Context\FindQueryContext;
use Archiweb\Rule\Rule;

class RuleProcessor {

    public function apply (FindQueryContext $ctx) {

        $appCtx = $ctx->getApplicationContext();

        $rules = [];

        foreach ($appCtx->getRules() as $rule) {
            if ($rule->shouldApply($ctx)) {
                $rules[] = $rule;
            }
        }

        $isRuleInRules = function (Rule $thisRule, array $rules) use (&$isRuleInRules) {

            foreach ($rules as $rule) {
                if ($isRuleInRules($thisRule, $rule->listChildRules())) {
                    return true;
                }
            }

            return false;
        };

        $rulesToApply = [];
        foreach ($rules as $rule) {
            if (!$isRuleInRules($rule, $rules)) {
                $rulesToApply[] = $rule;
            }
        }

        foreach ($rulesToApply as $rule) {
            $rule->apply($ctx);
        }

    }

} 
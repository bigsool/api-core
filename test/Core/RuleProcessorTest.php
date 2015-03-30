<?php

namespace Core;


use Core\Context\FindQueryContext;
use Core\Rule\CallbackRule;
use Core\Rule\Processor;

class RuleProcessorTest extends TestCase {

    public function testApply () {

        $appCtx = $this->getApplicationContext();


        $appliedRules = [];

        $rulesWhichMustBeApplied = [];

        $rulesParams = [
            ['1_1', true, [], true],
            ['1_2', false, [], false],
            ['1_3', true, [], false],
            ['1_4', false, [], false],
            ['1_5', true, [], false],
            ['1_6', false, [], false],
            ['1_7', true, [], false],
            ['1_8', false, [], false],
            ['1_9', true, [], false],
            ['2_1', true, ['1_3', '1_4'], true],
            ['2_2', false, ['1_3', '1_4'], false],
            ['2_3', true, ['1_5', '1_6'], false],
            ['2_4', false, ['1_5', '1_6'], false],
            ['3_1', true, ['2_3', '2_4', '1_7', '1_8', '1_9'], true],
        ];

        $rules = [];
        foreach ($rulesParams as $param) {
            list($name, $apply, $children, $applied) = $param;
            $realChildren = [];
            foreach ($children as $child) {
                $realChildren[] = $rules[$child];
            }
            $rules[$name] =
                new CallbackRule($name, $this->getCb($apply), function () use (&$rules, $name, &$appliedRules) {

                    $appliedRules[] = $rules[$name];

                }, $realChildren);
            if ($applied) {
                $rulesWhichMustBeApplied[] = $rules[$name];
            }
        }

        foreach ($rules as $rule) {
            $appCtx->addRule($rule);
        }

        $processor = new Processor();

        $processor->apply(new FindQueryContext('Product'));

        $this->assertCount(3, $appliedRules);
        foreach ($rulesWhichMustBeApplied as $rule) {
            $this->assertContains($rule, $appliedRules);
        }

    }

    /**
     * @param $bool
     *
     * @return callable
     */
    protected function getCb ($bool) {

        return function () use ($bool) {

            return $bool;

        };
    }

} 
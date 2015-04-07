<?php


namespace Core\Module\Student;


use Core\Context\ActionContext;
use Core\Module\BasicHelper;

class Helper extends BasicHelper {

    public function createStudentInfo (ActionContext $actCtx, array $params) {

        $studentInfo = $this->createRealModel('StudentInfo');

        $this->basicSave($studentInfo, $params);

        $actCtx['studentInfo'] = $studentInfo;

    }

}
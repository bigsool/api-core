<?php


namespace Core\Module\Student;


use Core\Context\ActionContext;
use Core\Helper\BasicHelper;

class Helper extends BasicHelper {

    /**
     * @param ActionContext $actCtx
     * @param array         $params
     */
    public function createStudentInfo (ActionContext $actCtx, array $params) {

        $studentInfo = $this->createRealModel('StudentInfo');

        $this->basicSetValues($studentInfo, $params);

        $actCtx['studentInfo'] = $studentInfo;

    }

    /**
     * @param ActionContext $actCtx
     * @param               $studentInfo
     * @param array         $params
     */
    public function updateStudentInfo (ActionContext $actCtx, $studentInfo, array $params) {

        $this->checkRealModelType($studentInfo, 'StudentInfo');

        $this->basicSetValues($studentInfo, $params);

        $actCtx['studentInfo'] = $studentInfo;

    }

}
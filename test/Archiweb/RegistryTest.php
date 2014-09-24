<?php


namespace Archiweb;


use Archiweb\Model\Company;

class RegistryTest extends TestCase {

    public function testCreateWithoutParams () {

        $qryCtx = $this->getQueryContext();
        $qryCtx->setCommand('INSERT');
        $qryCtx->setEntity('Company');

        $registry = new Registry();
        $company = $registry->query($qryCtx);

        $this->assertEquals(new Company(), $company);

    }

    public function testCreateWithParams () {

        $companyName = 'bigsool';
        $qryCtx = $this->getQueryContext();
        $qryCtx->setCommand('INSERT');
        $qryCtx->setEntity('Company');
        $qryCtx->setParams(['name' => $companyName]);

        $registry = new Registry();
        $company = $registry->query($qryCtx);

        $this->assertInstanceOf('\Archiweb\Model\Company', $company);
        $this->assertEquals($companyName, $company->getName());

    }

    /**
     * @expectedException \Exception
     */
    public function testCreateWithUnsafeParameter () {

        //$company = $this->registry->create('Company', array('name' => $this->getParameterMock('company name', false)));

    }

    /**
     * @expectedException \Exception
     */
    public function testCreateWithFieldNotExists () {

        //$this->registry->create('Company', array('qwe' => $this->getParameterMock('qwe', true)));

    }

    /**
     * @expectedException \Exception
     */
    public function testEntityNotFound () {

        $qryCtx = $this->getQueryContext();
        $qryCtx->setCommand('INSERT');
        $qryCtx->setEntity('qwe');

        $registry = new Registry();
        $registry->query($qryCtx);

    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidParameterType () {

        //$this->registry->create('Company', array('name' => 'qwe'));

    }

    /**
     * @expectedException \Exception
     */
    public function testNoneFieldGiven () {

        $qryCtx = $this->getQueryContext();
        $qryCtx->setCommand('SELECT');
        $qryCtx->setEntity('Company');

        $registry = new Registry();
        $registry->query($qryCtx);

    }

    public function testFindWithoutFilter () {

        $qryCtx = $this->getQueryContext();
        $qryCtx->setCommand('SELECT');
        $qryCtx->setEntity('Company');
        $qryCtx->addField(new StarField($qryCtx->getEntity()));

        $registry = new Registry();
        $qb = $registry->query($qryCtx);

        $this->assertInstanceOf('\Doctrine\ORM\QueryBuilder', $qb);

        $dql = $qb->getDQL();
        $this->assertSame('SELECT company FROM \Archiweb\Model\Company company', $dql);

    }

    public function testFindWithoutOneFilter () {

        $qryCtx = $this->getQueryContext();
        $qryCtx->setCommand('SELECT');
        $qryCtx->setEntity('Company');
        $qryCtx->addField(new StarField($qryCtx->getEntity()));

        $filter = $this->getMockFilter();
        $expression = $this->getMockExpression();
        $expression->method('resolve')->willReturn('1 = 1');
        $filter->method('getExpression')->willReturn($expression);
        $qryCtx->addFilter($filter);

        $registry = new Registry();
        $qb = $registry->query($qryCtx);

        $this->assertInstanceOf('\Doctrine\ORM\QueryBuilder', $qb);

        $dql = $qb->getDQL();
        $this->assertSame('SELECT company FROM \Archiweb\Model\Company company WHERE 1 = 1', $dql);

    }

    public function testFindWithoutMultiParams () {

        $qryCtx = $this->getQueryContext();
        $qryCtx->setCommand('SELECT');
        $qryCtx->setEntity('Company');
        $qryCtx->addField(new StarField($qryCtx->getEntity()));

        $wheres = ['1 = 1', '2 = 2', '3 = 3'];

        foreach ($wheres as $where) {

            $filter = $this->getMockFilter();
            $expression = $this->getMockExpression();
            $expression->method('resolve')->willReturn($where);
            $filter->method('getExpression')->willReturn($expression);
            $qryCtx->addFilter($filter);

        }
        $registry = new Registry();
        $qb = $registry->query($qryCtx);

        $this->assertInstanceOf('\Doctrine\ORM\QueryBuilder', $qb);

        $dql = $qb->getDQL();
        $this->assertSame('SELECT company FROM \Archiweb\Model\Company company WHERE ' . implode(' AND ', $wheres),
                          $dql);

    }

}
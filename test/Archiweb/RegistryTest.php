<?php


namespace Archiweb;


class RegistryTest extends TestCase {

    /**
     * @expectedException \Exception
     */
    public function testEntityNotFound () {

        $qryCtx = $this->getFindQueryContext('qwe');

        $registry = $this->getRegistry();
        $registry->find($qryCtx);

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

        $qryCtx = $this->getFindQueryContext('Company');

        $registry = $this->getRegistry();
        $registry->find($qryCtx);

    }

    public function testFindWithoutFilter () {
        /*
                $qryCtx = $this->getFindQueryContext('Company');
                $qryCtx->addField(new StarField($qryCtx->getEntity()));

                $registry = new Registry();
                $qb = $registry->find($qryCtx);

                $this->assertInstanceOf('\Doctrine\ORM\QueryBuilder', $qb);

                $dql = $qb->getDQL();
                $this->assertSame('SELECT company FROM \Archiweb\Model\Company company', $dql);
        */
    }

    public function testFindWithoutOneFilter () {
        /*
                $qryCtx = $this->getFindQueryContext();
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
        */
    }

    public function testFindWithoutMultiParams () {
        /*
                $qryCtx = $this->getFindQueryContext();
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
        */
    }

}
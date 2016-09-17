<?php
/**
 * Created by PhpStorm.
 * User: yemistikris
 * Date: 21/05/16
 * Time: 17:49.
 */
namespace Pyrex\DupeBundle\Repository;

class DupeFileRepository extends AbstractRepository
{
    /**
     * @return int
     */
    public function deletedCount()
    {
        $qb = $this->createQueryBuilder('t');
        $qb->select('count(t.id)');
        $qb->where('t.deleteFlag = :boolVal');
        $qb->setParameter('boolVal', true);

        return $qb->getQuery()->getSingleScalarResult();
    }
}

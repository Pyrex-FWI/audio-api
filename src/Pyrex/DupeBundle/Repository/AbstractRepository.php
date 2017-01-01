<?php
/**
 * Created by PhpStorm.
 * User: yemistikris
 * Date: 21/05/16
 * Time: 17:49.
 */

namespace Pyrex\DupeBundle\Repository;

use Doctrine\ORM\EntityRepository;

abstract class AbstractRepository extends EntityRepository
{
    /**
     * @return int
     */
    public function count()
    {
        $qb = $this->createQueryBuilder('t');
        $qb->select('count(t.id)');

        return $qb->getQuery()->getSingleScalarResult();
    }
}

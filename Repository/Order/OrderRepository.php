<?php

namespace Maci\PageBundle\Repository\Order;

use Doctrine\ORM\EntityRepository;

class OrderRepository extends EntityRepository
{
    public function getConfirmed()
    {
        $query = $this->createQueryBuilder('o')
            ->where('o.invoice IS NOT NULL')
            ->andWhere('o.removed = false')
            ->orderBy('o.invoice', 'DESC')
            ->getQuery()
        ;

        return $query->getResult();
    }
}

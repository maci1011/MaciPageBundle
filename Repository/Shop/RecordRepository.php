<?php

namespace Maci\PageBundle\Repository\Shop;

use Doctrine\ORM\EntityRepository;

class RecordRepository extends EntityRepository
{
	public function fromTo($from, $to)
	{
		$query = $this->createQueryBuilder('r');

		if ($from)
			$query->where('r.recorded >= :from')
				->setParameter(':from', $from);

		if ($to)
			$query->andWhere('r.recorded <= :to')
				->setParameter(':to', $to);

		$query->orderBy('r.recorded', 'ASC');

		return $query->getQuery()->getResult();
	}
}

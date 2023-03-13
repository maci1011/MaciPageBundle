<?php

namespace Maci\PageBundle\Repository\Shop;

use Doctrine\ORM\EntityRepository;

class RecordRepository extends EntityRepository
{
	public function fromTo($from, $to, $collection = false)
	{
		$query = $this->createQueryBuilder('r');

		if ($from)
			$query->where('r.recorded >= :from')
				->setParameter(':from', $from);

		if ($to)
			$query->andWhere('r.recorded <= :to')
				->setParameter(':to', $to);

		if (!($collection === false) && $collection != '*')
		{
			if ($collection === null)
				$query->andWhere('r.collection IS NULL');
			else
				$query->andWhere('r.collection = :collection')
					->setParameter(':collection', $collection);
		}

		$query->orderBy('r.recorded', 'ASC');

		return $query->getQuery()->getResult();
	}
}

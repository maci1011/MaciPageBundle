<?php

namespace Maci\PageBundle\Repository\Order;

use Doctrine\ORM\EntityRepository;

class OrderRepository extends EntityRepository
{
	public function getLasts()
	{
		$query = $this->createQueryBuilder('o')
			->where('o.status = :st1')
			->setParameter(':st1', 'new')
			->orWhere('o.status = :st2')
			->setparameter(':st2', 'current')
			->andWhere('o.removed = false')
			->orderBy('o.updated', 'DESC')
			->getQuery()
		;

		return $query->getResult();
	}

	public function getConfirmed()
	{
		$query = $this->createQueryBuilder('o')
			->where('o.status = :st1')
			->setparameter(':st1', 'complete')
			->orWhere('o.status = :st2')
			->setparameter(':st2', 'paid')
			->orWhere('o.status = :st3')
			->setparameter(':st3', 'confirm')
			->andWhere('o.removed = false')
			->orderBy('o.invoice', 'DESC')
			->getQuery()
		;

		return $query->getResult();
	}

	public function getEnded()
	{
		$query = $this->createQueryBuilder('o')
			->where('o.status = :st1')
			->setparameter(':st1', 'canceled')
			->orWhere('o.status = :st2')
			->setparameter(':st2', 'end')
			->andWhere('o.removed = false')
			->orderBy('o.invoice', 'DESC')
			->getQuery()
		;

		return $query->getResult();
	}
}

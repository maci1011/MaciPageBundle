<?php

namespace Maci\PageBundle\Repository\Shop;

use Doctrine\ORM\EntityRepository;

class ProductRepository extends EntityRepository
{
	public function getList()
	{
		$query = $this->createQueryBuilder('p');
		$this->addProductFilters($query);
		$query = $query->setMaxResults(60)->getQuery();

		return $query->getResult();
	}

	public function getByPath($path)
	{
		$query = $this->createQueryBuilder('p')
			->where('p.path = :path')
			->setParameter(':path', $path);
		$this->addProductFilters($query);
		$query = $query->getQuery();

		return $query->getOneOrNullResult();
	}

	public function getByCategory($category)
	{
		$query = $this->createQueryBuilder('p')
			->leftJoin('p.categoryItems', 'i')
			->leftJoin('i.category', 'c')
			->where('c.id = :id')
			->setParameter(':id', $category->getId());
		$this->addProductFilters($query);
		$query = $query->getQuery();

		return $query->getResult();
	}

	public static function addProductFilters($query)
	{
		$query = $query->andWhere('p.public = true')
			->andWhere('p.removed = false')
			->orderBy('p.position');
	}

	public function search($request)
	{
		$query = $request->get('query');
		$locale = $request->get('_locale');
		$search = $this->createQueryBuilder('p')
			->where('p.name LIKE :query')
			->orWhere('p.description LIKE :query')
			->orWhere('p.composition LIKE :query')
			->andWhere('p.locale = :locale')
			->setParameter(':query', "%$query%")
			->setParameter(':locale', $locale)
			->getQuery()
		;

		return $search->getResult();
	}
}

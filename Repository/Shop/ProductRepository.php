<?php

namespace Maci\PageBundle\Repository\Shop;

use Doctrine\ORM\EntityRepository;

class ProductRepository extends EntityRepository
{
	public function getLatestProducts($max)
	{
		$query = $this->createQueryBuilder('p');
		$this->addProductListFilters($query);
		$query = $query->setMaxResults($max);
		return $query->getQuery()->getResult();
	}

	public function getList()
	{
		$query = $this->createQueryBuilder('p');
		$this->addProductListFilters($query);
		return $query->getQuery()->getResult();
	}

	public function getPromo()
	{
		$query = $this->createQueryBuilder('p');
		$this->addProductListFilters($query);
		$query = $query->andWhere('p.sale IS NOT NULL');
		return $query->getQuery()->getResult();
	}

	public function getByPath($path)
	{
		$query = $this->createQueryBuilder('p')
			->where('p.path = :path')
			->setParameter(':path', $path);
		$this->addProductFilters($query);
		return $query->getQuery()->getOneOrNullResult();
	}

	public function getByCategory($category)
	{
		$query = $this->createQueryBuilder('p')
			->leftJoin('p.categoryItems', 'i')
			->leftJoin('i.category', 'c')
			->where('c.id = :id')
			->setParameter(':id', $category->getId());
		$this->addProductListFilters($query);
		return $query->getQuery()->getResult();
	}

	public static function addProductListFilters(&$query)
	{
		self::addProductFilters($query);
		$query = $query
			->andWhere('0 < p.quantity')
			->andWhere('p.removed = false')
			->orderBy('p.updated', 'DESC');
	}

	public static function addProductFilters(&$query)
	{
		$query = $query
			->andWhere('p.public = true');
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
		;
		return $search->getQuery()->getResult();
	}
}

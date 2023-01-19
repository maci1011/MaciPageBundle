<?php

namespace Maci\PageBundle\Repository\Blog;

use Doctrine\ORM\EntityRepository;

/**
 * PostRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PostRepository extends EntityRepository
{
	protected $allCount = null;

	public function getLatestPosts($locale, $max = 13)
	{
		$q = $this->createQueryBuilder('p');
		$q
			->where('p.removed = :removed')
			->setparameter(':removed', false)
			->andWhere('p.status = :status')
			->setparameter(':status', 'pubblished')
			->andWhere('p.locale = :locale')
			->setparameter(':locale', $locale)
			->orderBy('p.created', 'DESC')
		;

		if (0 < $max)
			$q->setMaxResults($max);

		return $q->getQuery()->getResult();
	}

	public function getByTag($id)
	{
		$q = $this->createQueryBuilder('p');
		$q
			->leftJoin('p.tags', 't')
			->where('t.id = :id')
			->setParameter('id', $id)
			->andWhere('p.removed = :removed')
			->setparameter(':removed', false)
			->andWhere('p.status = :status')
			->setparameter(':status', 'pubblished')
			->orderBy('p.created', 'DESC')
		;
		return $q->getQuery()->getResult();
	}

	public function getByAuthor($author)
	{
		$q = $this->createQueryBuilder('p');
		$q
			->leftJoin('p.editors', 'e')
			->leftJoin('e.author', 'a')
			->where('a.id = :id')
			->setParameter('id', $author->getId())
			->andWhere('p.removed = :removed')
			->setparameter(':removed', false)
			->andWhere('p.status = :status')
			->setparameter(':status', 'pubblished')
			->orderBy('p.created', 'ASC')
			;
		return $q->getQuery()->getResult();
	}

	public function getPaged($limit, $page)
	{
		$offset = ($page - 1) * $limit;

		$q = $this->createQueryBuilder('p');
		$q
			->setFirstResult($offset)
			->setMaxResults($limit)
			;

		return $q->getQuery()->getResult();
	}

	public function countAll()
	{
		if (!is_null($this->allCount)) {
			return $this->allCount;
		}

		$q = $this->createQueryBuilder('p');
		$q
			->select('COUNT(p.id)')
			;

		return $q->getQuery()->getSingleScalarResult();
	}

	public function search($request)
	{
		$query = $request->get('query');
		$locale = $request->get('_locale');
		$search = $this->createQueryBuilder('p')
			->leftJoin('p.translations', 't')
			->where('t.title LIKE :query')
			->orWhere('t.subtitle LIKE :query')
			->orWhere('t.header LIKE :query')
			->orWhere('t.content LIKE :query')
			->setParameter(':query', "%$query%")
			->andWhere('t.locale = :locale')
			->setParameter(':locale', $locale)
			->andWhere('p.status = :status')
			->setparameter(':status', 'pubblished')
			->getQuery()
		;

		return $search->getResult();
	}
}

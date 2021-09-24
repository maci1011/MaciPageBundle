<?php

namespace Maci\PageBundle\Repository\Shop;

use Doctrine\ORM\EntityRepository;

class ProductRepository extends EntityRepository
{
    public function getList()
    {
        $query = $this->createQueryBuilder('p')
            ->where('p.removed = false')
            ->where('p.tabbed = true')
            ->orderBy('p.position')
            ->setMaxResults(60)
            ->getQuery()
        ;

        return $query->getResult();
    }

    public function getByPath($path)
    {
        $query = $this->createQueryBuilder('p')
            ->where('p.path = :path')
            ->setParameter(':path', $path)
            ->andWhere('p.removed = false')
            ->orderBy('p.position')
            ->getQuery()
        ;

        return $query->getOneOrNullResult();
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

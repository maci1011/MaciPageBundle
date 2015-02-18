<?php

namespace Maci\PageBundle\Repository;

use Doctrine\ORM\EntityRepository;

class PageRepository extends EntityRepository
{
    public function search($request)
    {
        $query = $request->get('query');
        $locale = $request->get('_locale');
        $search = $this->createQueryBuilder('p')
            ->leftJoin('p.translations', 't')
            ->where('t.title LIKE :query')
            ->orWhere('t.subtitle LIKE :query')
            ->orWhere('t.description LIKE :query')
            ->orWhere('t.header LIKE :query')
            ->orWhere('t.content LIKE :query')
            ->andWhere('t.locale = :locale')
            ->setParameter(':query', "%$query%")
            ->setParameter(':locale', $locale)
            ->getQuery()
        ;

        return $search->getResult();
    }
}

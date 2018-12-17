<?php

namespace Maci\PageBundle\Repository\Media;

use Doctrine\ORM\EntityRepository;

/**
 * Item
 */
class ItemRepository extends EntityRepository
{
    public function search($request)
    {
        $query = $request->get('query');
        $locale = $request->get('_locale');
        $search = $this->createQueryBuilder('i')
            ->leftJoin('i.translations', 't')
            ->where('t.name LIKE :query')
            ->orWhere('t.description LIKE :query')
            ->andWhere('t.locale = :locale')
            ->setParameter(':query', "%$query%")
            ->setParameter(':locale', $locale)
            ->getQuery()
        ;

        return $search->getResult();
    }
}

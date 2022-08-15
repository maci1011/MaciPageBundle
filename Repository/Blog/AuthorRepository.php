<?php

namespace Maci\PageBundle\Repository\Blog;

use Doctrine\ORM\EntityRepository;

/**
 * TagRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AuthorRepository extends EntityRepository
{
    public function getList($locale)
    {
        $q = $this->createQueryBuilder('a');
        $q
            ->where('a.locale = :locale')
            ->setparameter(':locale', $locale)
            ->orderBy('a.name', 'ASC')
            ;
        return $q->getQuery()->getResult();
    }
}
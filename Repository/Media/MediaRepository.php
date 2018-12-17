<?php

namespace Maci\PageBundle\Repository\Media;

use Doctrine\ORM\EntityRepository;

class MediaRepository extends EntityRepository
{
    public function getList()
    {
        $q = $this->getEntityManager()
            ->createQuery(
                'SELECT d FROM MaciPageBundle:Media\Media d WHERE d.public = true ORDER BY d.created DESC'
            )
        ;

        return $q->getResult();
    }

    public function getUserDocuments($user)
    {
        $q = $this->getEntityManager()
            ->createQuery(
                'SELECT d FROM MaciPageBundle:Media\Media d LEFT JOIN d.permissions p WHERE p.user = :id AND p.status = :status ORDER BY d.created DESC'
            )
            ->setParameter('id', $user->getId())
            ->setParameter('status', 'end')
        ;

        return $q->getResult();
    }

    public function search($request)
    {
        $query = $request->get('query');
        $locale = $request->get('_locale');
        $search = $this->createQueryBuilder('m')
            ->leftJoin('m.translations', 't')
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

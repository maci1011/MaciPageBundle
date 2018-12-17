<?php

namespace Maci\PageBundle\Repository\Media;

use Doctrine\ORM\EntityRepository;

/**
 * Album
 */
class AlbumRepository extends EntityRepository
{
    public function getByType($type)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT a FROM MaciPageBundle:Media\Album a WHERE a.type = :type ORDER BY a.position ASC'
            )
            ->setParameter('type', $type)
            ->getResult()
        ;
    }

    public function getGallery($type)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT a FROM MaciPageBundle:Media\Album a WHERE a.type = :type AND a.parent IS NULL ORDER BY a.position ASC'
            )
            ->setParameter('type', $type)
            ->getResult()
        ;
    }

    public function getById($id)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT a FROM MaciPageBundle:Media\Album a LEFT JOIN a.items i WHERE a.id = :id ORDER BY i.position ASC'
            )
            ->setParameter('id', $id)
            ->getOneOrNullResult()
        ;
    }
}

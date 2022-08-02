<?php

namespace Maci\PageBundle\Repository\Mailer;

/**
 * MailRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MailRepository extends \Doctrine\ORM\EntityRepository
{
    public function getUserMails($user)
    {
        $q = $this->getEntityManager()
            ->createQuery(
                'SELECT e FROM MaciPagebuNdle:Mail e WHERE e.user = :user ORDER BY e.created DESC'
            )
            ->setParameter('user', $user)
        ;

        return $q->getResult();
    }
}

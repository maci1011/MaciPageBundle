<?php

namespace Maci\PageBundle\Repository\Media;

use Doctrine\ORM\EntityRepository;
use Maci\PageBundle\Entity\Permission;

class PermissionRepository extends EntityRepository
{
    public function setDocumentsPermissions($documents, $user, $note = false)
    {
        if (count($documents)) {

            foreach ($documents as $document) {

                $permission = $this->_em->getRepository('MaciPageBundle:Media\Permission')->findOneBy(array(
                    'user' => $user,
                    'media' => $document
                ));

                if ($permission) {

                    $permission->setStatus('end');

                } else {

                    $permission = new Permission;

                    $permission->setUser($user);
                    $permission->setMedia($document);

                    $permission->setStatus('end');

                    if ($note) {
                    	$permission->setNote($note);
                    }

                    $this->_em->persist( $permission );

                }

            }

        	$this->_em->flush();
        }
    }
}

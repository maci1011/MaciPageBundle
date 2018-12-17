<?php

namespace Maci\PageBundle\Entity\Media;

use Doctrine\ORM\Mapping as ORM;

/**
 * MediaPermission
 */
class Permission
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $status;

    /**
     * @var string
     */
    private $note;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var \DateTime
     */
    private $updated;

    /**
     * @var \Maci\UserBundle\Entity\User
     */
    private $user;

    /**
     * @var \Maci\PageBundle\Entity\Media\Media
     */
    private $media;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->status = 'listed';
    }


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return MediaPermission
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set method
     *
     * @param string $method
     * @return MediaPermission
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Get method
     *
     * @return string 
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set transaction_id
     *
     * @param string $transactionId
     * @return MediaPermission
     */
    public function setTransactionId($transactionId)
    {
        $this->transaction_id = $transactionId;

        return $this;
    }

    /**
     * Get transaction_id
     *
     * @return string 
     */
    public function getTransactionId()
    {
        return $this->transaction_id;
    }

    /**
     * Set note
     *
     * @param string $note
     * @return MediaPermission
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return string 
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return MediaPermission
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return MediaPermission
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set user
     *
     * @param \Maci\UserBundle\Entity\User $user
     * @return MediaPermission
     */
    public function setUser(\Maci\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Maci\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set media
     *
     * @param \Maci\PageBundle\Entity\Media\Media $media
     * @return MediaPermission
     */
    public function setMedia(\Maci\PageBundle\Entity\Media\Media $media = null)
    {
        $this->media = $media;

        return $this;
    }

    /**
     * Get media
     *
     * @return \Maci\PageBundle\Entity\Media\Media 
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * 
     */
    public function setUpdatedValue()
    {
        $this->updated = new \DateTime;
    }

    /**
     * 
     */
    public function setCreatedValue()
    {
        $this->created = new \DateTime;
    }

    /**
     * __toString()
     */
    public function __toString()
    {
        return ( $this->getUser()->getUsername().': '.$this->getStatus() );
    }
}

<?php

namespace Maci\PageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Page
 */
class Page
{
    use \A2lix\I18nDoctrineBundle\Doctrine\ORM\Util\Translatable;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var string
     */
    private $path;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var \DateTime
     */
    private $updated;

    /**
     * @var \Maci\PageBundle\Entity\Page
     */
    private $parent;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $children;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $translations;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->translations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->status = 'active';
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
     * @param integer $status
     * @return Page
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return Page
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Page
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
     * @return Page
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
     * Set parent
     *
     * @param \Maci\PageBundle\Entity\Page $parent
     * @return Page
     */
    public function setParent(\Maci\PageBundle\Entity\Page $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Maci\PageBundle\Entity\Page 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add children
     *
     * @param \Maci\PageBundle\Entity\Page $children
     * @return Page
     */
    public function addChild(\Maci\PageBundle\Entity\Page $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \Maci\PageBundle\Entity\Page $children
     */
    public function removeChild(\Maci\PageBundle\Entity\Page $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * setUpdatedValue
     */
    public function setUpdatedValue()
    {
        $this->created = new \DateTime();
    }

    /**
     * setCreatedValue
     */
    public function setCreatedValue()
    {
        $this->updated = new \DateTime();
    }

    /**
     * getTitle
     */
    public function getTitle()
    {
        return $this->__call('title', null);
    }

    /**
     * getContent
     */
    public function getContent()
    {
        return $this->__call('content', null);
    }

    /**
     * getContent
     */
    public function getMetaTitle()
    {
        return $this->__call('metaTitle', null);
    }

    /**
     * getContent
     */
    public function getMetaDescription()
    {
        return $this->__call('metaDescription', null);
    }

    /**
     * __toString()
     */
    public function __toString()
    {
        return $this->getTitle();
    }
}

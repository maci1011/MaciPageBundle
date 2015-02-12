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
     * @var string
     */
    private $template;

    /**
     * @var string
     */
    private $map;

    /**
     * @var string
     */
    private $album;

    /**
     * @var string
     */
    private $gallery;

    /**
     * @var string
     */
    private $slider;

    /**
     * @var string
     */
    private $category;

    /**
     * @var string
     */
    private $tag;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var \DateTime
     */
    private $updated;

    /**
     * @var boolean
     */
    private $removed;

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
        $this->removed = false;
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

    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function getTemplateArray()
    {
        return array(
            'MaciPageBundle:Default:page.html.twig' => 'Page',
            'MaciPageBundle:Default:fullpage.html.twig' => 'Full Page',
            'MaciPageBundle:Default:homepage.html.twig' => 'Homepage',
            'MaciPageBundle:Default:contacts.html.twig' => 'Contacts'
        );
    }

    public function getTemplateLabel()
    {
        $array = $this->getTemplateArray();
        if (array_key_exists($this->template, $array)) {
            return $array[$this->template];
        }
        $str = str_replace('_', ' ', $this->template);
        return ucwords($str);
    }

    /**
     * Set map
     *
     * @param string $map
     * @return Page
     */
    public function setMap($map)
    {
        $this->map = $map;

        return $this;
    }

    /**
     * Get map
     *
     * @return string 
     */
    public function getMap()
    {
        return $this->map;
    }

    /**
     * Set album
     *
     * @param string $album
     * @return Page
     */
    public function setAlbum($album)
    {
        $this->album = $album;

        return $this;
    }

    /**
     * Get album
     *
     * @return string 
     */
    public function getAlbum()
    {
        return $this->album;
    }

    /**
     * Set gallery
     *
     * @param string $gallery
     * @return Page
     */
    public function setGallery($gallery)
    {
        $this->gallery = $gallery;

        return $this;
    }

    /**
     * Get gallery
     *
     * @return string 
     */
    public function getGallery()
    {
        return $this->gallery;
    }

    /**
     * Set slider
     *
     * @param string $slider
     * @return Page
     */
    public function setSlider($slider)
    {
        $this->slider = $slider;

        return $this;
    }

    /**
     * Get slider
     *
     * @return string 
     */
    public function getSlider()
    {
        return $this->slider;
    }

    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    public function getTag()
    {
        return $this->tag;
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

    public function setRemoved($removed)
    {
        $this->removed = $removed;

        return $this;
    }

    public function getRemoved()
    {
        return $this->removed;
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
     * getSubtitle
     */
    public function getSubitle()
    {
        return $this->__call('subtitle', null);
    }

    /**
     * getDescription()
     */
    public function getDescription()
    {
        return $this->__call('description', null);
    }

    /**
     * getHeader
     */
    public function getHeader()
    {
        return $this->__call('header', null);
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

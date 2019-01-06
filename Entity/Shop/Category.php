<?php

namespace Maci\PageBundle\Entity\Shop;

/**
 * Category
 */
class Category
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $type;

    /**
     * @var integer
     */
    private $position;

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
     * @var \Maci\PageBundle\Entity\Shop\Category
     */
    private $parent;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $children;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $items;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $translations;

    /**
     * @var string
     */
    private $locale;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->items = new \Doctrine\Common\Collections\ArrayCollection();
        $this->translations = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return CategoryTranslation
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return CategoryTranslation
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return Category
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer 
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Category
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
     * @return Category
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

    public function addChild(\Maci\PageBundle\Entity\Shop\Category $children)
    {
        $this->children[] = $children;

        return $this;
    }

    public function removeChild(\Maci\PageBundle\Entity\Shop\Category $children)
    {
        $this->children->removeElement($children);
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function setParent(\Maci\PageBundle\Entity\Shop\Category $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function getParentsList()
    {
        $list = array();
        $obj = $this->parent;
        while (is_object($obj)) {
            $list[] = $obj;
            $obj = $obj->getParent();
        }
        return array_reverse($list);
    }

    /**
     * Add items
     *
     * @param \Maci\PageBundle\Entity\Shop\CategoryItem $items
     * @return Category
     */
    public function addItem(\Maci\PageBundle\Entity\Shop\CategoryItem $items)
    {
        $this->items[] = $items;

        return $this;
    }

    /**
     * Remove items
     *
     * @param \Maci\PageBundle\Entity\Shop\CategoryItem $items
     */
    public function removeItem(\Maci\PageBundle\Entity\Shop\CategoryItem $items)
    {
        $this->items->removeElement($items);
    }

    /**
     * Get items
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getItems()
    {
        return $this->items;
    }

    public function getCurrentItems()
    {
        return $this->getItems()->filter(function($e){
            return !$e->getProduct()->getRemoved();
        });
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
     * __toString()
     */
    public function __toString()
    {
        return $this->getName();
    }
}

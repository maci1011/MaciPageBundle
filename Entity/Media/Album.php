<?php

namespace Maci\PageBundle\Entity\Media;

/**
 * Album
 */
class Album
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
    private $subtitle;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $meta_title;

    /**
     * @var text
     */
    private $meta_description;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $redirect;

    /**
     * @var integer
     */
    private $position;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $children;

    /**
     * @var \Maci\PageBundle\Entity\Media\Album
     */
    private $parent;

    /**
     * @var \Maci\PageBundle\Entity\Media\Media
     */
    private $preview;

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
        $this->type = 'album';
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
     * @return Album
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

    public function getLabel()
    {
        return (strlen($this->getName()) ? $this->getName() : 'Album[' . $this->getId() . ']');
    }

    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Album
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

    /**
     * Set title
     */
    public function setMetaTitle($meta_title)
    {
        $this->meta_title = $meta_title;

        return $this;
    }

    /**
     * Get title
     */
    public function getMetaTitle()
    {
        return $this->meta_title;
    }

    /**
     * Set description
     */
    public function setMetaDescription($meta_description)
    {
        $this->meta_description = $meta_description;

        return $this;
    }

    /**
     * Get description
     */
    public function getMetaDescription()
    {
        return $this->meta_description;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Album
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    public function getTypeArray()
    {
        return array(
            'Gallery' => 'gallery',
            'Products' => 'products',
            'Album' => 'album'
        );
    }

    public function getTypeLabel()
    {
        $array = $this->getTypeArray();
        if (array_key_exists($this->type, $array)) {
            return $array[$this->type];
        }
        $str = str_replace('_', ' ', $this->type);
        return ucwords($str);
    }

    /**
     * Set redirect
     *
     * @param string $redirect
     * @return string
     */
    public function setRedirect($redirect)
    {
        $this->redirect = $redirect;

        return $this;
    }

    /**
     * Get redirect
     *
     * @return string 
     */
    public function getRedirect()
    {
        return $this->redirect;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return Album
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

    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Add children
     *
     * @param \Maci\PageBundle\Entity\Media\Album $children
     * @return Album
     */
    public function addChild(\Maci\PageBundle\Entity\Media\Album $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \Maci\PageBundle\Entity\Media\Album $children
     */
    public function removeChild(\Maci\PageBundle\Entity\Media\Album $children)
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
     * Set parent
     *
     * @param \Maci\PageBundle\Entity\Media\Album $parent
     * @return Album
     */
    public function setParent(\Maci\PageBundle\Entity\Media\Album $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Maci\PageBundle\Entity\Media\Album 
     */
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
     * Set preview
     *
     * @param \Maci\PageBundle\Entity\Media\Media $preview
     * @return Album
     */
    public function setPreview(\Maci\PageBundle\Entity\Media\Media $preview = null)
    {
        $this->preview = $preview;

        return $this;
    }

    /**
     * Get preview
     *
     * @return \Maci\PageBundle\Entity\Media\Media 
     */
    public function getPreview()
    {
        return $this->preview;
    }

    public function getWebPreview()
    {
        if ($this->preview) {
            return $this->preview->getWebPreview();
        }
        return '/images/defaults/folder-icon.png';
    }

    /**
     * Add items
     *
     * @param \Maci\PageBundle\Entity\Media\Item $items
     * @return Album
     */
    public function addItem(\Maci\PageBundle\Entity\Media\Item $items)
    {
        $this->items[] = $items;

        return $this;
    }

    /**
     * Remove items
     *
     * @param \Maci\PageBundle\Entity\Media\Item $items
     */
    public function removeItem(\Maci\PageBundle\Entity\Media\Item $items)
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

    /**
     * Get public media
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPrivateMedia()
    {
        return $this->items->filter(function($e){
            return !$e->getMedia()->getPublic();
        });
    }

    /**
     * Get media | Images
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPrivateDocuments()
    {
        return $this->getPrivateMedia()->filter(function($e){
            return $e->getType() == 'document';
        });
    }

    /**
     * Get public media
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPublicMedia()
    {
        return $this->items->filter(function($e){
            return ( is_object($e->getMedia()) ? $e->getMedia()->getPublic() : false );
        });
    }

    /**
     * Get media | Images
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getImages()
    {
        return $this->getPublicMedia()->filter(function($e){
            return $e->getMedia()->getType() == 'image';
        });
    }

    public function getSlides()
    {
        return $this->items->filter(function($e){
            return ( is_object($e->getMedia()) ? $e->getMedia()->getPublic() : true );
        });
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
        return $this->getLabel();
    }
}

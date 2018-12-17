<?php

namespace Maci\PageBundle\Entity\Media;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tag
 */
class Tag
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
     * @var boolean
     */
    private $favourite;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $media;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $brand_items;

    /**
     * @var \Maci\MediaBundle\Entity\Media
     */
    private $preview;

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
        $this->media = new \Doctrine\Common\Collections\ArrayCollection();
        $this->brand_items = new \Doctrine\Common\Collections\ArrayCollection();
        $this->translations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->favourite = false;
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
            'Tag' => 'tag',
            'Brand' => 'brand'
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
     * Set favourite
     *
     * @param boolean $favourite
     */
    public function setFavourite($favourite)
    {
        $this->favourite = $favourite;

        return $this;
    }

    /**
     * Get favourite
     *
     * @return boolean 
     */
    public function getFavourite()
    {
        return $this->favourite;
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
     * Add media
     *
     * @param \Maci\MediaBundle\Entity\Media $media
     */
    public function addMedia(\Maci\MediaBundle\Entity\Media $media)
    {
        $this->media[] = $media;

        return $this;
    }

    /**
     * Remove media
     *
     * @param \Maci\MediaBundle\Entity\Media $media
     */
    public function removeMedia(\Maci\MediaBundle\Entity\Media $media)
    {
        $this->media->removeElement($media);
    }

    /**
     * Get media
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * Add brand_items
     *
     * @param \Maci\MediaBundle\Entity\Item $brand_items
     * @return Album
     */
    public function addBrandItem(\Maci\MediaBundle\Entity\Item $brand_items)
    {
        $this->brand_items[] = $brand_items;

        return $this;
    }

    /**
     * Remove brand_items
     *
     * @param \Maci\MediaBundle\Entity\Item $brand_items
     */
    public function removeBrandItem(\Maci\MediaBundle\Entity\Item $brand_items)
    {
        $this->brand_items->removeElement($brand_items);
    }

    /**
     * Get brand_items
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBrandItems()
    {
        return $this->brand_items;
    }

    /**
     * Set preview
     *
     * @param \Maci\MediaBundle\Entity\Media $preview
     * @return Album
     */
    public function setPreview(\Maci\MediaBundle\Entity\Media $preview = null)
    {
        $this->preview = $preview;

        return $this;
    }

    /**
     * Get preview
     *
     * @return \Maci\MediaBundle\Entity\Media 
     */
    public function getPreview()
    {
        return $this->preview;
    }

    public function getWebPreview()
    {
        if ($this->preview) {
            return $this->preview;
        }
        return '/images/defaults/folder-icon.png';
    }

    /**
     * __toString()
     */
    public function __toString()
    {
        return $this->getName();
    }
}

<?php

namespace Maci\PageBundle\Entity\Shop;

/**
 * MediaItem
 */
class MediaItem
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var boolean
     */
    private $favourite;

    /**
     * @var integer
     */
    private $position;

    /**
     * @var \Maci\PageBundle\Entity\Product
     */
    private $product;

    /**
     * @var \Maci\MediaBundle\Entity\Media\Media
     */
    private $media;


    /**
     * Constructor
     */
    public function __construct()
    {
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
     * Set favourite
     *
     * @param boolean $favourite
     * @return Item
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
    public function isFavourite()
    {
        return $this->favourite;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return Item
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
     * Set product
     *
     * @param \Maci\PageBundle\Entity\Product $product
     * @return MediaItem
     */
    public function setProduct(\Maci\PageBundle\Entity\Product $product = null)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return \Maci\PageBundle\Entity\Product 
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set media
     *
     * @param \Maci\MediaBundle\Entity\Media\Media $media
     * @return MediaItem
     */
    public function setMedia(\Maci\MediaBundle\Entity\Media\Media $media = null)
    {
        $this->media = $media;

        return $this;
    }

    /**
     * Get media
     *
     * @return \Maci\MediaBundle\Entity\Media\Media 
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * Get Name
     */
    public function getName()
    {
        return $this->getMedia()->getName();
    }

    /**
     * Get Description
     */
    public function getDescription()
    {
        return $this->getMedia()->getDescription();
    }

    /**
     * __toString()
     */
    public function __toString()
    {
        return $this->getName();
    }
}

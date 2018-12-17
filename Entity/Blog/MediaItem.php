<?php

namespace Maci\PageBundle\Entity\Blog;

/**
 * Item
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
     * @var \Maci\PageBundle\Entity\Blog\Post
     */
    private $post;

    /**
     * @var \Maci\MediaBundle\Entity\Blog\Media
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
     * Set post
     *
     * @param \Maci\PageBundle\Entity\Blog\Post $post
     * @return MediaItem
     */
    public function setPost(\Maci\PageBundle\Entity\Blog\Post $post = null)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * Get post
     *
     * @return \Maci\PageBundle\Entity\Blog\Post 
     */
    public function getPost()
    {
        return $this->post;
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

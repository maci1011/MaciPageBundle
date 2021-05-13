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
     * @var \Maci\PageBundle\Entity\Blog\Media
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
     * Get favourite.
     *
     * @return bool
     */
    public function getFavourite()
    {
        return $this->favourite;
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
     * @param \Maci\PageBundle\Entity\Media\Media $media
     * @return MediaItem
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

    public function getWebPreview()
    {
        if ($this->media) {
            return $this->media->getWebPreview();
        }
        return '/images/defaults/document-icon.png';
    }

    /**
     * __toString()
     */
    public function __toString()
    {
        return 'BlogMediaItem_' . (is_int($this->getId()) ? $this->getId() : 'New');
    }
}

<?php

namespace Maci\PageBundle\Entity\Media;

/**
 * Item
 */
class Item
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
     * @var boolean
     */
    private $favourite;

    /**
     * @var string
     */
    private $link;

    /**
     * @var string
     */
    private $style;

    /**
     * @var string
     */
    private $video;

    /**
     * @var integer
     */
    private $position;

    /**
     * @var \Maci\PageBundle\Entity\Media\Album
     */
    private $album;

    /**
     * @var \Maci\PageBundle\Entity\Media\Tag
     */
    private $brand;

    /**
     * @var \Maci\PageBundle\Entity\Media\Album
     */
    private $gallery;

    /**
     * @var \Maci\PageBundle\Entity\Media\Media
     */
    private $media;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $translations;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->favourite = false;
        $this->translations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->style = 'default';
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
     * @return Item
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
     * @return Item
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

    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set style
     *
     * @param integer $style
     * @return Item
     */
    public function setStyle($style)
    {
        $this->style = $style;

        return $this;
    }

    /**
     * Get style
     *
     * @return string 
     */
    public function getStyle()
    {
        return $this->style;
    }

    public function getStyleArray()
    {
        return array(
            'Default' => 'default',
            'Image & Text - Image' => 'image',
            'Image & Text - Center' => 'center',
            'Image & Text - Center Overlay' => 'center_ov',
            'Image & Text - Center Fix' => 'center_fix',
            'Image & Text - Center Fix Overlay' => 'center_fix_ov',
            'Image & Text - Left' => 'left',
            'Image & Text - Left Overlay' => 'left_ov',
            'Image & Text - Left Divided' => 'left_divided',
            'Image & Text - Right' => 'right',
            'Image & Text - Right Overlay' => 'right_ov',
            'Image & Text - Right Divided' => 'right_divided',
            'Text - Text Only' => 'text_only',
            'Text - Text Only Full' => 'text_only_full',
            'Blog - Last Posts' => 'last_posts',
            'Company' => 'company',
            'Contacts - Contact Info' => 'contact_info',
            'Contacts - Contact Form' => 'contact_form',
            'Contacts - Contact Form Bg' => 'contact_form_bg',
            'Gallery - Album Preview' => 'album_preview',
            'Gallery - Album' => 'album',
            'Gallery - Gallery 3IA' => 'gallery',
            'Gallery - Gallery 2IA' => 'gallery_a',
            'Gallery - Gallery 4IA' => 'gallery_b',
            'Gallery - Gallery 6IA' => 'gallery_c',
            'Gallery - Gallery 2IB' => 'gallery_d',
            'Gallery - Gallery 3IB' => 'gallery_e',
            'Gallery - Gallery 4IB' => 'gallery_f',
            'Gallery - Gallery 6IB' => 'gallery_g',
            'Gallery - Gallery 2CI' => 'gallery_h',
            'Gallery - Gallery 3CI' => 'gallery_i',
            'Gallery - Gallery 4CI' => 'gallery_j',
            'Gallery - Gallery 6CI' => 'gallery_k',
            'Gallery - Slider Default' => 'slider',
            'Gallery - Slider Style One' => 'slider_one',
            'Gallery - Slider Style Two' => 'slider_two',
            'Gallery - Slider Style Three' => 'slider_three',
            'Gallery - Static Image' => 'static_image',
            'Other - One' => 'one',
            'Other - Two' => 'two',
            'Other - Three' => 'three',
            'Other - Foo' => 'foo'
        );
    }

    public function getStyleLabel()
    {
        $array = $this->getStyleArray();
        $key = array_search($this->style, $array);
        if ($key) {
            return $key;
        }
        $str = str_replace('_', ' ', $this->style);
        return ucwords($str);
    }

    public function setVideo($video)
    {
        $this->video = $video;

        return $this;
    }

    public function getVideo()
    {
        return $this->video;
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
     * Set album
     *
     * @param \Maci\PageBundle\Entity\Media\Album $album
     * @return Item
     */
    public function setAlbum(\Maci\PageBundle\Entity\Media\Album $album = null)
    {
        $this->album = $album;

        return $this;
    }

    /**
     * Get album
     *
     * @return \Maci\PageBundle\Entity\Media\Album 
     */
    public function getAlbum()
    {
        return $this->album;
    }

    /**
     * Set gallery
     *
     * @param \Maci\PageBundle\Entity\Media\Album $gallery
     * @return Item
     */
    public function setGallery(\Maci\PageBundle\Entity\Media\Album $gallery = null)
    {
        $this->gallery = $gallery;

        return $this;
    }

    /**
     * Get gallery
     *
     * @return \Maci\PageBundle\Entity\Media\Album 
     */
    public function getGallery()
    {
        return $this->gallery;
    }

    /**
     * Set brand
     *
     * @param \Maci\PageBundle\Entity\Media\Tag $brand
     * @return Item
     */
    public function setBrand(\Maci\PageBundle\Entity\Media\Tag $brand = null)
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * Get brand
     *
     * @return \Maci\PageBundle\Entity\Media\Tag 
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * Set media
     *
     * @param \Maci\PageBundle\Entity\Media\Media $media
     * @return Item
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


    public function getWebPreview()
    {
        if ($this->media) {
            return $this->media->getWebPreview();
        }
        return '/images/defaults/document-icon.png';
    }


    public function getTags()
    {
        if ($this->media) {
            return $this->media->getTags();
        }
        return false;
    }

    /**
     * __toString()
     */
    public function __toString()
    {
        return (strlen($this->getName()) ? $this->getName() : 'MediaItem:' . ($this->getId() ? $this->getId() : 'new'));
    }
}

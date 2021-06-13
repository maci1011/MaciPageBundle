<?php

namespace Maci\PageBundle\Entity\Page;

/**
 * Slide
 */
class Slide
{
	/**
	 * @var integer
	 */
	private $id;

	/**
	 * @var string
	 */
	private $title;

	/**
	 * @var string
	 */
	private $content;

	/**
	 * @var string
	 */
	private $type;

	/**
	 * @var string
	 */
	private $link;

	/**
	 * @var string
	 */
	private $video;

	/**
	 * @var int
	 */
	private $position;

	/**
	 * @var \Maci\PageBundle\Entity\Media\Album
	 */
	private $album;

	/**
	 * @var \Maci\PageBundle\Entity\Media\Media
	 */
	private $media;

	/**
	 * @var \Maci\PageBundle\Entity\Page
	 */
	private $page;

	/**
	 * @var \Maci\PageBundle\Entity\Slide
	 */
	private $parent;

	/**
	 * @var \Doctrine\Common\Collections\Collection
	 */
	private $children;


	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->type = 'default';
		$this->position = 0;
		$this->children = new \Doctrine\Common\Collections\ArrayCollection();
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
	 * Set title
	 *
	 * @param string $title
	 *
	 * @return Slide
	 */
	public function setTitle($title)
	{
		$this->title = $title;

		return $this;
	}

	/**
	 * Get title
	 *
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * Set content
	 *
	 * @param string $content
	 *
	 * @return Slide
	 */
	public function setContent($content)
	{
		$this->content = $content;

		return $this;
	}

	/**
	 * Get content
	 *
	 * @return string
	 */
	public function getContent()
	{
		return $this->content;
	}

	/**
	 * Set type
	 *
	 * @param string $type
	 *
	 * @return Slide
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

	/**
	 * Get Type Array
	 */
	static public function getTypeArray()
	{
		return [
			'Default' => 'default',
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
			'Contacts - Contact Form Overlay' => 'contact_form_ov',
			'Slider, only Indicators' => 'slider',
			'Slider Minimal' => 'slider_one',
			'Slider with Caption' => 'slider_two',
			'Locked Slider' => 'slider_three',
			'Gallery - Album Preview' => 'album_preview',
			'Gallery - Album' => 'album',
			'Gallery - Grid' => 'quads',
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
			'Gallery - Static Image' => 'static_image',
			'Other - One' => 'one',
			'Other - Two' => 'two',
			'Other - Three' => 'three',
			'Other - Foo' => 'foo'
		];
	}

	public function getTypeLabel()
	{
		$array = $this->getTypeArray();
		$key = array_search($this->type, $array);
		if ($key) {
			return $key;
		}
		$str = str_replace('_', ' ', $this->type);
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
	 * Set position
	 *
	 * @param int $position
	 *
	 * @return Slide
	 */
	public function setPosition($position)
	{
		$this->position = $position;

		return $this;
	}

	/**
	 * Get position
	 *
	 * @return int
	 */
	public function getPosition()
	{
		return $this->position;
	}

	/**
	 * Set album
	 *
	 * @param \Maci\PageBundle\Entity\Media\Album $album
	 * @return Slide
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
	 * Set media
	 *
	 * @param string $media
	 *
	 * @return Slide
	 */
	public function setMedia($media)
	{
		$this->media = $media;

		return $this;
	}

	/**
	 * Get media
	 *
	 * @return string
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

	/**
	 * Set page
	 *
	 * @param string $page
	 *
	 * @return Slide
	 */
	public function setPage($page)
	{
		$this->page = $page;

		return $this;
	}

	/**
	 * Get page
	 *
	 * @return string
	 */
	public function getPage()
	{
		return $this->page;
	}

	/**
	 * Set parent
	 *
	 * @param string $parent
	 *
	 * @return Slide
	 */
	public function setParent($parent)
	{
		$this->parent = $parent;

		return $this;
	}

	/**
	 * Get parent
	 *
	 * @return string
	 */
	public function getParent()
	{
		return $this->parent;
	}

	/**
	 * Add children
	 *
	 * @param \Maci\PageBundle\Entity\Page\Slide $children
	 * @return Slide
	 */
	public function addChild(\Maci\PageBundle\Entity\Page\Slide $children)
	{
		$this->children[] = $children;

		return $this;
	}

	/**
	 * Remove children
	 *
	 * @param \Maci\PageBundle\Entity\Page\Slide $children
	 */
	public function removeChild(\Maci\PageBundle\Entity\Page\Slide $children)
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

	public function getSlides()
	{
		return $this->getChildren();
	}

	/**
	 * _toString()
	 */
	public function __toString()
	{
		return 'PageSlide_'.$this->getId();
	}
}

<?php

namespace Maci\PageBundle\Entity\Mailer;

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
	 * @var \Maci\PageBundle\Entity\Media\Media
	 */
	private $media;

	/**
	 * @var \Maci\PageBundle\Entity\Mailer\Mail
	 */
	private $mail;

	/**
	 * @var \Maci\PageBundle\Entity\Mailer\Slide
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
			'Header' => 'header',
			'Gallery' => 'gallery',
			'Foo' => 'foo'
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
	 * Set mail
	 *
	 * @param string $mail
	 *
	 * @return Slide
	 */
	public function setMail($mail)
	{
		$this->mail = $mail;

		return $this;
	}

	/**
	 * Get mail
	 *
	 * @return string
	 */
	public function getMail()
	{
		return $this->mail;
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
	 * @param \Maci\PageBundle\Entity\Mailer\Slide $children
	 * @return Slide
	 */
	public function addChild(\Maci\PageBundle\Entity\Mailer\Slide $children)
	{
		$this->children[] = $children;

		if ($this->type == 'default') {
			$this->type = 'gallery';
		}

		return $this;
	}

	/**
	 * Remove children
	 *
	 * @param \Maci\PageBundle\Entity\Mailer\Slide $children
	 */
	public function removeChild(\Maci\PageBundle\Entity\Mailer\Slide $children)
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
	 * _toString()
	 */
	public function __toString()
	{
		return 'MailSlide_'.$this->getId();
	}
}

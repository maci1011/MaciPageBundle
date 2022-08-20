<?php

namespace Maci\PageBundle\Entity\Blog;

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
	 * @var int
	 */
	private $position;

	/**
	 * @var \Maci\PageBundle\Entity\Media\Media
	 */
	private $preview;

	/**
	 * @var \Maci\PageBundle\Entity\Blog\Post
	 */
	private $post;


	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->type = 'default';
		$this->position = 0;
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
			'Default' => 'default'
			// 'Foo' => 'foo',
		];
	}

	public function getTypeLabel()
	{
		return \Maci\PageBundle\MaciPageBundle::getLabel($this->type, $this->getTypeArray());
	}

	static public function getTypes()
	{
		return array_values(Slide::getTypeArray());
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
	 * Set preview
	 *
	 * @param \Maci\PageBundle\Entity\Media\Media $preview
	 * @return Slide
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
		if (!$this->preview)
			return '/images/defaults/document-icon.png';

		return $this->preview->getWebPath();
	}

	/**
	 * Set post
	 *
	 * @param \Maci\PageBundle\Entity\Blog\Post $post
	 * @return Item
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
	 * _toString()
	 */
	public function __toString()
	{
		return 'MailSlide_'.$this->getId();
	}
}

<?php

namespace Maci\PageBundle\Entity\Blog;

/**
 * Related
 */
class Related
{
	/**
	 * @var int
	 */
	private $id;

	/**
	 * @var string
	 */
	private $type;

	/**
	 * @var int|null
	 */
	private $position;

	/**
	 * @var \Maci\PageBundle\Entity\Blog\Post
	 */
	private $sourcePost;

	/**
	 * @var \Maci\PageBundle\Entity\Blog\Post
	 */
	private $targetPost;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->type = 'prev';
	}


	/**
	 * Get id.
	 *
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Set type.
	 *
	 * @param string $type
	 *
	 * @return Related
	 */
	public function setType($type)
	{
		$this->type = $type;

		return $this;
	}

	/**
	 * Get type.
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
	public function getTypeArray()
	{
		return [
			'Prev Post' => 'prev',
			'Next Post' => 'next',
			'Related' => 'related'
		];
	}

	public function getTypeLabel()
	{
		return \Maci\PageBundle\MaciPageBundle::getLabel($this->type, $this->getTypeArray());
	}

	static public function getTypes()
	{
		return array_values(Related::getTypeArray());
	}

	/**
	 * Set position.
	 *
	 * @param int|null $position
	 *
	 * @return Related
	 */
	public function setPosition($position = null)
	{
		$this->position = $position;

		return $this;
	}

	/**
	 * Get position.
	 *
	 * @return int|null
	 */
	public function getPosition()
	{
		return $this->position;
	}

	/**
	 * Set sourcePost.
	 *
	 * @param \Maci\PageBundle\Entity\Blog\Post|null $sourcePost
	 *
	 * @return Related
	 */
	public function setSourcePost(\Maci\PageBundle\Entity\Blog\Post $sourcePost = null)
	{
		$this->sourcePost = $sourcePost;

		return $this;
	}

	/**
	 * Get sourcePost.
	 *
	 * @return \Maci\PageBundle\Entity\Blog\Post|null
	 */
	public function getSourcePost()
	{
		return $this->sourcePost;
	}

	/**
	 * Get Source Name
	 *
	 * @return string
	 */
	public function getSourceName()
	{
		if ($this->sourcePost) return $this->sourcePost->getTitle();
	}

	/**
	 * Set targetPost.
	 *
	 * @param \Maci\PageBundle\Entity\Blog\Post|null $targetPost
	 *
	 * @return Related
	 */
	public function setTargetPost(\Maci\PageBundle\Entity\Blog\Post $targetPost = null)
	{
		$this->targetPost = $targetPost;

		return $this;
	}

	/**
	 * Get targetPost.
	 *
	 * @return \Maci\PageBundle\Entity\Blog\Post|null
	 */
	public function getTargetPost()
	{
		return $this->targetPost;
	}

	/**
	 * Get Target Name
	 *
	 * @return string
	 */
	public function getTargetName()
	{
		if ($this->targetPost) return $this->targetPost->getTitle();
	}

	/**
	 * __toString()
	 */
	public function __toString()
	{
		return 'Related_' . $this->getId();
	}
}

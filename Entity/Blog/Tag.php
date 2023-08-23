<?php

namespace Maci\PageBundle\Entity\Blog;

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
	private $path;

	/**
	 * @var string
	 */
	private $description;

	/**
	 * @var boolean
	 */
	private $favourite;

	/**
	 * @var \Doctrine\Common\Collections\Collection
	 */
	private $posts;

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
		$this->path = uniqid();
		$this->posts = new \Doctrine\Common\Collections\ArrayCollection();
		$this->translations = new \Doctrine\Common\Collections\ArrayCollection();
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
	 * @return TagTranslation
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
	 * Set path
	 *
	 * @param string $path
	 * @return Page
	 */
	public function setPath($path)
	{
		$this->path = $path;

		return $this;
	}

	/**
	 * Get path
	 *
	 * @return string 
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * Set description
	 *
	 * @param string $description
	 * @return TagTranslation
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
	 * @return Post
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

	/**
	 * Add posts
	 *
	 * @param \Maci\PageBundle\Entity\Blog\Post $posts
	 * @return Post
	 */
	public function addPost(\Maci\PageBundle\Entity\Blog\Post $posts)
	{
		$this->posts[] = $posts;

		return $this;
	}

	/**
	 * Remove posts
	 *
	 * @param \Maci\PageBundle\Entity\Blog\Post $posts
	 */
	public function removePost(\Maci\PageBundle\Entity\Blog\Post $posts)
	{
		$this->posts->removeElement($posts);
	}

	/**
	 * Get posts
	 *
	 * @return \Doctrine\Common\Collections\Collection 
	 */
	public function getPosts()
	{
		return $this->posts;
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
	 * __toString()
	 */
	public function __toString()
	{
		return $this->name ? $this->name : 'New Tag';
	}
}

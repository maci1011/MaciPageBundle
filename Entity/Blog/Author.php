<?php

namespace Maci\PageBundle\Entity\Blog;

/**
 * Author
 */
class Author
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
	private $path;

	/**
	 * @var string
	 */
	private $locale;

	/**
	 * @var \Maci\UserBundle\Entity\User
	 */
	private $user;

	/**
	 * @var \Maci\PageBundle\Entity\Media\Media
	 */
	private $preview;

	/**
	 * @var \Doctrine\Common\Collections\Collection
	 */
	private $editors;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->path = uniqid();
		$this->editors = new \Doctrine\Common\Collections\ArrayCollection();
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
	 * @return Author
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
	 * @return Author
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
	 * Set path
	 *
	 * @param string $path
	 * @return Author
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
	 * Set user
	 *
	 * @param \Maci\UserBundle\Entity\User $user
	 * @return Post
	 */
	public function setUser(\Maci\UserBundle\Entity\User $user = null)
	{
		$this->user = $user;

		return $this;
	}

	/**
	 * Get user
	 *
	 * @return \Maci\UserBundle\Entity\User 
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * Set preview
	 *
	 * @param \Maci\PageBundle\Entity\Media\Media $preview
	 * @return Author
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
		if (!$this->preview) {
			return '/images/defaults/document-icon.png';
		}
		return $this->preview->getWebPath();
	}

	/**
	 * Add editors
	 *
	 * @param \Maci\PageBundle\Entity\Blog\Editor $editors
	 * @return Author
	 */
	public function addEditor(\Maci\PageBundle\Entity\Blog\Editor $editors)
	{
		$this->editors[] = $editors;

		return $this;
	}

	/**
	 * Remove editors
	 *
	 * @param \Maci\PageBundle\Entity\Blog\Editor $editors
	 */
	public function removeEditor(\Maci\PageBundle\Entity\Blog\Editor $editors)
	{
		$this->editors->removeElement($editors);
	}

	/**
	 * Get editors
	 *
	 * @return \Doctrine\Common\Collections\Collection 
	 */
	public function getEditors()
	{
		return $this->editors;
	}

	/**
	 * __toString()
	 */
	public function __toString()
	{
		return 'BlogAuthor#' . ($this->id ? $this->id : 'New');
	}
}

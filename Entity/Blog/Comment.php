<?php

namespace Maci\PageBundle\Entity\Blog;

/**
 * Comment
 */
class Comment
{
	/**
	 * @var string
	 */
	private $hash;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $email;

	/**
	 * @var string
	 */
	private $content;

	/**
	 * @var boolean
	 */
	private $approved;

	/**
	 * @var boolean
	 */
	private $removed;

	/**
	 * @var datetime
	 */
	private $created;

	/**
	 * @var \Maci\PageBundle\Entity\Blog\Comment
	 */
	private $parent;

	/**
	 * @var \Maci\PageBundle\Entity\Blog\Post
	 */
	private $post;

	/**
	 * @var \Maci\UserBundle\Entity\User
	 */
	private $user;

	/**
	 * @var \Doctrine\Common\Collections\Collection
	 */
	private $children;


	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->hash = uniqid();
		$this->approved = false;
		$this->removed = false;
	}


	/**
	 * Get hash
	 *
	 * @return string
	 */
	public function getHash()
	{
		return $this->hash;
	}

	/**
	 * Set name
	 *
	 * @param string $name
	 *
	 * @return Comment
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
	 * Set email
	 *
	 * @param string $email
	 *
	 * @return Comment
	 */
	public function setEmail($email)
	{
		$this->email = $email;

		return $this;
	}

	/**
	 * Get email
	 *
	 * @return string
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * Set content
	 *
	 * @param string $content
	 *
	 * @return Comment
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
	 * Set approved
	 *
	 * @param boolean $approved
	 *
	 * @return Comment
	 */
	public function setApproved($approved)
	{
		$this->approved = $approved;

		return $this;
	}

	/**
	 * Get approved
	 *
	 * @return boolean
	 */
	public function getApproved()
	{
		return $this->approved;
	}

	/**
	 * Set removed
	 *
	 * @param boolean $removed
	 *
	 * @return Comment
	 */
	public function setRemoved($removed)
	{
		$this->removed = $removed;

		return $this;
	}

	/**
	 * Get removed
	 *
	 * @return boolean
	 */
	public function getRemoved()
	{
		return $this->removed;
	}

	/**
	 * Set created
	 *
	 * @param \DateTime $created
	 * @return Post
	 */
	public function setCreated($created)
	{
		$this->created = $created;

		return $this;
	}

	/**
	 * setCreatedValue
	 */
	public function setCreatedValue()
	{
		$this->created = new \DateTime();
	}

	/**
	 * Get created
	 *
	 * @return \DateTime 
	 */
	public function getCreated()
	{
		return $this->created;
	}

	/**
	 * Set parent
	 *
	 * @param \Maci\PageBundle\Entity\Blog\Comment $parent
	 * @return Item
	 */
	public function setParent(\Maci\PageBundle\Entity\Blog\Comment $parent = null)
	{
		$this->parent = $parent;

		return $this;
	}

	/**
	 * Get parent
	 *
	 * @return \Maci\PageBundle\Entity\Blog\Comment 
	 */
	public function getParent()
	{
		return $this->parent;
	}

	public function addChild(\Maci\PageBundle\Entity\Blog\Comment $child)
	{
		$this->children[] = $child;

		return $this;
	}

	public function removeChild(\Maci\PageBundle\Entity\Blog\Comment $child)
	{
		$this->children->removeElement($child);
	}

	public function getChildren()
	{
		return $this->children;
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
	 * Get name
	 *
	 * @return string
	 */
	public function getUsername()
	{
		return $this->user ? $this->user->getUsername() : $this->name;
	}

	/**
	 * _toString()
	 */
	public function __toString()
	{
		return 'BlogComment#' . $this->hash;
	}
}

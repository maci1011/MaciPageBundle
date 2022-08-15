<?php

namespace Maci\PageBundle\Entity\Blog;

/**
 * Editor
 */
class Editor
{
	/**
	 * @var integer
	 */
	private $id;

	/**
	 * @var string
	 */
	private $type;

	/**
	 * @var integer
	 */
	private $position;

	/**
	 * @var \Maci\PageBundle\Entity\Blog\Post
	 */
	private $post;

	/**
	 * @var \Maci\PageBundle\Entity\Blog\Author
	 */
	private $author;


	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->type = '_author';
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
	 * Set type
	 *
	 * @param string $type
	 * @return Editor
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
			'Author' => '_author',
			'Translator' => 'transla'
		];
	}

	public function getTypeLabel()
	{
		return \Maci\PageBundle\MaciPageBundle::getLabel($this->type, $this->getTypeArray());
	}

	static public function getTypes()
	{
		return array_values(Editor::getTypeArray());
	}

	public function isAuthor()
	{
		return $this->type == '_author';
	}

	public function isTranslator()
	{
		return $this->type == 'transla';
	}

	/**
	 * Set position
	 *
	 * @param integer $position
	 * @return Editor
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
	 * @return Editor
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
	 * Set author
	 *
	 * @param \Maci\PageBundle\Entity\Blog\Author $author
	 * @return Post
	 */
	public function setAuthor(\Maci\PageBundle\Entity\Blog\Author $author = null)
	{
		$this->author = $author;

		return $this;
	}

	/**
	 * Get author
	 *
	 * @return \Maci\PageBundle\Entity\Blog\Author 
	 */
	public function getAuthor()
	{
		return $this->author;
	}

	/**
	 * Get Name
	 */
	public function getName()
	{
		return $this->author ? $this->author->getName() : null;
	}

	/**
	 * __toString()
	 */
	public function __toString()
	{
		return 'BlogEditor_' . (is_int($this->getId()) ? $this->getId() : 'New');
	}
}

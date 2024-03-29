<?php

namespace Maci\PageBundle\Entity\Shop;

/**
 * CategoryItem
 */
class CategoryItem
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
	 * @var \Maci\PageBundle\Entity\Shop\Product
	 */
	private $product;

	/**
	 * @var \Maci\CategoryBundle\Entity\Shop\Category
	 */
	private $category;


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
		return $this->getFavourite();
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
	 * Set product
	 *
	 * @param  Product $product
	 * @return CategoryItem
	 */
	public function setProduct(\Maci\PageBundle\Entity\Shop\Product $product = null)
	{
		$this->product = $product;

		return $this;
	}

	/**
	 * Get product
	 *
	 * @return Product 
	 */
	public function getProduct()
	{
		return $this->product;
	}

	/**
	 * Set category
	 *
	 * @param Category $category
	 * @return CategoryItem
	 */
	public function setCategory(\Maci\PageBundle\Entity\Shop\Category $category = null)
	{
		$this->category = $category;

		return $this;
	}

	/**
	 * Get category
	 *
	 * @return Category 
	 */
	public function getCategory()
	{
		return $this->category;
	}

	/**
	 * Get category name
	 */
	public function getCategoryName()
	{
		return $this->category ? $this->category->getName() : '';
	}

	/**
	 * Get category name
	 */
	public function getName()
	{
		return "CategoryItem#" . ($this->id ?? 'new');
	}

	/**
	 * __toString()
	 */
	public function __toString()
	{
		return $this->getName();
	}
}

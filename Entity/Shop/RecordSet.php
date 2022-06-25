<?php

namespace Maci\PageBundle\Entity\Shop;

/**
 * Record
 */
class RecordSet
{
	/**
	 * @var integer
	 */
	private $id;

	/**
	 * @var string
	 */
	private $label;

	/**
	 * @var string
	 */
	private $description;

	/**
	 * @var string
	 */
	private $type;

	/**
	 * @var \DateTime
	 */
	private $recorded;

	/**
	 * @var \Doctrine\Common\Collections\Collection
	 */
	private $children;

	/**
	 * @var \Maci\PageBundle\Entity\Shop\SetCategory
	 */
	private $category;

	/**
	 * @var \Maci\PageBundle\Entity\Shop\Supplier
	 */
	private $supplier;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->children = new \Doctrine\Common\Collections\ArrayCollection();
		$this->type = $this->getTypes()[0];
		$this->recorded = new \DateTime();
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
	 * Set label
	 *
	 * @param string $label
	 * @return Record
	 */
	public function setLabel($label)
	{
		$this->label = $label;
	
		return $this;
	}

	/**
	 * Get label
	 *
	 * @return string 
	 */
	public function getLabel()
	{
		return $this->label;
	}

	/**
	 * Set description
	 *
	 * @param string $description
	 * @return Record
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
	 * Set type
	 *
	 * @param string $type
	 * @return Product
	 */
	public function setType($type)
	{
		$this->type = $type;

		if ($type == $this->getTypes()[1]) $this->setVariantType('color-n-size');

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
			'Import' => 'imprt',
			'Export' => 'exprt'
		];
	}

	public function getTypeLabel()
	{
		return \Maci\PageBundle\MaciPageBundle::getLabel($this->type, $this->getTypeArray());
	}

	static public function getTypes()
	{
		return array_values(RecordSet::getTypeArray());
	}

	/**
	 * Set recorded
	 *
	 * @param string $recorded
	 * @return Record
	 */
	public function setRecorded($recorded)
	{
		$this->recorded = $recorded;
	
		return $this;
	}

	/**
	 * Get recorded
	 *
	 * @return datetime 
	 */
	public function getRecorded()
	{
		return $this->recorded;
	}

	public function addChild(\Maci\PageBundle\Entity\Shop\Record $child)
	{
		$this->children[] = $child;

		return $this;
	}

	public function removeChild(\Maci\PageBundle\Entity\Shop\Record $child)
	{
		$this->children->removeElement($child);
	}

	public function getChildren()
	{
		return $this->children;
	}

	public function setCategory(\Maci\PageBundle\Entity\Shop\SetCategory $category = null)
	{
		$this->category = $category;

		return $this;
	}

	public function getCategory()
	{
		return $this->category;
	}

	public function setSupplier(\Maci\PageBundle\Entity\Shop\Supplier $supplier = null)
	{
		$this->supplier = $supplier;

		return $this;
	}

	public function getSupplier()
	{
		return $this->supplier;
	}

	/**
	 * __toString()
	 */
	public function __toString()
	{
		return 'RecordSet#' . ($this->id ? $this->id : 'new');
	}
}

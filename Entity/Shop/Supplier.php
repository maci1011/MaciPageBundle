<?php

namespace Maci\PageBundle\Entity\Shop;

/**
 * Supplier
 */
class Supplier
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
	 * @var \Doctrine\Common\Collections\Collection
	 */
	private $recordSets;

	/**
	 * @var \Maci\UserBundle\Entity\Address
	 */
	private $address;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->recordSets = new \Doctrine\Common\Collections\ArrayCollection();
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

	public function addRecordSet(\Maci\PageBundle\Entity\Shop\RecordSet $recordset)
	{
		$this->recordSets[] = $recordset;

		return $this;
	}

	public function removeRecordSet(\Maci\PageBundle\Entity\Shop\RecordSet $recordset)
	{
		$this->recordSets->removeElement($recordset);
	}

	public function getRecordSets()
	{
		return $this->recordSets;
	}

	public function setAddress(\Maci\UserBundle\Entity\Address $address = null)
	{
		$this->address = $address;

		return $this;
	}

	public function getAddress()
	{
		return $this->address;
	}

	/**
	 * __toString()
	 */
	public function __toString()
	{
		return 'Supplier#' . ($this->label ? $this->label : 'new');
	}
}

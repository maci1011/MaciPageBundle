<?php

namespace Maci\PageBundle\Entity\Shop;

/**
 * SetCategory
 */
class SetCategory
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

	/**
	 * __toString()
	 */
	public function __toString()
	{
		return 'SetCategory#' . ($this->label ? $this->label : 'new');
	}
}

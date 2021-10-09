<?php

namespace Maci\PageBundle\Entity\Shop;

/**
 * Record
 */
class Record
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
	 * @var string
	 */
	private $label;

	/**
	 * @var decimal
	 */
	private $price;

	/**
	 * @var integer
	 */
	private $quantity;

	/**
	 * @var json
	 */
	private $data;

	/**
	 * @var \DateTime
	 */
	private $recorded;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->type = $this->getTypeArray()[0];
		$this->price = 0;
		$this->quantity = 1;
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
	 * @return Record
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
			'Unknown' => 'unknown',
			'Purchase' => 'purchas',
			'Sale' => 'sale',
			'Returned' => 'returne'
		];
	}

	public function getTypeLabel()
	{
		$array = $this->getTypeArray();
		$key = array_search($this->type, $array);
		if ($key) {
			return $key;
		}
		$str = str_replace('_', ' ', $this->type);
		return ucwords($str);
	}

	static public function getTypes()
	{
		return array_values(Mail::getTypeArray());
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
	 * Set price
	 *
	 * @param string $price
	 * @return Record
	 */
	public function setPrice($price)
	{
		$this->price = $price;

		return $this;
	}

	/**
	 * Get price
	 *
	 * @return string 
	 */
	public function getPrice()
	{
		return $this->price;
	}

	/**
	 * Set quantity
	 *
	 * @param string $quantity
	 * @return Record
	 */
	public function setQuantity($quantity)
	{
		$this->quantity = $quantity;

		return $this;
	}

	/**
	 * Get quantity
	 *
	 * @return string 
	 */
	public function getQuantity()
	{
		return $this->quantity;
	}

	/**
	 * Set data
	 *
	 * @param string $data
	 * @return Product
	 */
	public function setData($data)
	{
		$this->data = $data;

		return $this;
	}

	/**
	 * Get data
	 *
	 * @return string 
	 */
	public function getData()
	{
		return $this->data;
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

	/**
	 * setRecordedValue
	 */
	public function setRecordedValue()
	{
		$this->recorded = new \DateTime();
	}

	/**
	 * __toString()
	 */
	public function __toString()
	{
		return 'Record#' . ($this->id ? $this->id : 'new');
	}
}

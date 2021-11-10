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
	private $code;

	/**
	 * @var string
	 */
	private $barcode;

	/**
	 * @var string
	 */
	private $brand;

	/**
	 * @var string
	 */
	private $category;

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
	 * @var \Maci\PageBundle\Entity\Shop\Record
	 */
	private $parent;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->type = $this->getTypes()[0];
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
			'Unset' => 'unset',
			'Purchase' => 'purchas',
			'Sale' => 'sale',
			'Return' => 'return'
		];
	}

	public function getTypeLabel()
	{
		if($this->type == "")
			return array_search($this->getTypes()[0], $this->getTypeArray());
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
		return array_values(Record::getTypeArray());
	}

	/**
	 * Set code
	 *
	 * @param string $code
	 * @return Record
	 */
	public function setCode($code)
	{
		$this->code = $code;

		return $this;
	}

	/**
	 * Get code
	 *
	 * @return string 
	 */
	public function getCode()
	{
		return $this->code;
	}

	/**
	 * Set barcode
	 *
	 * @param string $barcode
	 * @return Record
	 */
	public function setBarcode($barcode)
	{
		$this->barcode = $barcode;

		return $this;
	}

	/**
	 * Get barcode
	 *
	 * @return string 
	 */
	public function getBarcode()
	{
		return $this->barcode;
	}

	/**
	 * Set brand
	 *
	 * @param string $brand
	 * @return Record
	 */
	public function setBrand($brand)
	{
		$this->brand = $brand;

		return $this;
	}

	/**
	 * Get brand
	 *
	 * @return string 
	 */
	public function getBrand()
	{
		return $this->brand;
	}

	/**
	 * Set category
	 *
	 * @param string $category
	 * @return Record
	 */
	public function setCategory($category)
	{
		$this->category = $category;
	
		return $this;
	}

	/**
	 * Get category
	 *
	 * @return string 
	 */
	public function getCategory()
	{
		return $this->category;
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

	public function getPriceLabel()
	{
		return number_format($this->price, 2);
	}

	/**
	 * Get total price
	 *
	 * @return string 
	 */
	public function getTotalPrice()
	{
		return $this->price * $this->quantity;
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

	public function import($data)
	{
		// Not used:
		// "Descr.Cat.Comm.": "Pantalone"
		// "ID dogan.": "61046300"
		foreach($data as $key => $value) {
			switch ($key) {
				case 'Articolo':
					$this->code = $value;
					break;
				case 'BARCODE13':
					$this->barcode = $value;
					break;
				case 'Descr.Colore':
					$this->addVariant('Color', $value);
					break;
				case 'Descr.Marchio':
					$this->brand = $value;
					break;
				case 'Descr.Cat.Mer.':
					$this->category = $value;
					break;
				case 'Tgl':
					$this->addVariant('Size', $value);
					break;
				case 'Uni:XXEUR025':
					$this->price = (intval(floatval($value) * 0.26) + 1) * 10;
					break;
				case 'Quantità':
					$this->quantity = intval($value);
					break;
				default:
					break;
			}
		}

		if($this->data == null) $this->data = [];
		$this->data['imported'] = $data;

		return $this;
	}

	/**
	 * Get data
	 *
	 * @return string 
	 */
	public function getData()
	{
		if (!is_array($this->data))
		{
			$this->data = [];
		}

		return $this->data;
	}

	public function getImported()
	{
		if (!array_key_exists('imported', $this->getData()))
		{
			return false;
		}

		return $this->data['imported'];
	}

	public function getImportedLocale()
	{
		if (!$this->getImported()) return null;
		if (array_key_exists('M.In', $this->data['imported'])) return strtolower($this->data['imported']['M.In']);
		return null;
	}

	public function getImportedCurrency()
	{
		if (!$this->getImported()) return null;
		if (array_key_exists('Uni:XXEUR025', $this->data['imported'])) return 'EUR';
		return null;
	}

	public function getImportedComposition()
	{
		if (!$this->getImported()) return null;
		if (array_key_exists('Composizione', $this->data['imported'])) return $this->data['imported']['Composizione'];
		return null;
	}

	public function getImportedPrice()
	{
		if (!$this->getImported()) return null;
		if (array_key_exists('Uni:XXEUR025', $this->data['imported'])) return number_format(floatval($this->data['imported']['Uni:XXEUR025']), 2);
		return null;
	}

	public function getBuyed()
	{
		return $this->getImportedPrice();
	}

	public function getImportedTotal()
	{
		if (!$this->getImported()) return null;
		if (array_key_exists('Tot:XXEUR025', $this->data['imported'])) return number_format(floatval($this->data['imported']['Tot:XXEUR025']), 2);
		return null;
	}

	public function getVariants()
	{
		if (!array_key_exists('variants', $this->getData()))
		{
			$this->data['variants'] = [];
		}

		return $this->data['variants'];
	}

	public function getVariantsLabel()
	{
		$len = count($this->getVariants());
		if ($len == 0) return '';
		$label = implode(': ', $this->data['variants'][0]);
		if ($len == 1) return $label;
		for ($i = 1; $i < $len; $i++)
		{
			$label = $label . ", " . implode(': ', $this->data['variants'][$i]);
		}
		return $label;
	}

	public function addVariant($label, $value)
	{
		$index = count($this->getVariants());
		$this->data['variants'][$index]['label'] = $label;
		$this->data['variants'][$index]['value'] = $value;
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

	public function setParent(\Maci\PageBundle\Entity\Shop\RecordSet $parent = null)
	{
		$this->parent = $parent;

		return $this;
	}

	public function getParent()
	{
		return $this->parent;
	}

	public function getParentLabel()
	{
		return $this->parent ? $this->parent->getLabel() : '';
	}

	/**
	 * __toString()
	 */
	public function __toString()
	{
		return 'Record#' . ($this->id ? $this->id : 'new');
	}
}

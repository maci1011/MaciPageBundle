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

	public function getEan13Barcode()
	{
		return $this->getEan13($this->barcode);
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

	public function getEan13Price()
	{
		return $this->getEan13((string)($this->price * 100));
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

	public function getDiffQuantity()
	{
		if ($this->type == 'sale') return -$this->quantity;
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
		if (!is_array($this->data))
		{
			return [];
		}

		return $this->data;
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
				case 'Descr.Marchio':
					$this->brand = $value;
					break;
				case 'Descr.Cat.Mer.':
					$this->category = $value;
					break;
				case 'Prz.Lordo':
				case 'Uni:XXEUR025':
					$this->price = $value;
					break;
				case 'Quantità':
					$this->quantity = floatval($value);
					break;
				default:
					break;
			}
		}

		if($this->data == null) $this->data = [];
		$this->data['imported'] = $data;
		$this->setVariant($data);

		return $this;
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
		if (!$this->getImported()) return false;
		if (array_key_exists('M.In', $this->data['imported'])) return strtolower($this->data['imported']['M.In']);
		return false;
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

	public function hasVariant()
	{
		return is_array($this->data) && array_key_exists('variant', $this->data) && is_array($this->data['variant']);
	}

	public function getVariant()
	{
		if (!$this->hasVariant()) return ['type' => 'unset'];
		return $this->data['variant'];
	}

	public function setVariant($data)
	{
		$variant = false;
		if(array_key_exists('Descr.Colore', $data))
		{
			$variant['type'] = 'color-n-size';
			$variant['color'] = $data['Descr.Colore'];
			$variant['name'] = $data['Tgl'];
		}
		else if(array_key_exists('type', $data)) $variant = $data;
		$this->data['variant'] = $variant;
	}

	public function getVariantLabel()
	{
		$data = $this->getData();
		if(!array_key_exists('variant', $data)) return '';
		if($data['variant']['type'] == 'color-n-size')
			return $data['variant']['color'] . " - " . $data['variant']['name'];
		return $data['variant']['type'];
	}

	public function getProductVariant()
	{
		$variant = $this->getVariant();
		if($variant['type'] == 'unset') return null;
		else if($variant['type'] == 'color-n-size') return $variant['color'];
		return null;
	}

	public function getVariantType()
	{
		return $this->getVariant()['type'];
	}

	public function isLoaded()
	{
		return array_key_exists('loaded', $this->getData());
	}

	public function getLoadedValue()
	{
		if (!$this->isLoaded())
		{
			return false;
		}

		return $this->data['loaded'];
	}

	public function setLoadedValue()
	{
		$this->data['loaded'] = date_format(new \DateTime(), 'r');
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

	public static function getEan13($chaine)
	{
		if (!is_string($chaine) || !strlen($chaine))
		{
			return null;
		}
		if (strlen($chaine) < 12)
		{
			while (strlen($chaine) < 12)
			{
				$chaine = '0' . $chaine;
			}
		}
		else if (strlen($chaine) == 13)
		{
			$chaine = substr($chaine, 1, 12);
		}

		if (strlen($chaine) != 12)
		{
			return null;
		}

		$isValid = true;

		for($i = 0; $i < 13; $i++)
		{
			if (ord($chaine[0]) < 48 || ord($chaine[0]) > 57)
			{
				$isValid = false;
				break;
			}
		}

		if (!$isValid)
		{
			return null;
		}

		$checksum = 0;
		$first = intval($chaine[0]);
		$code = $chaine[0] . chr(65 + intval($chaine[1]));
		$tableA = false;
		$ean13 = "";

		for ($i = 12; 0 < $i; $i-=2) $checksum = $checksum + intval($chaine[0]);
		$checksum = $checksum * 3;
		for ($i = 11; 0 < $i; $i-=2) $checksum = $checksum + intval($chaine[0]);

		$chaine = $chaine . (10 - $checksum % 10) % 10;

		for ($i = 2; $i < 7; $i++)
		{
			$tableA = false;
			switch ($i)
			{
				case 2:
					switch ($first)
					{
						case 0:
						case 1:
						case 2:
						case 3:
							$tableA = true;
							break;
					}
					break;
				case 3:
					switch ($first)
					{
						case 0:
						case 4:
						case 7:
						case 8:
							$tableA = true;
							break;
					}
					break;
				case 4:
					switch ($first)
					{
						case 0:
						case 1:
						case 4:
						case 5:
						case 9:
							$tableA = true;
							break;
					}
					break;
				case 5:
					switch ($first)
					{
						case 0:
						case 2:
						case 5:
						case 6:
						case 7:
							$tableA = true;
							break;
					}
					break;
				case 6:
					switch ($first)
					{
						case 0:
						case 3:
						case 6:
						case 8:
						case 9:
							$tableA = true;
							break;
					}
					break;
			}

			If ($tableA) $code .= chr(65 + intval($chaine[$i]));
			else $code .= chr(75 + intval($chaine[$i]));
		}

		$code .= "*";
		for ($i = 7; $i < 13; $i++) $code .= chr(97 + intval($chaine[$i]));
		$code .= "+";

		return $code;
	}

	/**
	 * __toString()
	 */
	public function __toString()
	{
		return 'Record#' . ($this->id ? $this->id : 'new');
	}
}

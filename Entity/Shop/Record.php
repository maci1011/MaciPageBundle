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
			'Customer Return' => 'return',
			'Supplier Return' => 'back'
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
		return \Maci\PageBundle\MaciPageBundle::getEan13($this->barcode);
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
		return \Maci\PageBundle\MaciPageBundle::getEan13(
			'2000030' . ($this->price < 100 ? '0' : '') . intval($this->price * 100)
		);
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
		if ($this->type == 'unset') return 0;
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
		foreach($data as $key => $value)
		{
			$opt = strtolower($key);
			$opt = str_replace('.', '', $key);
			$opt = str_replace(':', '', $key);
			if (7 < strlen($opt)) $opt = substr($opt, 0, 7);

			switch ($opt)
			{
				case 'articol':
				case 'codice':
				case 'codart':
					$this->code = $value;
					break;
				case 'barcode':
					$this->barcode = $value;
					break;
				case 'descrma':
				case 'marchio':
				case 'brand':
					$this->brand = $value;
					break;
				case 'descrca':
				case 'descriz':
				case 'titolo':
				case 'title':
				case 'categor':
					$this->category = $value;
					break;
				case 'prezzo':
				case 'przlord':
				case 'unixxeu':
				case 'price':
					$this->price = floatval(str_replace(',', '.', $value));
					break;
				case 'quantit':
				case 'qtà':
				case 'qta':
				case 'qty':
					$this->quantity = intval($value);
					break;
			}
		}

		if($this->data == null)
			$this->data = [];

		$this->data['imported'] = $data;
		$this->setVariant($data);
	}

	public function reload()
	{
		if (!is_array($this->data) || !array_key_exists('imported', $this->data))
			return false;

		$this->import($this->data['imported']);
		return true;
	}

	public function getImported()
	{
		if (!array_key_exists('imported', $this->getData()))
			return false;

		return $this->data['imported'];
	}

	public function getImportedLocale()
	{
		if (!$this->getImported()) return false;
		if (array_key_exists('Locale', $this->data['imported']))
			return strtolower($this->data['imported']['Locale']);
		return false;
	}

	public function getImportedCurrency()
	{
		if (!$this->getImported()) return null;
		if (array_key_exists('Uni:XXEUR025', $this->data['imported']))
			return 'EUR';
		if (array_key_exists('Currency', $this->data['imported']))
			return $this->data['imported']['Currency'];
		return 'EUR';
	}

	public function getImportedComposition()
	{
		if (!$this->getImported()) return null;
		if (array_key_exists('Composizione', $this->data['imported']))
			return $this->data['imported']['Composizione'];
		if (array_key_exists('Composition', $this->data['imported']))
			return $this->data['imported']['Composition'];
		return null;
	}

	public function getImportedPrice()
	{
		if (!$this->getImported()) return null;
		if (array_key_exists('Uni:XXEUR025', $this->data['imported']))
			return number_format(floatval($this->data['imported']['Uni:XXEUR025']), 2);
		if (array_key_exists('Prz.Lordo', $this->data['imported']))
			return number_format(floatval($this->data['imported']['Prz.Lordo']), 2);
		if (array_key_exists('Prezzo', $this->data['imported']))
			return number_format(floatval($this->data['imported']['Prezzo']), 2);
		if (array_key_exists('Price', $this->data['imported']))
			return number_format(floatval($this->data['imported']['Price']), 2);
		return null;
	}

	public function getBuyed()
	{
		return $this->getImportedPrice();
	}

	public function getImportedTotal()
	{
		if (!$this->getImported()) return null;
		if (array_key_exists('Tot:XXEUR025', $this->data['imported']))
			return number_format(floatval($this->data['imported']['Tot:XXEUR025']), 2);
		if (array_key_exists('Valore', $this->data['imported']))
			return number_format(floatval($this->data['imported']['Valore']), 2);
		if (array_key_exists('Amount', $this->data['imported']))
			return number_format(floatval($this->data['imported']['Amount']), 2);
		return null;
	}

	public function hasVariant()
	{
		return is_array($this->data) && array_key_exists('variant', $this->data) && is_array($this->data['variant']);
	}

	public function getVariant()
	{
		if (!$this->hasVariant())
			return ['type' => 'unset'];
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

		else if(array_key_exists('Colore', $data))
		{
			$variant['type'] = 'color-n-size';
			$variant['color'] = $data['Colore'];
			$variant['name'] = $data['Tgl'];
		}

		else if(array_key_exists('type', $data))
			$variant = $data;

		if (!$variant) return false;

		if ($variant['type'] == 'color-n-size' && $variant['name'] == 'TU')
		{
			$color = $variant['color'];
			$variant = [];
			$variant['type'] = 'simple';
			$variant['variant'] = $color;
			$variant['field'] = 'color';
		}

		$this->data['variant'] = $variant;
		return true;
	}

	public function getVariantLabel()
	{
		$data = $this->getData();
		if(!array_key_exists('variant', $data)) return null;

		if($data['variant']['type'] == 'color-n-size')
			return $data['variant']['color'] . ", " . $data['variant']['name'];

		if($data['variant']['type'] == 'simple')
			return $data['variant']['variant'];

		return $data['variant']['type'];
	}

	public function getVariantName()
	{
		$data = $this->getData();
		if(!array_key_exists('variant', $data)) return null;
		if($data['variant']['type'] == 'color-n-size')
			return $data['variant']['name'];
		return $data['variant']['type'];
	}

	public function getProductVariant()
	{
		$variant = $this->getVariant();

		if (!is_array($variant) ||
			!array_key_exists('type', $variant) ||
			$variant['type'] == 'unset'
		) return null;

		if ($variant['type'] == 'color-n-size') return $variant['color'];
		if ($variant['type'] == 'simple') return $variant['variant'];

		return null;
	}

	public function getVariantType()
	{
		return $this->getVariant()['type'];
	}

	public function isLoaded()
	{
		return array_key_exists('loaded', $this->getData()) && $this->data['loaded'];
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

	public function resetLoadedValue()
	{
		$this->data['loaded'] = false;
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

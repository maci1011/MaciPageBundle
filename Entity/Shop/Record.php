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
		return \Maci\PageBundle\MaciPageBundle::getLabel($this->type, $this->getTypeArray());
	}

	static public function getTypes()
	{
		return array_values(Record::getTypeArray());
	}

	public function isPurchase()
	{
		return $this->type == 'purchas';
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
			return [];

		return $this->data;
	}

	public function import($data)
	{
		foreach($data as $key => $value)
		{
			$opt = trim(strtolower($key));
			$opt = str_replace(' ', '', $opt);
			$opt = str_replace('.', '', $opt);
			$opt = str_replace(':', '', $opt);
			if (7 < strlen($opt)) $opt = substr($opt, 0, 7);

			switch ($opt)
			{
				case 'articol':
				case 'codice':
				case 'codart':
				case 'code':
					$this->code = $value;
					unset($data[$key]);
					$data['code'] = $value;
					break;
				case 'barcode':
					$this->barcode = $value;
					unset($data[$key]);
					$data['barcode'] = $value;
					break;
				case 'descrma':
				case 'marchio':
				case 'brand':
					$this->brand = $value;
					unset($data[$key]);
					$data['brand'] = $value;
					break;
				case 'descrca':
				case 'titolo':
				case 'title':
				case 'categor':
					$this->category = $value;
					unset($data[$key]);
					$data['category'] = $value;
					break;
				case 'prezzo':
				case 'przlord':
				case 'unixxeu':
				case 'price':
					$this->price = floatval(str_replace(',', '.', $value));
					unset($data[$key]);
					$data['price'] = number_format($this->price, 2);
					break;
				case 'ratio':
					$ratio = intval($value);
					unset($data[$key]);
					$data['ratio'] = $ratio;
					break;
				case 'quantit':
				case 'qtà':
				case 'qta':
				case 'qty':
					$this->quantity = intval($value);
					unset($data[$key]);
					$data['quantity'] = $this->quantity;
					break;
			}
		}

		if($this->data == null)
			$this->data = [];

		$this->setVariant($data);

		$this->data['imported'] = $data;
	}

	public function reload()
	{
		$variant = $this->getVariant();
		$this->setVariant($variant);

		if (!is_array($this->data) || !array_key_exists('imported', $this->data))
			return;

		$this->import($this->data['imported']);
	}

	public function hasImportedData()
	{
		return is_array($this->data) && array_key_exists('imported', $this->data);
	}

	public function getImported()
	{
		if (!$this->hasImportedData())
			return false;

		return $this->data['imported'];
	}

	public function getImportedLocale()
	{
		if (!$this->hasImportedData()) return false;
		if (array_key_exists('Locale', $this->data['imported']))
			return strtolower($this->data['imported']['Locale']);
		return false;
	}

	public function getImportedCurrency()
	{
		if (!$this->hasImportedData()) return null;
		if (array_key_exists('Uni:XXEUR025', $this->data['imported']))
			return 'EUR';
		if (array_key_exists('Currency', $this->data['imported']))
			return $this->data['imported']['Currency'];
		return 'EUR';
	}

	public function getImportedDescription()
	{
		if (!$this->hasImportedData()) return null;
		if (array_key_exists('Descrizione', $this->data['imported']))
			return $this->data['imported']['Descrizione'];
		if (array_key_exists('Descriz.', $this->data['imported']))
			return $this->data['imported']['Descriz.'];
		if (array_key_exists('Description', $this->data['imported']))
			return $this->data['imported']['Description'];
		return null;
	}

	public function getImportedComposition()
	{
		if (!$this->hasImportedData()) return null;
		if (array_key_exists('Composizione', $this->data['imported']))
			return $this->data['imported']['Composizione'];
		if (array_key_exists('Composition', $this->data['imported']))
			return $this->data['imported']['Composition'];
		return null;
	}

	public function getImportedTotal()
	{
		if (!$this->hasImportedData()) return null;
		if (array_key_exists('Tot:XXEUR025', $this->data['imported']))
			return number_format(floatval($this->data['imported']['Tot:XXEUR025']), 2);
		if (array_key_exists('Valore', $this->data['imported']))
			return number_format(floatval($this->data['imported']['Valore']), 2);
		if (array_key_exists('Amount', $this->data['imported']))
			return number_format(floatval($this->data['imported']['Amount']), 2);
		return null;
	}

	public function getImportedRatio()
	{
		if (!$this->hasImportedData() || !array_key_exists('ratio', $this->data['imported']))
			return null;

		return $this->data['imported']['ratio'];
	}

	public function getRatio()
	{
		$r = $this->getImportedRatio();
		return is_null($r) ? 0.28 : (intval($r) / 1000);
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

	public function setVariant(&$data)
	{
		$variant = false;
		$color = $this->findKey($data, ['Descr.Colore', 'Colore', 'Color']);
		$size = $this->findKey($data, ['Tgl', 'Taglia', 'Size']);

		if($color && $size)
		{
			$variant = [];
			$variant['type'] = 'color-n-size';

			$v = $data[$color];
			unset($data[$color]);
			$color = $v;
			$color = str_replace('+', ' ', $color);
			$color = ucwords($color);
			$data['color'] = $variant['color'] = $color;

			$v = $data[$size];
			unset($data[$size]);
			$size = $v;
			$size = str_replace('+', ' ', $size);
			$size = ucwords($size);
			$data['size'] = $variant['name'] = $size;
		}
		else if($this->findKey($data, ['Type']))
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

	public function findKey($array, $keys)
	{
		if (!is_array($array))
			return false;

		if (!is_array($keys))
		{
			if (!is_string($keys))
				return false;

			$keys = [$key];
		}

		foreach ($keys as $key)
		{
			if(array_key_exists($key, $array)) return $key;
			$x = strtolower($key);
			if(array_key_exists($x, $array)) return $x;
			$x = strtoupper($key);
			if(array_key_exists($x, $array)) return $x;
		}

		return false;
	}

	public function getVariantLabel()
	{
		if(!$this->hasVariant()) return null;

		$data = $this->getData();

		if($data['variant']['type'] == 'color-n-size')
			return $data['variant']['color'] . ", " . $data['variant']['name'];

		if($data['variant']['type'] == 'simple')
			return array_key_exists('variant', $data['variant']) ? $data['variant']['variant'] : '';

		return $data['variant']['type'];
	}

	public function getVariantName()
	{
		if(!$this->hasVariant()) return null;
		$data = $this->getData();

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

		if ($variant['type'] == 'color-n-size')
			return $variant['color'];

		if ($variant['type'] == 'simple')
			return $variant['variant'];

		return null;
	}

	public function getVariantType()
	{
		return $this->getVariant()['type'];
	}

	public function getVariantIdentifier()
	{
		$variant = $this->getProductVariant();

		if (!$variant)
			return null;

		return Product::getVariantIdentifier($variant);
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

	public function isSimple()
	{
		return in_array($this->getVariantType(), ['unset', 'simple']);
	}

	/**
	 * __toString()
	 */
	public function __toString()
	{
		return 'Record#' . ($this->id ? $this->id : 'new');
	}
}

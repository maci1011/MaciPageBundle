<?php

namespace Maci\PageBundle\Entity\Shop;

use Maci\PageBundle\Entity\Media\Media;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Maci\PageBundle\Entity\Shop\Record;

/**
 * Product
 */
class Product
{
	/**
	 * @var integer
	 */
	private $id;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $description;

	/**
	 * @var string
	 */
	private $composition;

	/**
	 * @var string
	 */
	private $path;

	/**
	 * @var string
	 */
	private $meta_title;

	/**
	 * @var text
	 */
	private $meta_description;

	/**
	 * @var string
	 */
	private $locale;

	/**
	 * @var decimal
	 */
	private $price;

	/**
	 * @var decimal
	 */
	private $sale;

	/**
	 * @var string
	 */
	private $type;

	/**
	 * @var string
	 */
	private $variant;

	/**
	 * @var boolean
	 */
	private $shipment;

	/**
	 * @var boolean
	 */
	private $limited;

	/**
	 * @var integer
	 */
	private $buyed;

	/**
	 * @var integer
	 */
	private $quantity;

	/**
	 * @var integer
	 */
	private $selled;

	/**
	 * @var string
	 */
	private $status;

	/**
	 * @var string
	 */
	private $code;

	/**
	 * @var string
	 */
	private $brand;

	/**
	 * @var integer
	 */
	private $position;

	/**
	 * @var json
	 */
	private $data;

	/**
	 * @var boolean
	 */
	private $public;

	/**
	 * @var \DateTime
	 */
	private $created;

	/**
	 * @var \DateTime
	 */
	private $updated;

	/**
	 * @var boolean
	 */
	private $removed;

	/**
	 * @var \Maci\PageBundle\Entity\Shop\Category
	 */
	private $parent;

	/**
	 * @var \Doctrine\Common\Collections\Collection
	 */
	private $records;

	/**
	 * @var \Doctrine\Common\Collections\Collection
	 */
	private $children;

	/**
	 * @var \Doctrine\Common\Collections\Collection
	 */
	private $categoryItems;

	/**
	 * @var \Doctrine\Common\Collections\Collection
	 */
	private $mediaItems;

	/**
	 * @var \Maci\PageBundle\Entity\Media\Media
	 */
	private $preview;

	/**
	 * @var \Maci\PageBundle\Entity\Media\Media
	 */
	private $cover;

	/**
	 * @var \Doctrine\Common\Collections\Collection
	 */
	private $mailSlideItems;

	/**
	 * @var \Doctrine\Common\Collections\Collection
	 */
	protected $translations;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->records = new \Doctrine\Common\Collections\ArrayCollection();
		$this->children = new \Doctrine\Common\Collections\ArrayCollection();
		$this->categoryItems = new \Doctrine\Common\Collections\ArrayCollection();
		$this->mediaItems = new \Doctrine\Common\Collections\ArrayCollection();
		$this->mailSlideItems = new \Doctrine\Common\Collections\ArrayCollection();
		$this->translations = new \Doctrine\Common\Collections\ArrayCollection();
		$this->name = 'New Product';
		$this->type = 'unset';
		$this->code = uniqid();
		$this->shipment = true;
		$this->limited = true;
		$this->buyed = 0;
		$this->quantity = 0;
		$this->selled = 0;
		$this->status = 'unset';
		$this->public = false;
		$this->removed = false;
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
	 * Set name
	 *
	 * @param string $name
	 * @return Product
	 */
	public function setName($name)
	{
		$this->name = $name;
	
		return $this;
	}

	/**
	 * Get name
	 *
	 * @return string 
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Set description
	 *
	 * @param string $description
	 * @return Product
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

	public function setComposition($composition)
	{
		$this->composition = $composition;

		return $this;
	}

	public function getComposition()
	{
		return $this->composition;
	}

	/**
	 * Set path
	 *
	 * @param string $path
	 * @return Product
	 */
	public function setPath($path)
	{
		$this->path = $path;

		return $this;
	}

	/**
	 * Get path
	 *
	 * @return string 
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * Set title
	 */
	public function setMetaTitle($meta_title)
	{
		$this->meta_title = $meta_title;

		return $this;
	}

	/**
	 * Get title
	 */
	public function getMetaTitle()
	{
		return $this->meta_title;
	}

	/**
	 * Set meta description
	 */
	public function setMetaDescription($meta_description)
	{
		$this->meta_description = $meta_description;

		return $this;
	}

	/**
	 * Get meta description
	 */
	public function getMetaDescription()
	{
		return $this->meta_description;
	}

	public function setLocale($locale)
	{
		$this->locale = $locale;

		return $this;
	}

	public function getLocale()
	{
		return $this->locale;
	}

	/**
	 * Set price
	 *
	 * @param float $price
	 * @return Product
	 */
	public function setPrice($price)
	{
		$this->price = $price;

		return $this;
	}

	/**
	 * Get price
	 *
	 * @return float 
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

	public function setSale($sale)
	{
		$this->sale = $sale;

		return $this;
	}

	public function getSale()
	{
		return $this->sale;
	}

	public function getSaleLabel()
	{
		return $this->sale ? number_format($this->sale, 2) : '';
	}

	public function getSellPrice()
	{
		return $this->sale ? $this->sale : $this->price;
	}

	public function getSellPriceLabel()
	{
		return number_format($this->getSellPrice(), 2);
	}

	public function getSaved()
	{
		return intval(100 - $this->getSellPrice() / $this->price * 100);
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

		if ($type == 'simple') $this->resetVariants();

		return $this;
	}

	public function resetType()
	{
		$this->type = 'unset';

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
			'Simple' => 'simple',
			'Variants' => 'vrnts'
		];
	}

	public function getTypeLabel()
	{
		return \Maci\PageBundle\MaciPageBundle::getLabel($this->type, $this->getTypeArray());
	}

	static public function getTypes()
	{
		return array_values(Product::getTypeArray());
	}

	public function isSimple()
	{
		return $this->type == 'simple';
	}

	public function setShipment($shipment)
	{
		$this->shipment = $shipment;

		return $this;
	}

	public function getShipment()
	{
		return $this->shipment;
	}

	/**
	 * Set limited
	 *
	 * @param boolean $limited
	 * @return Product
	 */
	public function setLimited($limited)
	{
		$this->limited = $limited;

		return $this;
	}

	/**
	 * Get limited
	 *
	 * @return boolean 
	 */
	public function getLimited()
	{
		return $this->limited;
	}

	/**
	 * Set buyed
	 *
	 * @param integer $buyed
	 * @return Product
	 */
	public function setBuyed($buyed)
	{
		$this->buyed = $buyed;

		return $this;
	}

	/**
	 * Get buyed
	 *
	 * @return integer 
	 */
	public function getBuyed()
	{
		return $this->buyed;
	}

	/**
	 * Set quantity
	 *
	 * @param string $quantity
	 * @return Product
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
	public function getQuantity($variant = false)
	{
		if (!$this->limited) return 1;
		if ($this->hasVariants() && $variant) return $this->getVariantQuantity($variant);
		return $this->quantity;
	}

	public function checkQuantity($quantity, $variant = false)
	{
		if (!$this->limited) return true;
		if ($this->hasVariants()) return $this->checkVariantQuantity($variant, $quantity);
		if ($this->quantity < $quantity) return false;
		return true;
	}

	public function sell($quantity, $variant = false)
	{
		if (!$this->limited) return true;
		if ($this->hasVariants()) return $variant ? $this->sellVariant($variant, $quantity) : false;
		if ($this->quantity < $quantity) return false;
		$this->quantity -= $quantity;
		$this->selled += $quantity;
		return true;
	}

	public function buy($quantity, $variant = false)
	{
		if (!$this->limited) return true;
		if ($this->hasVariants()) return $variant ? $this->buyVariant($variant, $quantity) : false;
		$this->quantity += $quantity;
		$this->buyed += $quantity;
		return true;
	}

	public function return($quantity, $variant = false)
	{
		if (!$this->limited) return true;
		if ($this->hasVariants()) return $variant ? $this->returnVariant($variant, $quantity) : false;
		if ($this->selled < $quantity) return false;
		$this->quantity += $quantity;
		$this->selled -= $quantity;
		return true;
	}

	public function back($quantity, $variant = false)
	{
		if (!$this->limited) return true;
		if ($this->hasVariants()) return $variant ? $this->backVariant($variant, $quantity) : false;
		if ($this->quantity < $quantity || $this->buyed < $quantity) return false;
		$this->quantity -= $quantity;
		$this->buyed -= $quantity;
		return true;
	}

	public function refreshQuantity()
	{
		if ($this->type == 'vrnts')
		{
			$this->buyed = $this->getTotalVariantsBuyed();
			$this->quantity = $this->getTotalVariantsQuantity();
			$this->selled = $this->getTotalVariantsSelled();
		}
	}

	public function resetQuantity()
	{
		$this->buyed = 0;
		$this->quantity = 0;
		$this->selled = 0;

		$this->resetVariantsQuantity();
	}

	/**
	 * Set selled
	 *
	 * @param integer $selled
	 * @return Product
	 */
	public function setSelled($selled)
	{
		$this->selled = $selled;

		return $this;
	}

	/**
	 * Get selled
	 *
	 * @return integer 
	 */
	public function getSelled()
	{
		return $this->selled;
	}

	/**
	 * Set status
	 *
	 * @param string $status
	 * @return Product
	 */
	public function setStatus($status)
	{
		$this->status = $status;

		return $this;
	}

	public function setAsStored()
	{
		return $this->setStatus('stored');
	}

	/**
	 * Get status
	 *
	 * @return string 
	 */
	public function getStatus()
	{
		return $this->status;
	}

	public static function getStatusArray()
	{
		return [
			'New Product' => 'new',
			'Stored Data' => 'stored',
			'Available' => 'availab',
			'On Sale' => 'on_sale',
			'Not Available' => 'not_ava',
			'Archived' => 'archive'
		];
	}

	public function getStatusLabel()
	{
		return \Maci\PageBundle\MaciPageBundle::getLabel($this->status, $this->getStatusArray());
	}

	public static function getStatusValues()
	{
		return array_values(Product::getStatusArray());
	}

	/**
	 * Set code
	 *
	 * @param string $code
	 * @return Product
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
	 * Set brand
	 *
	 * @param string $brand
	 * @return Product
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
	 * Set variant
	 *
	 * @param string $variant
	 * @return Product
	 */
	public function setVariant($variant)
	{
		$this->variant = $variant;

		return $this;
	}

	/**
	 * Get variant
	 *
	 * @return string 
	 */
	public function getVariant()
	{
		return $this->variant;
	}

	public function getVariantLabel()
	{
		return ucwords($this->variant);
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

	/**
	 * Set position
	 *
	 * @param integer $position
	 * @return Product
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
	 * Set created
	 *
	 * @param \DateTime $created
	 * @return Product
	 */
	public function setCreated($created)
	{
		$this->created = $created;

		return $this;
	}

	/**
	 * Get created
	 *
	 * @return \DateTime 
	 */
	public function getCreated()
	{
		return $this->created;
	}

	/**
	 * Set updated
	 *
	 * @param \DateTime $updated
	 * @return Product
	 */
	public function setUpdated($updated)
	{
		$this->updated = $updated;

		return $this;
	}

	/**
	 * Get updated
	 *
	 * @return \DateTime 
	 */
	public function getUpdated()
	{
		return $this->updated;
	}

	public function setRemoved($removed)
	{
		$this->removed = $removed;

		return $this;
	}

	public function getRemoved()
	{
		return $this->removed;
	}

	/**
	 * Set public
	 *
	 * @param boolean $public
	 * @return Product
	 */
	public function setPublic($public)
	{
		$this->public = $public;

		return $this;
	}

	/**
	 * Get public
	 *
	 * @return boolean 
	 */
	public function getPublic()
	{
		return $this->public;
	}

	public function addChild(\Maci\PageBundle\Entity\Shop\Product $child)
	{
		$this->children[] = $child;

		return $this;
	}

	public function removeChild(\Maci\PageBundle\Entity\Shop\Product $child)
	{
		$this->children->removeElement($child);
	}

	public function getChildren()
	{
		return $this->children;
	}

	public function getCurrentChildren()
	{
		return $this->getChildren()->filter(function($e){
			return !$e->getRemoved();
		});
	}

	public function setParent(\Maci\PageBundle\Entity\Shop\Product $parent = null)
	{
		$this->parent = $parent;

		return $this;
	}

	public function getParent()
	{
		return $this->parent;
	}

	public function getParentsList()
	{
		$list = array();
		$obj = $this->parent;
		while (is_object($obj)) {
			$list[] = $obj;
			$obj = $obj->getParent();
		}
		return array_reverse($list);
	}

	public function getHierarchy()
	{
		$list = $this->getParentsList();
		$list[] = $this;
		return $list;
	}

	public function addRecords(Record $record)
	{
		$this->records[] = $record;

		if($record->getType() == 'purchas') $this->quantity += $record->getQuantity();

		return $this;
	}

	public function removeRecords(Record $record)
	{
		$this->records->removeElement($record);
	}

	public function getRecords()
	{
		return $this->records;
	}

	/**
	 * Add categoryItems
	 *
	 * @param \Maci\PageBundle\Entity\Shop\CategoryItem $categoryItems
	 * @return Product
	 */
	public function addCategoryItem(\Maci\PageBundle\Entity\Shop\CategoryItem $categoryItems)
	{
		$this->categoryItems[] = $categoryItems;

		return $this;
	}

	/**
	 * Remove categoryItems
	 *
	 * @param \Maci\PageBundle\Entity\Shop\CategoryItem $categoryItems
	 */
	public function removeCategoryItem(\Maci\PageBundle\Entity\Shop\CategoryItem $categoryItems)
	{
		$this->categoryItems->removeElement($categoryItems);
	}

	/**
	 * Get categoryItems
	 *
	 * @return \Doctrine\Common\Collections\Collection 
	 */
	public function getCategoryItems()
	{
		return $this->categoryItems;
	}

	/**
	 * Add mediaItems
	 *
	 * @param \Maci\PageBundle\Entity\Shop\MediaItem $mediaItems
	 * @return Product
	 */
	public function addMediaItem(\Maci\PageBundle\Entity\Shop\MediaItem $mediaItem)
	{
		$this->mediaItems[] = $mediaItem;

		// if($this->preview == null) $this->setPreview($mediaItem->getMedia());
		// else if($this->cover == null) $this->setCover($mediaItem->getMedia());

		return $this;
	}

	/**
	 * Remove mediaItems
	 *
	 * @param \Maci\PageBundle\Entity\Shop\MediaItem $mediaItems
	 */
	public function removeMediaItem(\Maci\PageBundle\Entity\Shop\MediaItem $mediaItem)
	{
		$this->mediaItems->removeElement($mediaItem);
	}

	/**
	 * Get mediaItems
	 *
	 * @return \Doctrine\Common\Collections\Collection 
	 */
	public function getMediaItems()
	{
		return $this->mediaItems;
	}

	/**
	 * Add MailSlideItems
	 *
	 * @param \Maci\PageBundle\Entity\Mailer\SlideProduct $mailSlideItems
	 * @return Product
	 */
	public function addMailSlideItem(\Maci\PageBundle\Entity\Mailer\SlideProduct $mailSlideItem)
	{
		$this->mailSlideItems[] = $mailSlideItem;

		return $this;
	}

	/**
	 * Remove MailSlideItems
	 *
	 * @param \Maci\PageBundle\Entity\Mailer\SlideProduct $mailSlideItems
	 */
	public function removeMailSlideItem(\Maci\PageBundle\Entity\Mailer\SlideProduct $mailSlideItem)
	{
		$this->mailSlideItems->removeElement($mailSlideItem);
	}

	/**
	 * Get MailSlideItems
	 *
	 * @return \Doctrine\Common\Collections\Collection 
	 */
	public function getMailSlideItems()
	{
		return $this->mailSlideItems;
	}

	public function getPrivateMedia()
	{
		return $this->getMediaItems()->filter(function($e){
			return $e->getMedia() != null && !$e->getMedia()->getPublic();
		});
	}

	public function getPublicMedia()
	{
		return $this->getMediaItems()->filter(function($e){
			return $e->getMedia() != null && $e->getMedia()->getPublic();
		});
	}

	public function getPrivateDocuments()
	{
		return $this->getPrivateMedia()->filter(function($e){
			return $e->getMedia()->getType() == 'document';
		});
	}

	public function getImages()
	{
		return $this->getPublicMedia()->filter(function($e){
			return $e->getMedia()->getType() == 'image';
		});
	}

	public function getSideImages()
	{
		return $this->getImages()->filter(function($e){
			if(!$this->preview) return true;
			return $e->getMedia()->getId() != $this->preview->getId();
		});
	}

	/**
	 * Set preview
	 *
	 * @param \Maci\PageBundle\Entity\Media\Media $preview
	 * @return Product
	 */
	public function setPreview(\Maci\PageBundle\Entity\Media\Media $preview = null)
	{
		if ($preview == null) {
			$this->preview = null;
		}
		else if ($preview->getType() == 'image')
		{
			$this->preview = $preview;
			// if (!$this->hasMedia($preview)) {
			// 	$this->addImage($preview);
			// }
		}
		return $this;
	}

	public function hasMedia(\Maci\PageBundle\Entity\Media\Media $media = null)
	{
		foreach ($this->mediaItems as $item) {
			if ($item->getMedia()->getId() == $media->getId()) {
				return true;
			}
		}
		return false;
	}

	public function addImage(\Maci\PageBundle\Entity\Media\Media $media = null)
	{
		$item = new \Maci\PageBundle\Entity\Shop\MediaItem();
		$item->setMedia($media);
		$item->setProduct($this);
		$item->setPosition(count($this->mediaItems));
		$this->addMediaItem($item);
	}

	/**
	 * Get preview
	 *
	 * @return \Maci\PageBundle\Entity\Media\Media 
	 */
	public function getPreview()
	{
		return $this->preview;
	}

	public function getWebPreview()
	{
		if ($this->preview) {
			return $this->preview->getWebPreview();
		}
		return '/images/defaults/no-icon.png';
	}

	/**
	 * Set cover
	 *
	 * @param \Maci\PageBundle\Entity\Media\Media $cover
	 * @return Product
	 */
	public function setCover(\Maci\PageBundle\Entity\Media\Media $cover = null)
	{
		if ($cover == null) {
			$this->cover = null;
		}
		else if ($cover->getType() == 'image')
		{
			$this->cover = $cover;
			// if (!$this->hasMedia($cover)) {
			// 	$this->addImage($cover);
			// }
		}
		return $this;
	}

	/**
	 * Get cover
	 *
	 * @return \Maci\PageBundle\Entity\Media\Media 
	 */
	public function getCover()
	{
		return $this->cover;
	}

	/**
	 * setCreatedValue
	 */
	public function setCreatedValue()
	{
		$this->created = new \DateTime();
	}

	/**
	 * setUpdatedValue
	 */
	public function setUpdatedValue()
	{
		$this->updated = new \DateTime();
	}

	/**
	 * setCreatedValue
	 */
	public function setPathValue()
	{
		if ($this->path == null || $this->path == '')
			$this->path = $this->code . '-' . str_replace(' ', '-', trim(strtolower($this->name)));
	}

	public function isAvailable()
	{
		if ($this->removed)
			return false;

		elseif ($this->status === 'not_available')
			return false;

		elseif (!$this->price || $this->price <= 0)
			return false;

		elseif ($this->limited && $this->quantity < 1)
			return false;

		elseif ($this->type == 'vrnts' && $this->getTotalVariantsQuantity() < 1)
			return false;

		return true;
	}

	public function isOnSale()
	{
		if (0 < $this->sale) return true;
		return false;
	}

	public function getAmount()
	{
		if (0 < $this->sale) return $this->sale;
		return $this->price;
	}

	/**
	* Create a product from an image (used in the uploader)
	*/
	public function setFile(UploadedFile $file = null)
	{
		if ($file === null) return;

		$preview = new Media();
		$preview->setFile($file);
		$preview->setType('image');
		$this->setPreview($preview);

		$name = explode('-', explode('.', $file->getClientOriginalName())[0]);
		$i = 0;

		if (strlen($name[$i]) && is_numeric(trim($name[$i]))) { $this->setCode(trim($name[$i])); $i++; }
		if (count($name) - 1 <= $i) return;
		if (strlen($name[$i])) { $this->setName(trim($name[$i])); $i++; }
		if (count($name) - 1 <= $i) return;
		
		if (strlen($name[$i]) && is_numeric(trim($name[$i]))) $this->setPrice(intval(trim($name[$i]))/100);
		if (count($name) - 1 <= $i) return;
		if (strlen($name[$i])) $this->setDescription(trim($name[$i]));
		if (count($name) - 1 <= $i) return;
		if (strlen($name[$i])) $this->setComposition(trim($name[$i]));
	}

	public function getInhName()
	{
		if (0 < strlen($this->name)) return $this->name;
		$name = false;
		$list = $this->getParentsList();
		foreach ($list as $item) {
			if (0 < strlen($item->getName())) $name = $item->getName();
		}
		return $name;
	}

	public function getInhDescription()
	{
		if (0 < strlen($this->description)) return $this->description;
		$description = false;
		$list = $this->getParentsList();
		foreach ($list as $item) {
			if (0 < strlen($item->getDescription())) $description = $item->getDescription();
		}
		return $description;
	}

	public function getInhPreview()
	{
		if (is_object($this->preview)) return $this->preview;
		$preview = null;
		$list = $this->getParentsList();
		foreach ($list as $item) {
			if (is_object($item->getPreview())) $preview = $item->getPreview();
		}
		return $preview;
	}

	public function loadRecord(Record $record)
	{
		if($this->status != 'unset') return false;

		$name = ucwords(strtolower($record->getCategory()));
		$locale = $record->getImportedLocale();

		$this
			->setAsStored()
			->setCode($record->getCode())
			->setName($name)
			->setDescription($record->getImportedDescription())
			->setComposition($record->getImportedComposition())
			->setCollection($record->getCollection())
			->setBrand($record->getBrand())
			->setPath(str_replace(' ', '-',str_replace('.', '',
				$record->getCode() . "-" . strtolower(
					$record->getCategory() . "-" . $record->getBrand() .
					($record->hasVariant() ? '-' . $record->getProductVariant() : '')
				)
			)))
			->setPrice($record->getFinalPrice())
			->setLocale($locale != null && $locale != '' ? $locale : 'it')
			->setMetaTitle($name .
				($this->getVariant() ? " - " . ucwords($this->getVariant()) : '') .
				($record->getBrand() ? " - " . $record->getBrand() : '')
			)
			->setMetaDescription($this->getPriceLabel() . "€" .
				($this->getVariantsLabel() ? ' - ' . $this->getVariantsLabel() : '') .
				($record->getImportedComposition() ? ' - ' . $record->getImportedComposition() : '')
			)
		;

		$variant = $record->getVariant();

		if($variant['type'] == 'unset')
		{
			$this->setType($this->getTypes()[0]);
			return true;
		}

		return $this->addVariant($variant);
	}

	public function importRecord(Record $record)
	{
		if ($record->isLoaded() || !$this->buyOrSellRecord($record))
			return false;

		$record->setLoadedValue();

		return true;
	}

	public function getCollection()
	{
		$data = $this->getData();

		if(!array_key_exists('collection', $data))
			return null;

		return $this->data['collection'];
	}

	public function setCollection($collection)
	{
		$this->getData();
		$this->data['collection'] = $collection;
		return $this;
	}

	public function getVariants()
	{
		$data = $this->getData();
		if(!array_key_exists('variants', $data))
			$this->data['variants'] = [];
		return $this->data['variants'];
	}

	public function hasVariants()
	{
		return $this->type == 'vrnts' && $this->hasVariantType();
	}

	public function resetVariants()
	{
		$data = $this->getData();

		if(array_key_exists('variant-type', $data)) unset($data['variant-type']);
		if(array_key_exists('variant-field', $data)) unset($data['variant-field']);
		if(array_key_exists('variants-type', $data)) unset($data['variants-type']);

		if(array_key_exists('variants', $data)) $data['variants'] = [];

		$this->data = $data;
	}

	public function getVariantIndex($index)
	{
		if (!$this->hasVariants()) return false;
		$variants = $this->getVariants();
		return 0 <= $index && $index < count($variants) ? $variants[$index] : $variants[0];
	}

	public function getVariantByName($name)
	{
		if (is_array($name)) $name = $name['name'];
		foreach ($this->getVariants() as $key => $value)
			if ($name == $value['name'])
				return $value;
		return false;
	}

	public function getVariantQuantity($variant)
	{
		$variant = $this->getVariantByName($variant);
		if (!$variant) return $this->quantity;
		return $variant['quantity'];
	}

	public function resetVariantsQuantity()
	{
		if (!$this->hasVariants()) return;

		for ($i=0; $i < count($this->getVariants()); $i++)
		{
			$this->data['variants'][$i]['buyed'] = 0;
			$this->data['variants'][$i]['quantity'] = 0;
			$this->data['variants'][$i]['selled'] = 0;
		}
	}

	public function sumVariants($key)
	{
		$t = 0;
		foreach ($this->getVariants() as $value)
		{
			if (!array_key_exists($key, $value)) continue;
			$t += intval($value[$key]);
		}
		return $t;
	}

	public function getTotalVariantsBuyed()
	{
		return $this->sumVariants('buyed');
	}

	public function getTotalVariantsQuantity()
	{
		return $this->sumVariants('quantity');
	}

	public function getTotalVariantsSelled()
	{
		return $this->sumVariants('selled');
	}

	public function checkTotalQuantity()
	{
		if (($this->buyed - $this->quantity - $this->selled) != 0) return false;
		$this->refreshQuantity();
		return ($this->buyed - $this->quantity - $this->selled) == 0;
	}

	public function hasVariantType()
	{
		$data = $this->getData();
		return array_key_exists('variant-type', $data) && strlen($data['variant-type']);
	}

	public function getVariantType()
	{
		return $this->isSimple() ? 'simple' :
			($this->hasVariantType() ? $this->data['variant-type'] : 'variant')
		;
	}

	public function setVariantType($type)
	{
		if ($this->type != 'unset' || !is_string($type) ||
			!in_array($type, $this->getVariantTypes())
		) return;

		if (!$this->data) $this->data = [];

		$this->setType($this->getTypes()[1]);

		switch ($type)
		{
			case 'color-n-size':
				$this->data['variant-type'] = 'color-n-size';
				$this->data['variant-field'] = 'color';
				$this->data['variants-type'] = 'size';
				break;

			case 'simple':
				$this->resetVariants();
				$this->setType($this->getTypes()[0]);
				$this->data['variant-field'] = 'variant';
				break;
		}

		$this->data['variants'] = [];
	}

	public static function getVariantTypes()
	{
		return [
			'simple',
			'color-n-size'
		];
	}

	public function isColorNSize()
	{
		return $this->getVariantType() == 'color-n-size';
	}

	public function hasVariantField()
	{
		$data = $this->getData();
		return array_key_exists('variant-field', $data) && strlen($data['variant-field']);
	}

	public function getVariantField()
	{
		return $this->hasVariantField() ? $this->data['variant-field'] : 'variant';
	}

	public function hasVariantsType()
	{
		$data = $this->getData();
		return array_key_exists('variants-type', $data) && strlen($data['variants-type']);
	}

	public function getVariantsType()
	{
		return $this->hasVariantsType() ? $this->data['variants-type'] : 'variants';
	}

	public function addVariant($variant)
	{
		if (!is_array($variant) || !array_key_exists('type', $variant))
			return false;

		if ($variant['type'] == 'color-n-size')
			return $this->addColorAndSize($variant);

		if ($variant['type'] == 'simple')
			return $this->addSimpleVariant($variant);

		return false;
	}

	public function checkVariantQuantity($variant, $quantity)
	{
		if (!$this->hasVariants() || !$variant) return false;
		foreach ($this->getVariants() as $key => $value) {
			if ($variant['name'] == $value['name'])
			{
				if ($quantity <= $value['quantity']) {
					return true;
				}
				return false;
			}
		}
		return false;
	}

	public function addSimpleVariant($variant)
	{
		if (!$this->data || !$this->hasVariantType())
		{
			$this->setVariantType('simple');

			if (!array_key_exists('variant', $variant))
				return true;

			$this->setVariant($variant['variant']);

			if (array_key_exists('field', $variant) && strlen($variant['field']))
				$this->data['variant-field'] = $variant['field'];
		}

		if ($this->hasVariantType() || !is_array($this->variant) ||
			$this->getVariantType() != $variant['type'] ||
			(!array_key_exists('variant', $variant) && $this->variant != null) ||
			(array_key_exists('variant', $variant) && $this->variant != $variant['variant'])
		) return false;

		return true;
	}

	public function addColorAndSize($variant)
	{
		if (!$this->data || !array_key_exists('variant-type', $this->data) || !$this->data['variant-type'])
		{
			$this->setVariantType('color-n-size');

			if (array_key_exists('color', $variant))
				$this->setVariant($variant['color']);
		}

		if (!$this->isColorNSize() || !is_array($variant) ||
			'color-n-size' != $variant['type'] ||
			(!array_key_exists('color', $variant) && $this->variant != null) ||
			(array_key_exists('color', $variant) && $this->variant != $variant['color'])
		) return false;

		return $this->addSize($variant);
	}

	public function addSize($variant)
	{
		$index = $this->findVariant($variant['name']);
		if($index == -1)
		{
			if (is_null($this->variant))
			{
				$this->addColorAndSize(array_merge(['color' => 'Unset'], $variant));
				return false;
			}
			$newVariant = $this->newVariantItem($variant['name']);
			$this->data['variants'][count($this->data['variants'])] = $newVariant;
			return true;
		}
		$item = $this->data['variants'][$index];
		$this->data['variants'][$index] = $item;
		return true;
	}

	public function setVariantItem($variant, $option, $quantity, $aor = true)
	{
		$index = $this->findVariant($variant['name']);
		if ($index == -1)
		{
			if (!$this->addVariant($variant)) return false;
			$index = count($this->getVariants()) - 1;
		}
		$item = $this->data['variants'][$index];
		if (!array_key_exists($option, $item)) $item[$option] = 0;
		$item['quantity'] = intval($item['quantity']) + $quantity;
		$item[$option] = intval($item[$option]) + ($aor ? $quantity : -$quantity);
		$this->data['variants'][$index] = $item;
		$this->refreshQuantity();
		return true;
	}

	public function buyVariant($variant, $quantity)
	{
		return $this->setVariantItem($variant, 'buyed', $quantity);
	}

	public function backVariant($variant, $quantity)
	{
		return $this->setVariantItem($variant, 'buyed', -$quantity);
	}

	public function sellVariant($variant, $quantity)
	{
		return $this->setVariantItem($variant, 'selled', -$quantity, false);
	}

	public function returnVariant($variant, $quantity)
	{
		return $this->setVariantItem($variant, 'selled', $quantity, false);
	}

	public function buyOrSellRecord($record)
	{
		if ($record->getType() == 'unset')
			return false;

		if ($record->isSimple() == $this->hasVariants())
			return false;

		$variant = $record->getVariant();

		if($this->hasVariants())
		{
			if ($record->getType() == 'return')
				return $this->returnVariant($variant, $record->getQuantity());

			if ($record->getType() == 'back')
				return $this->backVariant($variant, $record->getQuantity());

			return $record->getDiffQuantity() < 0 ?
				$this->sellVariant($variant, $record->getQuantity()) :
				$this->buyVariant($variant, $record->getQuantity())
			;
		}

		if ($record->getType() == 'return')
			return $this->return($record->getQuantity());
		if ($record->getType() == 'back')
			return $this->back($record->getQuantity());
		if ($record->getType() == 'sale')
			return $this->sell($record->getQuantity());
		if ($record->getType() == 'purchas')
			return $this->buy($record->getQuantity());

		return false;
	}

	public function revertRecord($record)
	{
		$_rec = clone $record;
		$_rec->setQuantity(-$_rec->getQuantity());
		return $this->buyOrSellRecord($_rec);
	}

	public function newVariantItem($name)
	{
		return [
			'name' => $name,
			'quantity' => 0,
			'buyed' => 0,
			'selled' => 0
		];
	}

	public function findVariantItem($name)
	{
		$index = $this->findVariant($name);
		return $index == -1 ? false : $this->data['variants'][$index];
	}

	public function findVariant($name)
	{
		if(!$this->hasVariants() || !strlen($name)) return -1;
		for ($i=0; $i < count($this->getVariants()); $i++)
			if($name == $this->data['variants'][$i]['name'])
				return $i;
		return -1;
	}

	public function getVariantsLabel()
	{
		if (!$this->hasVariants() || !is_array($this->data['variants']))
			return null;

		$list = [];

		for ($i=0; $i < count($this->data['variants']); $i++)
			$list[] = $this->data['variants'][$i]['name'];

		return join(', ', $list);
	}

	public function getVariantId()
	{
		return $this->getVariantIdentifier($this->getVariant());
	}

	static public function getVariantIdentifier($name)
	{
		if (!$name || !strlen($name))
			return null;

		return strtolower(str_replace('.', '', str_replace(' ', '', $name)));
	}

	public function checkVariantAttr($variant)
	{
		$th = $this->getVariantId();
		$ch = $this->getVariantIdentifier($variant);
		$len = strlen($ch) < strlen($th) ? strlen($ch) : strlen($th);

		if ($len == 0)
			return false;

		return substr($th, 0, $len) == substr($ch, 0, $len);
	}

	public function checkVariant($variant)
	{
		if ($this->hasVariants() && is_string($variant))
			return -1 < $this->findVariant($variant);

		return !(
			!$this->hasVariants() && is_array($variant) && (
				(!array_key_exists('type', $variant) || $variant['type'] != 'simple') ||
				(array_key_exists('variant', $variant) && !$this->checkVariantAttr($variant['variant'])) ||
				(array_key_exists('field', $variant) && $variant['field'] != $this->getVariantField())
			) ||
			$this->hasVariants() && (
				!is_array($variant) ||
				!array_key_exists('name', $variant) ||
				$this->findVariant($variant['name']) < 0 ||
				!array_key_exists('type', $variant) ||
				$variant['type'] == 'unset' ||
				$variant['type'] != $this->getVariantType() ||
				(array_key_exists('variant', $variant) && !$this->checkVariantAttr($variant['variant'])) ||
				(array_key_exists('color', $variant) && !$this->checkVariantAttr($variant['color']))
			)
		);
	}

	public function checkRecord($record)
	{
		return 
			is_object($record) &&
			$this->getCode() == $record->getCode() &&
			$this->checkVariantAttr($record->getProductVariant()) &&
			$this->getVariantType() == $record->getVariantType()
		;
	}

	public function checkRecordVariant($record)
	{
		return $this->checkRecord($record) && $this->checkVariant($record->getVariant());
	}

	public function exportVariant($name = null)
	{
		if (!$this->hasVariants())
		{
			return [
				'type' => $this->getVariantType(),
				'field' => $this->getVariantField(),
				'variant' => $this->getVariant()
			];
		}

		if (!is_string($name) || -1 == $this->findVariant($name))
			return false;

		// $variant = $this->findVariantItem($name);

		if ($this->isColorNSize())
		{
			return [
				'type' => 'color-n-size',
				'name' => $name,
				'color' => $this->getVariant()
			];
		}

		return false;
	}

	public function createRecord($type, $variant, $quantity = 1)
	{
		if (!in_array($type, Record::getTypes()) || !$this->checkVariant($variant))
			return false;

		if ($this->hasVariants() && is_string($variant))
			$variant = $this->exportVariant($variant);

		$record = new Record;
		$record->setCode($this->getCode());
		$record->setCategory($this->getName());
		$record->setCollection($this->getCollection());
		$record->setBrand($this->getBrand());
		$record->setPrice($this->getPrice());
		$record->setType($type);
		$record->setVariant($variant);
		$record->setQuantity($quantity);

		return $record;
	}

	public function exportRecord($type, $variant, $quantity = 0)
	{
		$record = $this->createRecord($type, $variant, $quantity);

		if (!$record || !$this->importRecord($record))
			return false;

		return $record;
	}

	public function exportSaleRecord($variant, $quantity = 0)
	{
		return $this->exportRecord('sale', $variant, $quantity);
	}

	public function exportPurchaseRecord($variant, $quantity = 0)
	{
		return $this->exportRecord('purchas', $variant, $quantity);
	}

	public function exportReturnRecord($variant, $quantity = 0)
	{
		return $this->exportRecord('return', $variant, $quantity);
	}

	public function exportBackRecord($variant, $quantity = 0)
	{
		return $this->exportRecord('back', $variant, $quantity);
	}

	public function getFit()
	{
		$data = $this->getData();

		if(!array_key_exists('fit', $data))
			$this->data['fit'] = 'm';

		return $this->data['fit'];
	}

	static public function getFitArray()
	{
		return [
			'Tight' => 's',
			'Regular' => 'm',
			'Large' => 'l'
		];
	}

	public function getFitLabel()
	{
		return \Maci\PageBundle\MaciPageBundle::getLabel($this->getFit(), $this->getFitArray());
	}

	public static function getFitValues()
	{
		return array_values(Product::getFitArray());
	}

	public function setFit($fit)
	{
		$data = $this->getData();

		$this->data['fit'] = $fit;

		return $this;
	}

	/**
	 * __toString()
	 */
	public function __toString()
	{
		return (0 < strlen($this->getName()) ? $this->getName() : ('Product[' . ($this->id ? $this->id : 'tmp') . ']'));
	}
}

<?php

namespace Maci\PageBundle\Entity\Order;

use Doctrine\ORM\Mapping as ORM;

/**
 * Item
 */
class Item
{
	/**
	 * @var integer
	 */
	private $id;

	/**
	 * @var string
	 */
	private $info;

	/**
	 * @var json
	 */
	private $details;

	/**
	 * @var string
	 */
	private $quantity;

	/**
	 * @var string
	 */
	private $amount;

	/**
	 * @var \DateTime
	 */
	private $created;

	/**
	 * @var \DateTime
	 */
	private $updated;

	/**
	 * @var \Maci\PageBundle\Entity\Order\Order
	 */
	private $order;

	/**
	 * @var \Maci\PageBundle\Entity\Shop\Product
	 */
	private $product;

	/**
	 * Constructor
	 */
	public function __construct()
	{
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
	 * Set info
	 *
	 * @param string $info
	 * @return Item
	 */
	public function setInfo($info)
	{
		$this->info = $info;

		return $this;
	}

	/**
	 * Get info
	 *
	 * @return string 
	 */
	public function getInfo()
	{
		return $this->info;
	}

	/**
	 * Set details
	 *
	 * @param json $details
	 * @return Item
	 */
	public function setDetails($details)
	{
		$this->details = $details;

		return $this;
	}

	/**
	 * Get details
	 *
	 * @return json || null 
	 */
	public function getDetails()
	{
		return $this->details;
	}

	/**
	 * Set quantity
	 *
	 * @param string $quantity
	 * @return Item
	 */
	public function setQuantity($quantity)
	{
		$this->quantity = $quantity;

		if ($this->quantity < 1)
			$this->quantity = 1;

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
	 * Set amount
	 *
	 * @param string $amount
	 * @return Item
	 */
	public function setAmount($amount)
	{
		$this->amount = $amount;

		return $this;
	}

	/**
	 * Get amount
	 *
	 * @return string 
	 */
	public function getAmount()
	{
		return $this->amount;
	}

	/**
	 * Set created
	 *
	 * @param \DateTime $created
	 * @return Item
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
	 * @return Item
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

	/**
	 * Set order
	 *
	 * @param \Maci\PageBundle\Entity\Order\Order $order
	 * @return Item
	 */
	public function setOrder(\Maci\PageBundle\Entity\Order\Order $order = null)
	{
		$this->order = $order;

		return $this;
	}

	/**
	 * Get order
	 *
	 * @return \Maci\PageBundle\Entity\Order\Order 
	 */
	public function getOrder()
	{
		return $this->order;
	}

	/**
	 * Set product
	 *
	 * @param \Maci\PageBundle\Entity\Shop\Product $product
	 * @return Item
	 */
	public function setProduct(\Maci\PageBundle\Entity\Shop\Product $product = null)
	{
		$this->product = $product;

		return $this;
	}

	/**
	 * Get product
	 *
	 * @return \Maci\PageBundle\Entity\Shop\Product 
	 */
	public function getProduct()
	{
		return $this->product;
	}

	public function getProductName()
	{
		return $this->product ? $this->product->getName() : '';
	}

	public function setUpdatedValue()
	{
		$this->updated = new \DateTime();
	}

	public function setCreatedValue()
	{
		$this->created = new \DateTime();
	}

	public function setVariant($variant)
	{
		if (!is_array($this->details))
			$this->details = [];

		if (!$this->product || !$this->product->hasVariants())
		{
			if (!array_key_exists('type', $variant) || !in_array($variant['type'], ['unset', 'simple']))
				return false;

			$this->details['variant'] = $variant;
			return true;
		}

		if (!array_key_exists('type', $variant))
			$variant['type'] = $this->product->getVariantType();

		if (!$this->product->checkVariant($variant))
			return false;

		$this->details['variant'] = $variant;

		return true;
	}

	public function getVariant()
	{
		if (!$this->product->hasVariants() ||
			is_null($this->details) ||
			!array_key_exists('variant', $this->details)
		) return false;
		return $this->details['variant'];
	}

	public function getVariantLabel()
	{
		if (!$this->getVariant()) return '';
		return $this->details['variant']['type'] . ': ' . $this->details['variant']['name'];
	}

	public function getVariantName()
	{
		return $this->getVariant() ? $this->details['variant']['name'] : null;
	}

	public function getVariantNameLabel()
	{
		return ucfirst(str_replace('_', ' ', $this->getVariantName()));
	}

	public function getVariantType()
	{
		return $this->getVariant() ? $this->details['variant']['type'] : null;
	}

	public function getVariantTypeLabel()
	{
		return ucfirst(str_replace('_', ' ', $this->getVariantType()));
	}

	public function getPrivateDocuments()
	{
		$documents = array();

		if ($this->product) {
			foreach ($this->product->getPrivateDocuments() as $item) {
				$document = $item->getMedia();
				$documents[$document->getId()] = $document;
			}
		}

		if (count($documents)) {
			return $documents;
		}

		return false;
	}

	public function checkAvailability()
	{
		if (!$this->product
			|| !$this->product->isAvailable()
			|| !$this->product->checkQuantity($this->quantity, $this->getVariant())
		) return false;
		return true;
	}

	public function refreshAmount()
	{
		$this->amount = ( $this->getProductAmount() * $this->quantity );
	}

	public function getProductAmount()
	{
		if ($this->product) {
			return ( $this->product->getAmount() );
		}
		return 0;
	}

	public function getSale()
	{
		if ($this->product) {
			return ( $this->product->getSale() );
		}
		return 0;
	}

	public function exportSaleRecord()
	{
		return $this->getProduct()->exportSaleRecord($this->getVariant(), $this->getQuantity());
	}

	public function exportReturnRecord()
	{
		return $this->getProduct()->exportReturnRecord($this->getVariant(), $this->getQuantity());
	}

	public function getDescription()
	{
		if (!$this->product)
			return 'Item#' . ($this->id ? $this->id : 'New');

		return (
			$this->getProduct()->getCode() . ' - ' .
			$this->getProduct()->getName() . ' - ' .
			$this->getProduct()->getVariantLabel() .
			($this->getVariantName() ? ' - ' . $this->getVariantNameLabel() : '')
		);
	}

	public function applyCoupon($coupon)
	{
		foreach ($coupon->getTerms() as $term)
		{
			if (!$this->$term())
				return false;
		}
		$this->applyDiscount($coupon->getDiscount());
	}

	public function applyDiscount($discount)
	{
		if (!is_array($this->details))
			$this->details = [];

	}

	/**
	 *   TERMS START
	 */

	public static function getTermsArray()
	{
		return [
			'Product is not On Sale' => 'isNotProductOnSale',
			'Product is On Sale' => 'isProductOnSale'
		];
	}

	public function isProductOnSale()
	{
		if (!$this->product)
			return false;

		return $this->product->isOnSale();
	}

	public function isNotProductOnSale()
	{
		return !$this->isProductOnSale();
	}

	/**
	 *   TERMS END
	 */

	/**
	 * __toString()
	 */
	public function __toString()
	{
		return 'Order-Item_' . ($this->id ? $this->id : 'New');
	}
}

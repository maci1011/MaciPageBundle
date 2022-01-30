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

	public function setUpdatedValue()
	{
		$this->updated = new \DateTime();
	}

	public function setCreatedValue()
	{
		$this->created = new \DateTime();
	}

	public function setVariant($product, $variant)
	{
		if (!$product->hasVariants()) return;
		if (is_null($this->details)) $this->details = [];
		$variant = $product->getVariantIndex($variant);
		$this->details['variant'] = [
			'name' => $variant['name'],
			'type' => $product->getVariantsType()
		];
	}

	public function getVariant()
	{
		if (is_null($this->details) || !array_key_exists('variant', $this->details)) return false;
		return $this->details['variant'];
	}

	public function getVariantLabel()
	{
		return !$this->getVariant() ? '' : ucfirst($this->details['variant']['type']) . ': ' . $this->details['variant']['name'];
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

	public function checkProduct($quantity = false)
	{
		if ($this->product) {
			if ( !$this->product->isAvailable() ) {
				return false;
			}
			$quantity = $quantity ? $quantity : $this->quantity;
			if ( !$this->product->checkQuantity($quantity) ) {
				return false;
			}
		}
		return true;
	}

	public function checkAvailability($quantity = false)
	{
		if ( $this->product ) {
			if ( !$this->product->isAvailable() || !$this->checkProduct($quantity) ) {
				return false;
			}
		}
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

	/**
	 * __toString()
	 */
	public function __toString()
	{
		return 'Order-Item_'.($this->id ? $this->id : 'New');
	}
}

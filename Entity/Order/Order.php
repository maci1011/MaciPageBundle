<?php

namespace Maci\PageBundle\Entity\Order;

use Doctrine\ORM\Mapping as ORM;
use Maci\PageBundle\Entity\Order\Item;

/**
 * Order
 */
class Order
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
	private $mail;

	/**
	 * @var string
	 */
	private $status;

	/**
	 * @var string
	 */
	private $checkout;

	/**
	 * @var string
	 */
	private $type;

	/**
	 * @var string
	 */
	private $shipping;

	/**
	 * @var string
	 */
	private $payment;

	/**
	 * @var string
	 */
	private $shipping_cost;

	/**
	 * @var string
	 */
	private $payment_cost;

	/**
	 * @var string
	 */
	private $code;

	/**
	 * @var string
	 */
	private $amount;

	/**
	 * @var string
	 */
	private $sub_amount;

	/**
	 * @var string
	 */
	private $token;

	/**
	 * @var string
	 */
	private $locale;

	/**
	 * @var \DateTime
	 */
	private $invoice;

	/**
	 * @var \DateTime
	 */
	private $paid;

	/**
	 * @var \DateTime
	 */
	private $due;

	/**
	 * @var json
	 */
	private $data;

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
	 * @var \Doctrine\Common\Collections\Collection
	 */
	private $items;

	/**
	 * @var \Doctrine\Common\Collections\Collection
	 */
	private $transactions;

	/**
	 * @var \Doctrine\Common\Collections\Collection
	 */
	private $payments;

	/**
	 * @var \Maci\UserBundle\Entity\User
	 */
	private $user;

	/**
	 * @var \Maci\UserBundle\Entity\Address
	 */
	private $billing_address;

	/**
	 * @var \Maci\UserBundle\Entity\Address
	 */
	private $shipping_address;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->items = new \Doctrine\Common\Collections\ArrayCollection();
		$this->transactions = new \Doctrine\Common\Collections\ArrayCollection();
		$this->payments = new \Doctrine\Common\Collections\ArrayCollection();
		$this->token = md5(
			'MaciPageBundle_Entity_Order-' . rand(10000, 99999) . '-' . 
			date('h') . date('i') . date('s') . date('m') . date('d') . date('Y')
		);
		$this->type = 'order';
		$this->status = 'new';
		$this->checkout = 'checkout';
		$this->shipping = null;
		$this->payment = null;
		$this->amount = 0;
		$this->sub_amount = 0;
		$this->shipment = false;
		$this->invoice = null;
		$this->paid = null;
		$this->due = null;
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
	 * @return Order
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

	/**
	 * Set mail
	 *
	 * @param string $mail
	 * @return Address
	 */
	public function setMail($mail)
	{
		$this->mail = $mail;

		return $this;
	}

	/**
	 * Get mail
	 *
	 * @return string 
	 */
	public function getMail()
	{
		return $this->mail;
	}

	/**
	 * Set type
	 *
	 * @param string $type
	 * @return Order
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

	public static function getTypeArray()
	{
		return [
			'Cart' => 'cart',
			'Order' => 'order'
		];
	}

	public function getTypeLabel()
	{
		return \Maci\PageBundle\MaciPageBundle::getLabel($this->type, $this->getTypeArray());
	}

	public static function getTypeValues()
	{
		return array_values(Order::getTypeArray());
	}

	public function setPayment($payment)
	{
		$this->payment = $payment;

		return $this;
	}

	public function getPayment()
	{
		return $this->payment;
	}

	public function setShipping($shipping)
	{
		$this->shipping = $shipping;

		return $this;
	}

	public function getShipping()
	{
		return $this->shipping;
	}

	public function setPaymentCost($payment_cost)
	{
		$this->payment_cost = $payment_cost;

		return $this;
	}

	public function getPaymentCost()
	{
		return $this->payment_cost;
	}

	public function getpayment_cost()
	{
		return $this->getPaymentCost();
	}

	public function setShippingCost($shipping_cost)
	{
		$this->shipping_cost = $shipping_cost;

		return $this;
	}

	public function getShippingCost()
	{
		return $this->shipping_cost;
	}

	public function getshipping_cost()
	{
		return $this->getShippingCost();
	}

	/**
	 * Set status
	 *
	 * @param string $status
	 * @return Order
	 */
	public function setStatus($status)
	{
		$this->status = $status;

		return $this;
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
			'New' => 'new',
			'Session' => 'session',
			'Wish List' => 'wishlist',
			'Current' => 'current',
			'Confirmed' => 'confirm',
			'Paid' => 'paid',
			'Complete' => 'complete',
			'Paid' => 'paid',
			'Refuse' => 'refuse'
		];
	}

	public function getProgression()
	{
		$i = 0;
		foreach ($this->getStatusArray() as $key => $value)
		{
			if ($value == $this->status)
				return $i;
			$i++;
		}
		return -1;
		// return array_search($this->status, array_values($this->getStatusArray()));
	}

	public function getStatusLabel()
	{
		return \Maci\PageBundle\MaciPageBundle::getLabel($this->status, $this->getStatusArray());
	}

	public static function getStatusValues()
	{
		return array_values(Order::getStatusArray());
	}

	public function setCheckout($checkout)
	{
		$this->checkout = $checkout;

		return $this;
	}

	public function getCheckout()
	{
		return $this->checkout;
	}

	public static function getCheckoutArray()
	{
		return [
			'Checkout' => 'checkout',
			'Pickup In Store' => 'pickup',
			'Booking' => 'booking'
		];
	}

	public function getCheckoutLabel()
	{
		return \Maci\PageBundle\MaciPageBundle::getLabel($this->checkout, $this->getCheckoutArray());
	}

	public static function getCheckoutValues()
	{
		return array_values(Order::getCheckoutArray());
	}

	/**
	 * Set token
	 *
	 * @param string $token
	 * @return Order
	 */
	public function setToken($token)
	{
		$this->token = $token;

		return $this;
	}

	/**
	 * Get token
	 *
	 * @return string 
	 */
	public function getToken()
	{
		return $this->token;
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
	 * Set code
	 *
	 * @param string $code
	 * @return Order
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
	 * Set amount
	 *
	 * @param string $amount
	 * @return Order
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

	public function getSubAmount()
	{
		if (!$this->sub_amount) {
			$this->refreshAmount();
		}
		return $this->sub_amount;
	}

	/**
	 * Set invoice
	 *
	 * @param \DateTime $invoice
	 * @return Order
	 */
	public function setInvoice($invoice)
	{
		$this->invoice = $invoice;

		return $this;
	}

	/**
	 * Get invoice
	 *
	 * @return \DateTime 
	 */
	public function getInvoice()
	{
		return $this->invoice;
	}

	/**
	 * Set paid
	 *
	 * @param \DateTime $paid
	 * @return Order
	 */
	public function setPaid($paid)
	{
		$this->paid = $paid;

		return $this;
	}

	/**
	 * Get paid
	 *
	 * @return \DateTime 
	 */
	public function getPaid()
	{
		return $this->paid;
	}

	/**
	 * Set due
	 *
	 * @param \DateTime $due
	 * @return Order
	 */
	public function setDue($due)
	{
		$this->due = $due;

		return $this;
	}

	/**
	 * Get due
	 *
	 * @return \DateTime 
	 */
	public function getDue()
	{
		return $this->due;
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
	 * Set created
	 *
	 * @param \DateTime $created
	 * @return Order
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
	 * @return Order
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
	 * Add items
	 *
	 * @param \Maci\PageBundle\Entity\Order\Item $items
	 * @return Order
	 */
	public function addItem(\Maci\PageBundle\Entity\Order\Item $items)
	{
		$this->items[] = $items;

		return $this;
	}

	/**
	 * Remove items
	 *
	 * @param \Maci\PageBundle\Entity\Order\Item $items
	 */
	public function removeItem(\Maci\PageBundle\Entity\Order\Item $items)
	{
		$this->items->removeElement($items);
	}

	/**
	 * Get items
	 *
	 * @return \Doctrine\Common\Collections\Collection 
	 */
	public function getItems()
	{
		return $this->items;
	}

	/**
	 * Add transactions
	 *
	 * @param \Maci\PageBundle\Entity\Order\Transaction $transactions
	 * @return Order
	 */
	public function addTransaction(\Maci\PageBundle\Entity\Order\Transaction $transactions)
	{
		$this->transactions[] = $transactions;

		return $this;
	}

	/**
	 * Remove transactions
	 *
	 * @param \Maci\PageBundle\Entity\Order\Transaction $transactions
	 */
	public function removeTransaction(\Maci\PageBundle\Entity\Order\Transaction $transactions)
	{
		$this->transactions->removeElement($transactions);
	}

	/**
	 * Get transactions
	 *
	 * @return \Doctrine\Common\Collections\Collection 
	 */
	public function getTransactions()
	{
		return $this->transactions;
	}

	/**
	 * Add payments
	 *
	 * @param \Maci\PageBundle\Entity\Order\Payment $payments
	 * @return Order
	 */
	public function addPayment(\Maci\PageBundle\Entity\Order\Payment $payments)
	{
		$this->payments[] = $payments;

		return $this;
	}

	/**
	 * Remove payments
	 *
	 * @param \Maci\PageBundle\Entity\Order\Payment $payments
	 */
	public function removePayment(\Maci\PageBundle\Entity\Order\Payment $payments)
	{
		$this->payments->removeElement($payments);
	}

	/**
	 * Get payments
	 *
	 * @return \Doctrine\Common\Collections\Collection 
	 */
	public function getPayments()
	{
		return $this->payments;
	}

	/**
	 * Set user
	 *
	 * @param \Maci\UserBundle\Entity\User $user
	 * @return Order
	 */
	public function setUser(\Maci\UserBundle\Entity\User $user = null)
	{
		$this->user = $user;

		return $this;
	}

	/**
	 * Get user
	 *
	 * @return \Maci\UserBundle\Entity\User 
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * Set billing_address
	 *
	 * @param \Maci\UserBundle\Entity\Address $billing_address
	 * @return Order
	 */
	public function setBillingAddress(\Maci\UserBundle\Entity\Address $billing_address = null)
	{
		$this->billing_address = $billing_address;

		return $this;
	}

	/**
	 * Get billing_address
	 *
	 * @return \Maci\UserBundle\Entity\Address 
	 */
	public function getBillingAddress()
	{
		return $this->billing_address;
	}

	/**
	 * Set shipping_address
	 *
	 * @param \Maci\UserBundle\Entity\Address $shipping_address
	 * @return Order
	 */
	public function setShippingAddress(\Maci\UserBundle\Entity\Address $shipping_address = null)
	{
		if($shipping_address && $this->shipping_address && $this->shipping) {
			if($this->shipping_address->getCountry() !== $shipping_address->getCountry()) {
				$this->shipping = null;
			}
		}

		$this->shipping_address = $shipping_address;

		return $this;
	}

	/**
	 * Get shipping_address
	 *
	 * @return \Maci\UserBundle\Entity\Address 
	 */
	public function getShippingAddress()
	{
		return $this->shipping_address;
	}

	public function setUpdatedValue()
	{
		$this->updated = new \DateTime();
	}

	public function setCreatedValue()
	{
		$this->created = new \DateTime();
	}

	public function setInvoiceValue()
	{
		$this->invoice = new \DateTime();
	}

	public function getTransactionsAmount()
	{
		$amount = 0;

		foreach ($this->transactions as $item) {
			$amount += $item->getAmount();
		}

		return $amount;
	}

	public function getBalance()
	{
		return ( $this->getTransactionsAmount() - $this->getAmount() );
	}

	public function getArrayLabel($array, $key)
	{
		if (array_key_exists($key, $array)) {
			return $array[$key];
		}
		$str = str_replace('_', ' ', $key);
		return ucwords($str);
	}

	public function getOrderDocuments()
	{
		$documents = array();
		foreach ($this->items as $item) {
			$docs = $item->getPrivateDocuments();
			if (is_array($docs) && count($docs)) {
				foreach ($docs as $id => $doc) {
					$documents[$id] = $doc;
				}
			}
		}

		return $documents;
	}

	public function checkOrder()
	{
		if(!count($this->items)) {
			return false;
		}

		foreach ($this->items as $item) {
			if (!$item->checkAvailability()) {
				return false;
			}
		}

		return true;
	}


	public function checkShipment()
	{
		$shipment = false;

		foreach ($this->items as $item) {
			$product = $item->getProduct();
			if ( $product && $product->getShipment() ) {
				$shipment = true;
				break;
			}
		}

		return $shipment;
	}

	public function refreshAmount()
	{
		$amounts = $this->getItems()->map(function($e){
			$e->refreshAmount();
			return $e->getAmount();
		});

		$tot = 0;

		foreach ($amounts as $amount) {
			$tot += $amount;
		}

		$this->sub_amount = $tot;

		if ( $this->getShippingCost() ) {
			$tot += $this->getShippingCost();
		}
		if ( $this->getPaymentCost() ) {
			$tot += $this->getPaymentCost();
		}

		return $this->amount = $tot;
	}

	public function subItemsQuantity()
	{
		foreach ($this->items as $item) {
			if ($product = $item->getProduct()) {
				if (!$product->sell($item->getQuantity(), $item->getVariant())) {
					return false;
				}
			}
		}
		return true;
	}

	public function reverseOrder()
	{
		foreach ($this->items as $item) {
			if ($product = $item->getProduct()) {
				$product->return($item->getQuantity(), $item->getVariant());
			}
		}
	}

	public function getItemsNumber()
	{
		return count($this->items);
	}

	public function getTotalItemsQuantity()
	{
		$tot = 0;
		foreach ($this->items as $item) {
			$tot += $item->getQuantity();
		}
		return $tot;
	}

	public function checkConfirmation()
	{
		return !(3 < $this->getProgression() || !$this->checkOrder());
	}

	public static function getCheckoutActions()
	{
		return [
			'billingAddress',
			'shippingAddress',
			'payment',
			'shipping'
		];
	}

	public function getCheckoutParameters($editAction)
	{
		$type = $this->getCheckout();

		if (!$type || !in_array($type, $this->getCheckoutValues()))
			return false;

		if ($type == 'pickup')
			return $this->getPickupInStoreParameters($editAction);

		if ($type == 'booking')
			return $this->getBookingParameters($editAction);

		if (!is_string($editAction) || !in_array($editAction, $this->getCheckoutActions()))
			$editAction = false;

		$set = false;
		$checkout = [
			'billingAddress' => false,
			'shippingAddress' => false,
			'payment' => false,
			'shipping' => false,
			'confirm' => false
		];

		if ($this->getBillingAddress() && $editAction != 'billingAddress')
			$checkout['billingAddress'] = 'setted';
		else
		{
			if ($set)
				$checkout['billingAddress'] = 'toset';
			else
			{
				$checkout['billingAddress'] = 'set';
				$set = true;
			}
		}

		if ($this->checkShipment())
		{
			if ($this->getShippingAddress() && $editAction != 'shippingAddress')
				$checkout['shippingAddress'] = 'setted';
			else
			{
				if ($set)
					$checkout['shippingAddress'] = 'toset';
				else
				{
					$checkout['shippingAddress'] = 'set';
					$set = true;
				}
			}

			if ($this->getShipping() && $editAction != 'shipping')
				$checkout['shipping'] = 'setted';
			else
			{
				if ($set)
					$checkout['shipping'] = 'toset';
				else
				{
					$checkout['shipping'] = 'set';
					$set = true;
				}
			}
		}
		else
		{
			$checkout['shippingAddress'] = false;
			$checkout['shipping'] = false;
		}

		if ($this->getPayment() && $editAction != 'payment')
			$checkout['payment'] = 'setted';
		else
		{
			if ($set)
				$checkout['payment'] = 'toset';
			else
			{
				$checkout['payment'] = 'set';
				$set = true;
			}
		}

		if ($set)
		{
			$checkout['confirm'] = 'toset';
			$checkout['confirm_form'] = false;
		}
		else
		{
			$checkout['confirm'] = 'set';
			$checkout['confirm_form'] = true;
		}

		$checkout['order'] = $this;
		$checkout['checkout'] = true;

		return $checkout;
	}

	public static function getPickupInStoreActions()
	{
		return [
			'billingAddress'
		];
	}

	public function getPickupInStoreParameters($editAction)
	{
		if (!is_string($editAction) || !in_array($editAction, $this->getPickupInStoreActions()))
			$editAction = false;

		$set = false;
		$checkout = [
			'billingAddress' => false,
			'shippingAddress' => false,
			'payment' => false,
			'shipping' => false,
			'confirm' => false
		];

		if ($this->getBillingAddress() && $editAction != 'billingAddress')
			$checkout['billingAddress'] = 'setted';
		else
		{
			if ($set)
				$checkout['billingAddress'] = 'toset';
			else
			{
				$checkout['billingAddress'] = 'set';
				$set = true;
			}
		}

		if ($set)
		{
			$checkout['confirm'] = 'toset';
			$checkout['confirm_form'] = false;
		}
		else
		{
			$checkout['confirm'] = 'set';
			$checkout['confirm_form'] = true;
		}

		$checkout['order'] = $this;
		$checkout['checkout'] = true;

		return $checkout;
	}

	public function getBookingParameters($editAction)
	{
		// TO DO...
		return $this->getPickupInStoreParameters($editAction);
	}

	public function confirmOrder($params = false)
	{
		$this->addConfirmData($params);

		$this->subItemsQuantity();

		$this->setInvoiceValue();

		$this->due = $this->invoice;

		$this->due->modify('+1 month');

		$this->status = 'confirm';

		return true;
	}

	public function addConfirmData($params)
	{
		if (!is_array($params)) return;

		if (!is_array($this->data))
			$this->data = [];

		if (!array_key_exists('confirmData', $this->data))
			$this->data['confirmData'] = [];

		$this->data['confirmData'][date("Y/m/d H:i:s", time())] = $params;
	}

	public function completeOrder()
	{
		$this->status = 'complete';

		return true;
	}

	public function endOrder()
	{
		if (6 < $this->getProgression()) {
			return false;
		}

		if (!$this->getBalance() < 0 ) {
			$this->status = 'complete';
		} else {
			$this->status = 'paid';
		}

		return true;
	}

	public function addProduct($product, $quantity = 1, $variant = false)
	{
		$same_item = false; $persist = false;
		$index = -1;
		foreach ($this->getItems() as $item) {
			$index++;
			$item_product = $item->getProduct();
			if (!is_object($item_product)) break;
			if ($item_product->getId() == $product->getId()) {
				if ($variant) {
					if ($item->getVariant()['name'] == $variant['name']) {
						$same_item = $item;
						break;
					}
				}
				else {
					$same_item = $item;
					break;
				}
			}
		}

		if ($same_item) {
			$item = $same_item;
			$quantity = $item->getQuantity() + $quantity;
			if ($quantity < 1) $quantity = 1;
			$limit = intval($product->getVariantIndex($variant)['quantity']);
			if ($variant && $limit < $quantity) $quantity = $limit;
			if (!$variant && !$product->checkQuantity($quantity)) $quantity = $product->getQuantity();
		} else {
			$item = new Item;
			$item->setProduct($product);
			$item->setOrder($this);
			$this->addItem($item);
			if ($variant != false) $item->setVariant($variant);
			$persist = true;
		}

		$item->setQuantity($quantity < 1 ? 1 : $quantity);

		if (!$item->checkAvailability()) return false;
		/*
		{
			if ($same_item) $this->removeItem($index);
			else $persist = false;
			return false;
		}
		*/
		if ($persist) return $item;

		return true;
	}

	/**
	 * __toString()
	 */
	public function __toString()
	{
		return 'Order_'.($this->id ? $this->id : 'New');
	}
}

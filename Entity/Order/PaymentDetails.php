<?php

namespace Maci\PageBundle\Entity\Order;

use Doctrine\ORM\Mapping as ORM;
use Payum\Core\Model\ArrayObject;

class PaymentDetails extends ArrayObject
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
	 * @var \Maci\PageBundle\Entity\Order\Payment
	 */
	private $payment;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->type = 'unset';
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

	public function setDetails(array $details)
	{
		return $this->details = $details;
	}

	public function getDetails()
	{
		return $this->details;
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
			'Unset' => 'unset',
			'Checkout Payment' => 'checkoutPayment',
			'PayPal Express Checkout' => 'paypalExpress',
			'PayPal Ipn' => 'paypalIpn'
		];
	}

	public function getTypeLabel()
	{
		return \Maci\PageBundle\MaciPageBundle::getLabel($this->type, $this->getTypeArray());
	}

	public static function getTypeValues()
	{
		return array_values(PaymentDetails::getTypeArray());
	}

	/**
	 * Set payment
	 *
	 * @param \Maci\PageBundle\Entity\Order\Payment $payment
	 * @return Item
	 */
	public function setPayment(\Maci\PageBundle\Entity\Order\Payment $payment = null)
	{
		$this->payment = $payment;

		return $this;
	}

	/**
	 * Get payment
	 *
	 * @return \Maci\PageBundle\Entity\Order\Payment 
	 */
	public function getPayment()
	{
		return $this->payment;
	}

	public function getPayaPalStatus()
	{
		if ((array_key_exists('ACK', $this->details) && $this->details['ACK'] == 'Success') &&
			(array_key_exists('ADDRESSSTATUS', $this->details) && $this->details['ADDRESSSTATUS'] == 'Confirmed') &&
			(array_key_exists('CHECKOUTSTATUS', $this->details) && $this->details['CHECKOUTSTATUS'] == 'PaymentActionCompleted') &&
			(array_key_exists('PAYMENTINFO_0_PAYMENTSTATUS', $this->details) && $this->details['PAYMENTINFO_0_PAYMENTSTATUS'] == 'Completed')
		) return 'success';

		return 'unpaid';
	}

	public function getStatus()
	{
		if (!is_array($this->details))
			return false;

		if ($this->getType() == 'paypalExpress')
			return $this->getPayaPalStatus();

		if (!array_key_exists('status', $this->details))
			return false;

		return $this->details['status'];
	}

	/**
	 * __toString()
	 */
	public function __toString()
	{
		return 'PaymentDetails_'.($this->id ? $this->id : 'New');
	}
}

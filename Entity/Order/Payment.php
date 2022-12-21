<?php

namespace Maci\PageBundle\Entity\Order;

use Doctrine\ORM\Mapping as ORM;
use Payum\Core\Model\Payment as BasePayment;
use Symfony\Component\Intl\Currencies;

class Payment extends BasePayment
{
	/**
	 * @var integer
	 */
	private $id;

	/**
	 * @var string
	 */
	private $status;

	/**
	 * @var \DateTime
	 */
	private $created;

	/**
	 * @var ArrayCollection
	 */
	private $paymentDetails;

	/**
	 * @var \Maci\PageBundle\Entity\Order\Order
	 */
	private $order;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->paymentDetails = new \Doctrine\Common\Collections\ArrayCollection();
		$this->details = [];
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
		return $this->status == null ? 'unp' : $this->status;
	}

	public static function getStatusArray()
	{
		return [
			'Unpaid' => 'unp',
			'Error' => 'err',
			'Pending' => 'pen',
			'Refunded' => 'ref',
			'Paid' => 'pai'
		];
	}

	public function getStatusLabel()
	{
		return \Maci\PageBundle\MaciPageBundle::getLabel($this->status, $this->getStatusArray());
	}

	public static function getStatusValues()
	{
		return array_values(Payment::getStatusArray());
	}

	public function setStatusValue()
	{
		if (!count($this->paymentDetails))
		{
			$this->status = 'unp';
			return;
		}

		switch (strtolower($this->paymentDetails->last()->getStatus()))
		{
			case 'success':
				$this->status = 'pai';
				break;
			
			case 'failure':
				$this->status = 'err';
				break;
			
			case 'pending':
				$this->status = 'pen';
				break;

			case 'refunded':
				$this->status = 'ref';
				break;

			case 'canceled':
				$this->status = 'can';
				break;

			default:
				$this->status = 'unp';
				break;
		}
	}

	public function isPaid()
	{
		return $this->status == 'pai';
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

	public function setCreatedValue()
	{
		$this->created = new \DateTime();
	}

	/**
	 * Add paymentDetails
	 *
	 * @param \Maci\PageBundle\Entity\Order\PaymentDetails $paymentDetails
	 * @return Order
	 */
	public function addPaymentDetails(\Maci\PageBundle\Entity\Order\PaymentDetails $paymentDetails)
	{
		$this->paymentDetails[] = $paymentDetails;

		if (!$this->status)
			$this->setStatusValue();

		return $this;
	}

	/**
	 * Remove paymentDetails
	 *
	 * @param \Maci\PageBundle\Entity\Order\PaymentDetails $paymentDetails
	 */
	public function removePaymentDetails(\Maci\PageBundle\Entity\Order\PaymentDetails $paymentDetails)
	{
		$this->paymentDetails->removeElement($paymentDetails);
	}

	/**
	 * Get paymentDetails
	 *
	 * @return \Doctrine\Common\Collections\Collection 
	 */
	public function getPaymentDetails()
	{
		return $this->paymentDetails;
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

	public function getGateway()
	{
		if (!is_array($this->details) || !array_key_exists('gateway', $this->details))
			return count($this->paymentDetails) ? $this->paymentDetails->first()->getType() : null;

		return $this->details['gateway'];
	}

	public function getGatewayLabel()
	{
		if (!is_array($this->details) || !array_key_exists('gateway', $this->details))
			return count($this->paymentDetails) ? $this->paymentDetails->first()->getTypeLabel() : null;

		return ucwords($this->details['gateway']);
	}

	public function getAmount()
	{
		return intval($this->getTotalamount()) / 100;
	}

	public function getPaidAmount()
	{
		return $this->isPaid() ? intval($this->getTotalamount()) / 100 : 0;
	}

	public function getTotalamountLabel()
	{
		return number_format($this->getTotalamount() / 100, 2, '.', ',') . " " . ucfirst(Currencies::getName($this->getCurrencyCode()));
	}

	/**
	 * __toString()
	 */
	public function __toString()
	{
		return 'Payment_'.($this->id ? $this->id : 'New');
	}
}

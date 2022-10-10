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

    public function getTypeArray()
    {
        return array(
            'Unset' => 'unset',
            'Checkout Payment' => 'checkoutPayment',
            'PayPal Express Checkout' => 'paypalExpress',
            'PayPal Ipn' => 'paypalIpn'
        );
    }

    public function getTypeLabel()
    {
        $array = $this->getTypeArray();
        $key = array_search($this->type, $array);
        if ($key) return $key;
        $str = str_replace('_', ' ', $this->type);
        return ucwords($str);
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

    public function getACK()
    {
        if (!is_array($this->details) || !array_key_exists('ACK', $this->details))
            return null;

        return $this->details['ACK'];
    }

    public function getStatus()
    {
        if (!is_array($this->details) || !array_key_exists('status', $this->details))
            return $this->getACK();

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

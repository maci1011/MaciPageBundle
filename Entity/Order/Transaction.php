<?php

namespace Maci\PageBundle\Entity\Order;

use Doctrine\ORM\Mapping as ORM;

/**
 * Transaction
 */
class Transaction
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $tx;

    /**
     * @var string
     */
    private $gateway;

    /**
     * @var string
     */
    private $details;

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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set tx
     *
     * @param string $tx
     * @return Item
     */
    public function setTx($tx)
    {
        $this->tx = $tx;

        return $this;
    }

    /**
     * Get tx
     *
     * @return string 
     */
    public function getTx()
    {
        return $this->tx;
    }

    /**
     * Set gateway
     *
     * @param string $gateway
     * @return Item
     */
    public function setGateway($gateway)
    {
        $this->gateway = $gateway;

        return $this;
    }

    /**
     * Get gateway
     *
     * @return string 
     */
    public function getGateway()
    {
        return $this->gateway;
    }

    /**
     * Set details
     *
     * @param string $details
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
     * @return string 
     */
    public function getDetails()
    {
        return $this->details;
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
     * __toString()
     */
    public function __toString()
    {
        return $this->getTx();
    }

    public function setUpdatedValue()
    {
        $this->updated = new \DateTime();
    }

    public function setCreatedValue()
    {
        $this->created = new \DateTime();
    }
}

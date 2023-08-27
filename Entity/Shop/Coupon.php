<?php

namespace Maci\PageBundle\Entity\Shop;

use Maci\PageBundle\Entity\Media\Media;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Maci\PageBundle\Entity\Shop\Record;

/**
 * Coupon
 */
class Coupon
{
	/**
	 * @var integer
	 */
	private $id;

	/**
	 * @var string
	 */
	private $code;

	/**
	 * @var string
	 */
	private $discount;

	/**
	 * @var \DateTime
	 */
	private $start;

	/**
	 * @var \DateTime
	 */
	private $expire;

	/**
	 * @var boolean
	 */
	private $enable;

	/**
	 * @var json
	 */
	private $terms;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->enable = false;
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
	 * Set code
	 *
	 * @param string $code
	 * @return Coupon
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
	 * Set discount
	 *
	 * @param string $discount
	 * @return Coupon
	 */
	public function setDiscount($discount)
	{
		$this->discount = $discount;
	
		return $this;
	}

	/**
	 * Get discount
	 *
	 * @return string 
	 */
	public function getDiscount()
	{
		return $this->discount;
	}

	/**
	 * Set start
	 *
	 * @param \DateTime $start
	 * @return Coupon
	 */
	public function setStart($start)
	{
		$this->start = $start;

		return $this;
	}

	/**
	 * Get start
	 *
	 * @return \DateTime 
	 */
	public function getStart()
	{
		return $this->start;
	}

	/**
	 * Set expire
	 *
	 * @param \DateTime $expire
	 * @return Coupon
	 */
	public function setExpire($expire)
	{
		$this->expire = $expire;

		return $this;
	}

	/**
	 * Get expire
	 *
	 * @return \DateTime 
	 */
	public function getExpire()
	{
		return $this->expire;
	}

	public function setEnable($enable)
	{
		$this->enable = $enable;

		return $this;
	}

	public function getEnable()
	{
		return $this->enable;
	}

	/**
	 * Set terms
	 *
	 * @param string $terms
	 * @return Coupon
	 */
	public function setTerms($terms)
	{
		$this->terms = $terms;
	
		return $this;
	}

	/**
	 * Get terms
	 *
	 * @return string 
	 */
	public function getTerms()
	{
		return $this->terms;
	}

	/**
	 * __toString()
	 */
	public function __toString()
	{
		return 'Coupon#' . ($this->id ? $this->id : 'new');
	}
}

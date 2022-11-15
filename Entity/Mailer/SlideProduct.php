<?php

namespace Maci\PageBundle\Entity\Mailer;

/**
 * Slide
 */
class SlideProduct
{
	/**
	 * @var integer
	 */
	private $id;

	/**
	 * @var int
	 */
	private $position;

	/**
	 * @var \Maci\PageBundle\Entity\Shop\Product
	 */
	private $product;

	/**
	 * @var \Maci\PageBundle\Entity\Mailer\Slide
	 */
	private $slide;


	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->position = 0;
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
	 * Set position
	 *
	 * @param int $position
	 *
	 * @return Slide
	 */
	public function setPosition($position)
	{
		$this->position = $position;

		return $this;
	}

	/**
	 * Get position
	 *
	 * @return int
	 */
	public function getPosition()
	{
		return $this->position;
	}

	/**
	 * Set product
	 *
	 * @param string $product
	 *
	 * @return Slide
	 */
	public function setProduct(\Maci\PageBundle\Entity\Shop\Product $product)
	{
		$this->product = $product;

		return $this;
	}

	/**
	 * Get product
	 *
	 * @return string
	 */
	public function getProduct()
	{
		return $this->product;
	}

	/**
	 * Set slide
	 *
	 * @param string $slide
	 *
	 * @return Slide
	 */
	public function setSlide(\Maci\PageBundle\Entity\Mailer\Slide $slide)
	{
		$this->slide = $slide;

		return $this;
	}

	/**
	 * Get slide
	 *
	 * @return string
	 */
	public function getSlide()
	{
		return $this->slide;
	}

	public function getWebPreview()
	{
		if ($this->product)
			return $this->product->getWebPreview();

		return '/images/defaults/document-icon.png';
	}

	public function getName()
	{
		return $this->product ? $this->product->getName() : '';
	}

	/**
	 * _toString()
	 */
	public function __toString()
	{
		return 'MailSlideProduct_'.($this->getId()?$this->getId():'New');
	}
}

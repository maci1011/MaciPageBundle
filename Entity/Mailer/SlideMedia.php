<?php

namespace Maci\PageBundle\Entity\Mailer;

/**
 * Slide
 */
class SlideMedia
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
	 * @var \Maci\PageBundle\Entity\Shop\Media
	 */
	private $media;

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
	 * Set media
	 *
	 * @param string $media
	 *
	 * @return Slide
	 */
	public function setMedia(\Maci\PageBundle\Entity\Media\Media $media)
	{
		$this->media = $media;

		return $this;
	}

	/**
	 * Get media
	 *
	 * @return string
	 */
	public function getMedia()
	{
		return $this->media;
	}

	public function getWebPreview()
	{
		return $this->media ? $this->media->getWebPreview() : '/images/defaults/document-icon.png';
	}

	public function getName()
	{
		return $this->media ? $this->media->getName() : '';
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

	/**
	 * _toString()
	 */
	public function __toString()
	{
		return 'MailSlideMedia_'.($this->getId()?$this->getId():'New');
	}
}

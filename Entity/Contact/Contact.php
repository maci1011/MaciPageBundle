<?php

namespace Maci\PageBundle\Entity\Contact;

use Doctrine\Common\Collections\ArrayCollection;

class Contact
{
	/**
	 * @var bigint $id
	 */
	private $id;

	/**
	 * @var string $name
	 */
	private $name;

	/**
	 * @var string $surname
	 */
	private $surname;

	/**
	 * @var string $email
	 */
	private $email;

	/**
	 * @var text $message
	 */
	private $message;

	/**
	 * @var \Doctrine\Common\Collections\Collection
	 */
	private $media;

	/**
	 * @var \DateTime
	 */
	private $created;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->media = new \Doctrine\Common\Collections\ArrayCollection();
	}


	/**
	 * Get id
	 *
	 * @return bigint 
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Set name
	 *
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
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
	 * Set surname
	 *
	 * @param string $surname
	 */
	public function setSurname($surname)
	{
		$this->surname = $surname;
	}

	/**
	 * Get surname
	 *
	 * @return string 
	 */
	public function getSurname()
	{
		return $this->surname;
	}

	/**
	 * Get full name
	 *
	 * @return string 
	 */
	public function getFullName()
	{
		return $this->surname . ' ' . $this->name;
	}

	/**
	 * Set email
	 *
	 * @param string $email
	 */
	public function setEmail($email)
	{
		$this->email = $email;
	}

	/**
	 * Get email
	 *
	 * @return string 
	 */
	public function getEmail()
	{
		return $this->email;
	}

	public function getRecipient()
	{
		if (!$this->email)
			return null;

		return [$this->email => $this->getFullName()];
	}

	/**
	 * Set message
	 *
	 * @param text $message
	 */
	public function setMessage($message)
	{
		$this->message = $message;
	}

	/**
	 * Get message
	 *
	 * @return text 
	 */
	public function getMessage()
	{
		return $this->message;
	}

	/**
	 * Set created
	 *
	 * @param \DateTime $created
	 * @return Post
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
	 * setCreatedValue
	 */
	public function setCreatedValue()
	{
		$this->created = new \DateTime();
	}

	/**
	 * Add media
	 *
	 * @param \Maci\PageBundle\Entity\Media $media
	 * @return Contact
	 */
	public function addMedia(\Maci\PageBundle\Entity\Media\Media $media)
	{
		$this->media[] = $media;

		return $this;
	}

	/**
	 * Remove media
	 *
	 * @param \Maci\PageBundle\Entity\Media $media
	 */
	public function removeMedia(\Maci\PageBundle\Entity\Media\Media $media)
	{
		$this->media->removeElement($media);
	}

	/**
	 * Get media
	 *
	 * @return \Doctrine\Common\Collections\Collection 
	 */
	public function getMedia()
	{
		return $this->media;
	}

	/**
	 * __toString()
	 */
	public function __toString()
	{
		return ( $this->getName().' '.$this->getSurname() );
	}
}

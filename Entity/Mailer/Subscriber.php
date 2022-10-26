<?php

namespace Maci\PageBundle\Entity\Mailer;

use Symfony\Component\Intl\Intl;

/**
 * Subscriber
 */
class Subscriber
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
	private $mail;

	/**
	 * @var string
	 */
	private $locale;

	/**
	 * @var string
	 */
	private $token;

	/**
	 * @var boolean
	 */
	private $removed;

	/**
	 * @var \DateTime
	 */
	private $created;

	/**
	 * @var \DateTime
	 */
	private $updated;

	/**
	 * @var \Maci\UserBundle\Entity\User
	 */
	private $user;


	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->token = md5(uniqid());
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
	 *
	 * @return Subscriber
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
	 * Get header
	 *
	 * @return string 
	 */
	public function getHeader()
	{
		return $this->getName();
	}

	/**
	 * Set mail
	 *
	 * @param string $mail
	 *
	 * @return Subscriber
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

	public function getRecipient()
	{
		return $this->mail ? [$this->mail => $this->getName()] : null;
	}

	/**
	 * Set locale
	 *
	 * @param string $locale
	 * @return Subscriber
	 */
	public function setLocale($locale)
	{
		$this->locale = $locale;

		return $this;
	}

	/**
	 * Get locale
	 *
	 * @return string 
	 */
	public function getLocale()
	{
		return $this->locale;
	}

	/**
	 * @return string 
	 */
	// public function getLocaleName()
	// {
	// 	return Intl::getRegionBundle()->getCountryName( $this->country );
	// }

	/**
	 * Set token
	 *
	 * @param string $token
	 * @return Subscriber
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

	/**
	 * Set removed
	 *
	 * @param boolean $removed
	 *
	 * @return Subscriber
	 */
	public function setRemoved($removed)
	{
		$this->removed = $removed;

		return $this;
	}

	/**
	 * Get removed
	 *
	 * @return boolean
	 */
	public function getRemoved()
	{
		return $this->removed;
	}

	public function isRemoved()
	{
		return $this->removed;
	}

	/**
	 * Set created
	 *
	 * @param \DateTime $created
	 *
	 * @return Subscriber
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
	 *
	 * @return Subscriber
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
	 * Set user
	 *
	 * @param \Maci\UserBundle\Entity\User $user
	 *
	 * @return Subscriber
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
	 * Get full name
	 *
	 * @return string 
	 */
	static public function getNameFromMail($mail)
	{
		$name = explode('@', $mail)[0];
		$name = str_replace('-', ' ', $name);
		$name = str_replace('_', ' ', $name);
		$name = str_replace('.', ' ', $name);
		$name = ucwords($name);
		return $name;
	}


	public function setNameValue()
	{
		if (!$this->name) $this->name = $this->getNameFromMail($this->mail);
	}

	public function setCreatedValue()
	{
		$this->created = new \DateTime();
	}

	public function setUpdatedValue()
	{
		$this->updated = new \DateTime();
	}

	public function unsubscribe()
	{
		$this->removed = true;
	}

	/**
	 * __toString()
	 */
	public function __toString()
	{
		return 'Subscriber#'.$this->getId();
	}
}

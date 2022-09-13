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
	private $surname;

	/**
	 * @var \Date
	 */
	private $birthdate;

	/**
	 * @var boolean
	 */
	private $sex;

	/**
	 * @var string
	 */
	private $mail;

	/**
	 * @var string
	 */
	private $mobile;

	/**
	 * @var string
	 */
	private $country;

	/**
	 * @var string
	 */
	private $locale;

	/**
	 * @var string
	 */
	private $notes;

	/**
	 * @var string
	 */
	private $token;

	/**
	 * @var boolean
	 */
	private $newsletter;

	/**
	 * @var boolean
	 */
	private $sms;

	/**
	 * @var boolean
	 */
	private $phone;

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
	 * @var \Maci\UserBundle\Entity\Address
	 */
	private $address;


	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->token = md5(uniqid());
		$this->sex = false;
		$this->removed = false;
		$this->newsletter = false;
		$this->sms = false;
		$this->phone = false;
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
	 * Set surname
	 *
	 * @param string $surname
	 * @return Subscriber
	 */
	public function setSurname($surname)
	{
		$this->surname = $surname;

		return $this;
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
		if (!$this->name && !$this->surname)
		{
			$name = explode('@', $this->mail)[0];
			$name = str_replace('-', ' ', $name);
			$name = str_replace('_', ' ', $name);
			$name = str_replace('.', ' ', $name);
			$name = ucwords($name);
			return $name;
		}

		return $this->surname . ' ' . $this->name;
	}

	/**
	 * Get header
	 *
	 * @return string 
	 */
	public function getHeader()
	{
		return $this->getFullName();
	}

	/**
	 * Set birthdate
	 *
	 * @param string $birthdate
	 * @return Subscriber
	 */
	public function setBirthdate($birthdate)
	{
		$this->birthdate = $birthdate;

		return $this;
	}

	/**
	 * Get birthdate
	 *
	 * @return string 
	 */
	public function getBirthdate()
	{
		return $this->birthdate;
	}

	/**
	 * Set sex
	 *
	 * @param string $sex
	 * @return Subscriber
	 */
	public function setSex($sex)
	{
		$this->sex = $sex;

		return $this;
	}

	/**
	 * Get sex
	 *
	 * @return string 
	 */
	public function getSex()
	{
		return $this->sex;
	}

	public function getSexLabel()
	{
		return $this->sex ? 'M' : 'F';
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
		$this->newsletter = true;

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
		if (!$this->mail)
			return null;

		return [$this->mail => $this->getFullName()];
	}

	/**
	 * Set mobile
	 *
	 * @param string $mobile
	 * @return Subscriber
	 */
	public function setMobile($mobile)
	{
		$this->mobile = $mobile;
		$this->phone = true;

		return $this;
	}

	/**
	 * Get mobile
	 *
	 * @return string 
	 */
	public function getMobile()
	{
		return $this->mobile;
	}

	/**
	 * Set country
	 *
	 * @param string $country
	 * @return Address
	 */
	public function setCountry($country)
	{
		$this->country = $country;

		return $this;
	}

	/**
	 * Get country
	 *
	 * @return string 
	 */
	public function getCountry()
	{
		return $this->country;
	}

	/**
	 * Get country
	 *
	 * @return string 
	 */
	public function getCountryName()
	{
		return Intl::getRegionBundle()->getCountryName( $this->country );
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
	 * Set notes
	 *
	 * @param string $notes
	 * @return Subscriber
	 */
	public function setNotes($notes)
	{
		$this->notes = $notes;

		return $this;
	}

	/**
	 * Get notes
	 *
	 * @return string 
	 */
	public function getNotes()
	{
		return $this->notes;
	}

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
	 * Set newsletter
	 *
	 * @param string $newsletter
	 * @return Subscriber
	 */
	public function setNewsletter($newsletter)
	{
		$this->newsletter = $newsletter;

		return $this;
	}

	/**
	 * Get newsletter
	 *
	 * @return string 
	 */
	public function getNewsletter()
	{
		return $this->newsletter;
	}

	public function wantsNewsletter()
	{
		return ($this->newsletter && !$this->removed);
	}

	/**
	 * Set sms
	 *
	 * @param string $sms
	 * @return Subscriber
	 */
	public function setSms($sms)
	{
		$this->sms = $sms;

		return $this;
	}

	/**
	 * Get sms
	 *
	 * @return string 
	 */
	public function getSms()
	{
		return $this->sms;
	}

	public function wantsSms()
	{
		return ($this->sms && !$this->removed);
	}

	/**
	 * Set phone
	 *
	 * @param string $phone
	 * @return Subscriber
	 */
	public function setPhone($phone)
	{
		$this->phone = $phone;

		return $this;
	}

	/**
	 * Get phone
	 *
	 * @return string 
	 */
	public function getPhone()
	{
		return $this->phone;
	}

	public function wantsCalls()
	{
		return ($this->phone && !$this->removed);
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
	 * Set address
	 *
	 * @param \Maci\UserBundle\Entity\Address $address
	 *
	 * @return Subscriber
	 */
	public function setAddress(\Maci\UserBundle\Entity\Address $address = null)
	{
		$this->address = $address;

		return $this;
	}

	/**
	 * Get address
	 *
	 * @return \Maci\UserBundle\Entity\Address
	 */
	public function getAddress()
	{
		return $this->address;
	}


	public function setUpdatedValue()
	{
		$this->updated = new \DateTime();
	}

	public function setCreatedValue()
	{
		$this->created = new \DateTime();
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

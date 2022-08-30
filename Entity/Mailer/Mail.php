<?php

namespace Maci\PageBundle\Entity\Mailer;

use Maci\PageBundle\Entity\Mailer\Slide;

/**
 * Mail
 */
class Mail
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
	private $token;

	/**
	 * @var string
	 */
	private $type;

	/**
	 * @var string
	 */
	private $sender;

	/**
	 * @var string
	 */
	private $header;

	/**
	 * @var string
	 */
	private $subject;

	/**
	 * @var string
	 */
	private $text;

	/**
	 * @var string
	 */
	private $content;

	/**
	 * @var json
	 */
	private $data;

	/**
	 * @var string
	 */
	private $locale;

	/**
	 * @var boolean
	 */
	private $public;

	/**
	 * @var boolean
	 */
	private $sended;

	/**
	 * @var bolean
	 */
	private $removed;

	/**
	 * @var \Doctrine\Common\Collections\Collection
	 */
	private $slides;

	/**
	 * @var \Maci\UserBundle\Entity\User
	 */
	private $user;


	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->template = '@MaciPage/Templates/default.html.twig';
		$this->type = $this->getTypes()[0];
		$this->token = md5(
			'MaciPageBundle_Entity_Mail-' . rand(10000, 99999) . '-' . 
			date('h') . date('i') . date('s') . date('m') . date('d') . date('Y')
		);
		$this->data = $this->getDefaultData();
		$this->public = false;
		$this->sended = false;
		$this->removed = false;
		$this->slides = new \Doctrine\Common\Collections\ArrayCollection();
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
	 * @return Mail
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
	 * Set token
	 *
	 * @param string $token
	 * @return Order
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
	 * Set type
	 *
	 * @param string $type
	 *
	 * @return Mail
	 */
	public function setType($type)
	{
		$this->type = $type;

		if ($type == 'newsletter' && !count($this->slides))
		{
			$this->addNewSlide('header');
			$this->addNewSlide('intro');
			$this->addNewSlide('last_products');
			$this->addNewSlide('footer');
		}

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

	/**
	 * Get Type Array
	 */
	static public function getTypeArray()
	{
		return [
			'Unknown' => 'unknown',
			'Message' => 'message',
			'Newsletter' => 'newsletter',
			'Notify' => 'notify'
		];
	}

	public function getTypeLabel()
	{
		return \Maci\PageBundle\MaciPageBundle::getLabel($this->type, $this->getTypeArray());
	}

	static public function getTypes()
	{
		return array_values(Mail::getTypeArray());
	}

	/**
	 * Set sender
	 *
	 * @return Mail
	 */
	public function setSender($sender, $header = false)
	{
		if (is_array($sender))
		{
			$mail = array_keys($sender)[0];
			$this->sender = $mail;
			$this->header = $sender[$mail];

			return $this;
		}

		$this->sender = $sender;

		if (!$sender)
			$this->header = null;

		elseif (is_string($header))
			$this->header = $header;

		return $this;
	}

	/**
	 * Get sender
	 *
	 * @return string
	 */
	public function getSender()
	{
		return $this->sender;
	}

	/**
	 * Set header
	 *
	 * @param string $header
	 *
	 * @return MailTranslation
	 */
	public function setHeader($header)
	{
		$this->header = $header;

		return $this;
	}

	/**
	 * Get header
	 *
	 * @return string
	 */
	public function getHeader()
	{
		return $this->header;
	}

	/**
	 * Set subject
	 *
	 * @param string $subject
	 *
	 * @return MailTranslation
	 */
	public function setSubject($subject)
	{
		$this->subject = $subject;

		return $this;
	}

	/**
	 * Get subject
	 *
	 * @return string
	 */
	public function getSubject()
	{
		return $this->subject;
	}

	/**
	 * Set text
	 *
	 * @param string $text
	 *
	 * @return MailTranslation
	 */
	public function setText($text)
	{
		$this->text = $text;

		return $this;
	}

	/**
	 * Get text
	 *
	 * @return string
	 */
	public function getText()
	{
		return $this->text;
	}

	/**
	 * Set content
	 *
	 * @param string $content
	 *
	 * @return MailTranslation
	 */
	public function setContent($content)
	{
		$this->content = $content;

		return $this;
	}

	/**
	 * Get content
	 *
	 * @return string
	 */
	public function getContent()
	{
		return $this->content;
	}

	/**
	 * Set data
	 *
	 * @param string $data
	 *
	 * @return Mail
	 */
	public function setData($data)
	{
		$this->data = $data;

		return $this;
	}

	/**
	 * Get default data
	 *
	 * @return array
	 */
	static public function getDefaultData()
	{
		return [
			'recipients' => []
		];
	}

	/**
	 * Get data
	 *
	 * @return string
	 */
	public function getData()
	{
		if (is_null($this->data)) return $this->getDefaultData();
		return $this->data;
	}

	/**
	 * Reset data
	 *
	 * @return Mail
	 */
	public function resetData()
	{
		$this->data = $this->getDefaultData();

		return $this;
	}

	public function getRecipientIndex($mail)
	{
		$list = $this->getRecipients();

		foreach ($list as $index => $recipient)
		{
			if ($recipient['mail'] == $mail)
				return $index;
		}

		return -1;
	}

	public function hasRecipient($mail)
	{
		return -1 != $this->getRecipientIndex($mail);
	}

	public function addRecipient($mail, $name)
	{
		$this->addRecipients([$mail => $name]);
	}

	public function addRecipients($array)
	{
		$list = $this->getRecipients();

		if (!$list)
			$list = [];

		foreach ($array as $mail => $name)
		{
			if ($this->hasRecipient($mail))
				continue;

			array_push($list, [
				'name' => $name,
				'mail' => $mail,
				'sended' => false
			]);
		}

		$this->data['recipients'] = $list;
	}

	public function removeRecipient($mail)
	{
		$index = $this->getRecipientIndex($mail);

		if ($index == -1)
			return;

		$list = $this->getRecipients();
		unset($list[$index]);

		$this->data['recipients'] = $list;
	}

	public function removeRecipients($array)
	{
		foreach ($array as $mail)
			$this->removeRecipient($mail);
	}

	public function getRecipients()
	{
		$data = $this->getData();
		return array_key_exists('recipients', $data) ? $data['recipients'] : false;
	}

	public function getNextRecipients($max = 0)
	{
		$data = $this->getData();
		$list = [];

		for ($i = 0; $i < count($data['recipients']); $i++)
		{
			if (!$data['recipients'][$i]['sended'])
				array_push($list, $data['recipients'][$i]);

			if (0 < $max && $max <= count($list))
				break;
		}

		return $list;
	}

	public function getRecipientByMail($mail)
	{
		$list = $this->getRecipients();

		if (!$list)
			return false;

		for ($i = 0; $i < count($list); $i++)
		{
			if ($list[$i]['mail'] == $mail && !$list[$i]['sended'])
				return $list[$i];
		}

		return false;
	}

	public function hasNextRecipients()
	{
		$list = $this->getNextRecipients(1);
		return !!count($list);
	}

	public function getNextRecipient()
	{
		$list = $this->getNextRecipients(1);
		return count($list) ? $list[0] : false;
	}

	public function getNextRecipientMail()
	{
		$recipient = $this->getNextRecipient();
		return $recipient ? $recipient['mail'] : false;
	}

	public function getNextRecipientName()
	{
		$recipient = $this->getNextRecipient();
		return $recipient ? $recipient['name'] : false;
	}

	public function addSubscribers($array)
	{
		$list = $this->getRecipients();

		if (!$list)
			$list = [];

		foreach ($array as $subscriber)
		{
			if ($subscriber->isRemoved())
				continue;

			array_push($list, [
				'name' => $subscriber->getFullName(),
				'mail' => $subscriber->getMail(),
				'sended' => false
			]);
		}

		$this->data['recipients'] = $list;

		return $this;
	}

	public function setSendedValue($mail, $value = false)
	{
		$list = $this->getRecipients();

		if (!$list)
			return false;

		for ($i = 0; $i < count($list); $i++)
		{
			if ($list[$i]['mail'] == $mail && !$list[$i]['sended'])
			{
				$list[$i]['sended'] = $value ? $value : date("c", time());
				$this->data['recipients'] = $list;
				return true;
			}
		}

		return false;
	}

	public function setNextSendedValue()
	{
		$list = $this->getRecipients();

		if (!$list)
			return;

		$found = false;
		for ($i = 0; $i < count($list); $i++)
		{
			if (!$list[$i]['sended'])
			{
				$list[$i]['sended'] = date("c", time());
				$found = true;
				break;
			}
		}

		if (!$found)
			return false;

		$this->data['recipients'] = $list;
		$this->sended = true;

		return true;
	}

	public function setSendedValues()
	{
		// $datetime = date_format(new \DateTime(), 'r');
		$list = $this->getRecipients();

		if (!$list)
			return;

		for ($i = 0; $i < count($list); $i++)
		{
			if (!$list[$i]['sended'])
				$list[$i]['sended'] = date("c", time());
		}

		$this->data['recipients'] = $list;
		$this->sended = true;
	}

	public function getBcc()
	{
		$data = $this->getData();
		return array_key_exists('bcc', $data) ? $data['bcc'] : false;
	}

	public function addBcc($list)
	{
		$list = $this->getBcc();

		if (!$list)
			$list = [];

		$list = array_merge($list, $list);

		$this->data['bcc'] = $list;

		return $this;
	}

	public function getReplyTo()
	{
		$data = $this->getData();
		return array_key_exists('reply-to', $data) ? $data['reply-to'] : false;
	}

	public function setReplyTo($mail)
	{
		$data = $this->getData();
		$data['reply-to'] = $mail;
		$this->data = $data;

		return $this;
	}

	public function addNotified($array, $sended = false)
	{
		$list = $this->getNotified();

		if (!$list)
			$list = [];

		foreach ($array as $mail => $name) {
			array_push($list, [
				'name' => $name,
				'mail' => $mail,
				'sended' => $sended
			]);
		}

		$this->data['notified'] = $list;

		return $this;
	}

	public function getNotified()
	{
		$data = $this->getData();
		return array_key_exists('notified', $data) ? $data['notified'] : false;
	}

	/**
	 * Set locale
	 *
	 * @param string $locale
	 *
	 * @return Mail
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
	 * Set public
	 *
	 * @param boolean $public
	 *
	 * @return Mail
	 */
	public function setPublic($public)
	{
		$this->public = $public;

		return $this;
	}

	/**
	 * Get public
	 *
	 * @return boolean
	 */
	public function getPublic()
	{
		return $this->public;
	}

	/**
	 * Set sended
	 *
	 * @param boolean $sended
	 *
	 * @return Mail
	 */
	public function setSended($sended)
	{
		$this->sended = $sended;

		return $this;
	}

	/**
	 * Get sended
	 *
	 * @return boolean
	 */
	public function getSended()
	{
		return $this->sended;
	}

	/**
	 * Set removed
	 *
	 * @param boolean $removed
	 *
	 * @return Mail
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

	/**
	 * Add slide
	 *
	 * @param \Maci\PageBundle\Entity\Mailer\Slide $slide
	 * @return Mail
	 */
	public function addSlide(\Maci\PageBundle\Entity\Mailer\Slide $slide)
	{
		$this->slides[] = $slide;

		return $this;
	}

	/**
	 * Remove slide
	 *
	 * @param \Maci\PageBundle\Entity\Mailer\Slide $slide
	 */
	public function removeSlide(\Maci\PageBundle\Entity\Mailer\Slide $slide)
	{
		$this->slides->removeElement($slide);
	}

	/**
	 * Get slides
	 *
	 * @return \Doctrine\Common\Collections\Collection 
	 */
	public function getSlides()
	{
		return $this->slides;
	}

	public function addNewSlide($type)
	{
		$slide = new Slide();
		$slide->setType($type);
		$slide->setMail($this);
		$this->addSlide($slide);
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

	// ---> Utils

	public function getMessage()
	{
		$message = (new \Swift_Message());

		if ($this->subject)
			$message->setSubject($this->subject);

		if ($this->sender)
			$message->setFrom($this->sender, $this->header);

		if ($this->content)
		{
			$message->setBody($this->content, 'text/html');
			if ($this->text)
				$message->addPart($this->text, 'text/plain');
		}
		else if ($this->text)
			$message->setBody($this->text, 'text/plain');

		$val = $this->getReplyTo();
		if ($val)
			$message->setReplyTo($val);

		$val = $this->getBcc();
		if ($val)
			$message->setBcc($val);

		return $message;
	}

	public function getNextMessage($mail)
	{
		$recipient = $this->getRecipientByMail($mail);

		if (!$recipient)
			return false;

		$message = $this->getMessage();
		$message->addTo($recipient['mail'], $recipient['name']);

		return $message;
	}

	public function getSwiftMessage($len = 0)
	{
		$recipients = $this->getNextRecipients($len);

		if (!$recipients)
			return false;

		$message = $this->getMessage();

		foreach ($recipients as $recipient)
			$message->addTo($recipient['mail'], $recipient['name']);

		return $message;
	}

	public function getNext()
	{
		return $this->getSwiftMessage(1);
	}

	public function isNew()
	{
		return !$this->id;
	}

	/**
	 * toString()
	 */
	public function __toString()
	{
		return 'Mail_'.$this->getToken();
	}
}

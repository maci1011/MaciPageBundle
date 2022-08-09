<?php

namespace Maci\PageBundle\Entity\Mailer;

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

		if ($header)
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

	public function getRecipients()
	{
		$data = $this->getData();
		return array_key_exists('recipients', $data) ? $data['recipients'] : false;
	}

	public function addRecipients($array)
	{
		$list = $this->getRecipients();

		if (!$list)
			$list = [];

		foreach ($array as $mail => $name)
		{
			array_push($list, [
				'name' => $name,
				'mail' => $mail,
				'sended' => false
			]);
		}

		$this->data['recipients'] = $list;

		return $this;
	}

	public function setSendedValues($datetime)
	{
		$list = $this->getRecipients();

		if (!$list)
			return;

		for ($i = 0; $i < count($list); $i++)
		{
			if (!$list[$i]['sended'])
				$list[$i]['sended'] = $datetime;
		}

		$this->data['recipients'] = $list;
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

	public function getNotified()
	{
		$data = $this->getData();
		return array_key_exists('notified', $data) ? $data['notified'] : false;
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

	public function getSwiftMessage()
	{
		$message = (new \Swift_Message());
		$data = $this->getData();

		if ($this->subject)
			$message->setSubject($this->subject);

		if ($this->sender)
			$message->setFrom($this->sender, $this->header);

		$rt = $this->getReplyTo();
		if ($rt)
			$message->setReplyTo($rt);

		$recipients = $this->getRecipients();
		if ($recipients) foreach ($recipients as $recipient)
			$message->addTo($recipient['mail'], $recipient['name']);

		if ($this->content)
		{
			$message->setBody($this->content, 'text/html');
			if ($this->text)
				$message->addPart($this->text, 'text/plain');
		}
		else if ($this->text)
			$message->setBody($this->text, 'text/plain');

		if (array_key_exists('bcc', $data))
			$message->setBcc($data['bcc']);

		return $message;
	}

	// public function getSwiftMessage()
	// {
	// 	$to = $this->getCurrentTo();

	// 	if (!$to) {
	// 		return false;
	// 	}

	// 	$message = (new \Swift_Message())
	// 		->setSubject($this->getSubject())
	// 		->setFrom($this->getFrom(), $this->getHeader())
	// 		->setTo($to[0], $to[1])
	// 		->setBcc($this->getBcc())
	// 		->setBody($this->getContent(), 'text/html')
	// 		->addPart($this->getText(), 'text/plain')
	// 	;

	// 	return $message;
	// }

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

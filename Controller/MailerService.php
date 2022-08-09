<?php

namespace Maci\PageBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Maci\PageBundle\Entity\Mailer\Mail;
use Maci\PageBundle\Entity\Mailer\Subscriber;

class MailerService extends AbstractController
{
	private $om;

	private $mailer;

	private $env;

	private $server_mail;

	private $server_header;

	public function __construct(
		ObjectManager $objectManager,
		$_mailer,
		string $_env,
		string $_server_mail,
		string $_server_header
	) {
		$this->om = $objectManager;
		$this->mailer = $_mailer;
		$this->env = $_env;
		$this->server_mail = $_server_mail;
		$this->server_header = $_server_header;
	}

	public function send(Mail $mail, $notify = false)
	{
		$message = $mail->getSwiftMessage($mail);

		if (!$message->getTo())
			return false;

		if (!$message->getFrom())
			$message->setFrom($this->server_mail, $this->server_header);

		// Send Mail

		// ---> send message
		if ($this->env == "prod")
			$this->mailer->send($message);

		$mail->setSendedValues(date_format(new \DateTime(), 'r'));

		if ($mail->isNew())
			$this->om->persist($mail);

		// Notify

		if (!$notify)
		{
			$this->om->flush();
			return true;
		}

		$messify = clone $message;
		$messify->setTo(null);

		if (!is_array($notify))
			$notify = [$this->server_mail => $this->server_header];

		foreach ($notify as $notMail => $header)
			$messify->addTo($notMail, $header);

		// ---> send notify
		if ($this->env == "prod")
			$this->mailer->send($messify);

		$mail->addNotified($notify, date_format(new \DateTime(), 'r'));

		$this->om->flush();

		return true;
	}
}

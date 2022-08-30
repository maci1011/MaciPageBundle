<?php

namespace Maci\PageBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Maci\PageBundle\Entity\Mailer\Mail;
use Maci\PageBundle\Entity\Mailer\Subscriber;

class MailerService extends AbstractController
{
	private $om;

	private $mailer;

	private $templating;

	private $env;

	private $server_mail;

	private $server_header;

	public function __construct(
		ObjectManager $objectManager,
		$_mailer,
		TwigEngine $templating,
		string $_env,
		string $_server_mail,
		string $_server_header
	) {
		$this->om = $objectManager;
		$this->mailer = $_mailer;
		$this->templating = $templating;
		$this->env = $_env;
		$this->server_mail = $_server_mail;
		$this->server_header = $_server_header;
	}

	public function send(Mail &$mail, $notify = false)
	{
		$message = $this->sendMail($mail);

		if (!$message)
			return false;

		if (!$notify)
			return true;

		// Notify

		if (!$this->sendNotify($message, $notify))
			return false;

		$mail->addNotified($notify, date_format(new \DateTime(), 'r'));

		$this->om->flush();

		return true;
	}

	public function sendNext(Mail &$mail, $subscriber)
	{
		if (!$subscriber)
			return false;

		$message = $this->sendMail($mail, $subscriber);

		if (!$message)
			return false;

		return true;
	}

	public function sendMail(Mail &$mail, $subscriber = false)
	{
		if (!$mail->getSender())
			$mail->setSender($this->server_mail, $this->server_header);

		if (!$mail->getContent())
		{
			$params = ['mail' => $mail];

			if ($subscriber)
				$params['subscriber'] = $subscriber;

			if (array_key_exists('template', $mail->getData()))
				$mail->setContent(
					$this->templating->render($mail->getData()['template']['path'], array_merge(
						$params, $mail->getData()['template']['params']
					))
				);

			else
				$mail->setContent(
					$this->templating->render('@MaciPage/Mailer/show.html.twig', $params)
				);
		}

		if ($subscriber)
			$message = $mail->getNextMessage($subscriber->getMail());
		else
			$message = $mail->getSwiftMessage();

		if (!$this->sendMessage($message))
			return false;

		// if ($subscriber)
		// 	$mail->setSendedValue($subscriber->getMail());
		// else
		// 	$mail->setSendedValues();

		if ($mail->isNew())
			$this->om->persist($mail);

		$this->om->flush();

		return $message;
	}

	public function sendMessage(\Swift_Message $message)
	{
		if (!$message)
			return false;

		// Send Message
		if ($this->env == "prod")
			$this->mailer->send($message);

		return true;
	}

	public function sendNotify(\Swift_Message $message, $to)
	{
		if (!$message)
			return false;

		$notify = clone $message;
		$notify->setSubject('Notify: ' . $notify->getSubject());
		$notify->setTo(null);

		if (!is_array($to))
			$to = [$this->server_mail => $this->server_header];

		foreach ($to as $mail => $header)
			$notify->addTo($mail, $header);

		// ---> send notify
		if ($this->env == "prod")
			$this->mailer->send($notify);

		return true;
	}
}

<?php

namespace Maci\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Locales;

use Maci\PageBundle\Entity\Mailer\Mail;
use Maci\PageBundle\Entity\Mailer\Subscriber;
use Maci\PageBundle\Form\Mailer\SubscribeType;


class MailerController extends AbstractController
{
	public function indexAction()
	{
		// return $this->render('@MaciPage/Mailer/index.html.twig');
		return $this->redirect($this->generateUrl('maci_mailer_notifications'));
	}

	public function userMailsAction()
	{
		$list = $this->getDoctrine()->getManager()
			->getRepository('MaciPageBundle:Mailer\Mail')
			->getUserMails( $this->getUser() );

		return $this->render('@MaciPage/Mailer/user_mails.html.twig', array('list' => $list));
	}

	public function adminShowAction($token)
	{
		if (false === $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
			return $this->redirect($this->generateUrl('maci_homepage'));

		$mail = $this->getDoctrine()->getManager()
			->getRepository('MaciPageBundle:Mailer\Mail')
			->findOneByToken($token);

		$subscriber = $this->getARandomSubscriber();

		return $this->render('@MaciPage/Mailer/show.html.twig', [
			'mail' => $mail,
			'subscriber' => $subscriber
		]);
	}

	public function showAction($token)
	{
		$mail = $this->getDoctrine()->getManager()
			->getRepository('MaciPageBundle:Mailer\Mail')
			->findOneByToken($token);

		if (!$mail->isPublic())
			return $this->redirect($this->generateUrl('maci_homepage'));

		return $this->render('@MaciPage/Mailer/show.html.twig', [
			'mail' => $mail
		]);
	}

	public function subscribeAction(Request $request)
	{
		$subscriber = new Subscriber;

		$form = $this->getSubscribeForm($subscriber);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			$em = $this->getDoctrine()->getManager();

			$item = $em->getRepository('MaciPageBundle:Mailer\Subscriber')
				->findOneByMail($form->getData()->getMail());

			if ($item && !$item->getRemoved())
			{
				$this->get('session')->getFlashBag()->add('danger', $this->get('maci.translator')->getText('error.subscribe-error', 'This mail cannot be added. Try with another one.'));
				return $this->render('@MaciPage/Mailer/subscribe.html.twig', array(
					'form' => $form->createView()
				));
			}

			$subscriber->setLocale($request->getLocale());

			$em->persist($subscriber);
			$em->flush();

			$mt = $this->get('maci.translator');

			$mail = new Mail();
			$mail
				->setName('SubscriptionCompleted')
				->setType('message')
				->setSubject($mt->getLabel('newsletter.subscribtion-completed.mail-title', 'Subscription Completed'))
				->setReplyTo([$this->get('service_container')->getParameter('contact_email') => $this->get('service_container')->getParameter('contact_email_int')])
				->setSender([$this->get('service_container')->getParameter('server_email') => $this->get('service_container')->getParameter('server_email_int')])
				->addSubscribers([$subscriber])
				->setLocale($request->getLocale())
				// ->setText($this->renderView('@MaciPage/Email/subscription_complete.txt.twig', ['subscriber' => $subscriber]))
				->setContent($this->renderView('@MaciPage/Email/subscription_complete.html.twig', ['subscriber' => $subscriber]))
			;

			$this->get('maci.mailer')->send($mail);

			return $this->redirect($this->generateUrl('maci_page', [
				'path' => $this->get('maci.translator')->getRoute('newsletter.subscribe-completed', 'subscribtion-completed')
			]));
		}

		return $this->render('@MaciPage/Mailer/subscribe.html.twig', [
			'form' => $form->createView()
		]);
	}

	public function manageRedirectAction($token)
	{
		return $this->redirect($this->generateUrl('maci_mailer_manage', ['token' => $token]));
	}

	public function manageAction($token)
	{
		$subscriber = $this->getDoctrine()->getManager()
			->getRepository('MaciPageBundle:Mailer\Subscriber')
			->findOneByToken($token);

		if ($subscriber->getRemoved())
			return $this->redirect($this->generateUrl('maci_homepage'));

		return $this->render('@MaciPage/Mailer/manage.html.twig', [
			'subscriber' => $subscriber
		]);
	}

	public function unsubscribeAction($token)
	{
		$om = $this->getDoctrine()->getManager();
		$subscriber = $om->getRepository('MaciPageBundle:Mailer\Subscriber')
			->findOneByToken($token);

		$subscriber->unsubscribe();
		$om->flush();

		return $this->redirect($this->generateUrl('maci_page', ['path' => 'unsubscribed']));
	}

	public function getSubscribeForm(&$subscriber)
	{
		if (!$subscriber)
			$subscriber = new Subscriber;
		
		$choices = [];
		$choices[ucfirst(Locales::getName('it'))] = 'it';
		$choices[ucfirst(Locales::getName('en'))] = 'en';

		return $this->createForm(SubscribeType::class, $subscriber, array(
			'action' => $this->generateUrl('maci_mailer_subscribe'),
			'method' => 'POST',
			'env' => $this->get('kernel')->getEnvironment(),
			'locales' => $choices
		));
	}

	public function templatesAction()
	{
		if (false === $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
			return $this->redirect($this->generateUrl('maci_homepage'));

		return $this->render('@MaciPage/Mailer/templates.html.twig');
	}

	public function orderPlacedTemplateAction()
	{
		return $this->render('@MaciPage/Email/order_placed.html.twig', [
			'order' => $this->getARandomOrder()
		]);
	}

	public function orderCompletedTemplateAction()
	{
		return $this->render('@MaciPage/Email/order_completed.html.twig', [
			'order' => $this->getARandomOrder()
		]);
	}

	public function orderConfirmedTemplateAction()
	{
		return $this->render('@MaciPage/Email/order_confirmed.html.twig', [
			'order' => $this->getARandomOrder()
		]);
	}

	public function orderShippedTemplateAction()
	{
		return $this->render('@MaciPage/Email/order_shipped.html.twig', [
			'order' => $this->getARandomOrder()
		]);
	}

	public function orderInvoiceTemplateAction()
	{
		return $this->render('@MaciPage/Email/order_invoice.html.twig', [
			'order' => $this->getARandomOrder()
		]);
	}

	public function subscriptionCompleteTemplateAction()
	{
		return $this->render('@MaciPage/Email/subscription_complete.html.twig', [
			'subscriber' => $this->getARandomSubscriber()
		]);
	}

	public function getARandomOrder()
	{
		$orders = $this->getDoctrine()->getManager()
			->getRepository('MaciPageBundle:Order\Order')
			->findBy(['status' => ['confirm', 'complete']], ['id' => 'DESC']);

		return $orders[rand(0,count($orders) - 1)];
	}

	public function getARandomSubscriber()
	{
		$subscribers = $this->getDoctrine()->getManager()
			->getRepository('MaciPageBundle:Mailer\Subscriber')
			->findBy(['removed' => false], ['id' => 'DESC']);

		return $subscribers[rand(0,count($subscribers) - 1)];
	}

	public function sendPageAction()
	{
		if (false === $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
			return $this->redirect($this->generateUrl('maci_homepage'));

		$list = $this->getDoctrine()->getManager()
			->getRepository('MaciPageBundle:Mailer\Mail')
			->findBy(['sended' => false, 'removed' => false], ['id' => 'DESC']);

		return $this->render('@MaciPage/Mailer/send_page.html.twig', [
			'list' => $list
		]);
	}

	public function sendMailAction($token)
	{
		if (false === $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
			return $this->redirect($this->generateUrl('maci_homepage'));

		$item = $this->getDoctrine()->getManager()
			->getRepository('MaciPageBundle:Mailer\Mail')
			->findOneByToken($token);

		return $this->render('@MaciPage/Mailer/send_mail.html.twig', [
			'item' => $item
		]);
	}

	public function getNextsAction(Request $request)
	{
		if (false === $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
			return $this->redirect($this->generateUrl('maci_homepage'));

		if (!$request->isXmlHttpRequest())
			return $this->redirect($this->generateUrl('maci_homepage'));

		if ($request->getMethod() !== 'POST')
			return new JsonResponse(['success' => false, 'error' => 'Bad Request.'], 200);

		$mail = $this->getDoctrine()->getManager()
			->getRepository('MaciPageBundle:Mailer\Mail')
			->findOneById($request->get('id'));

		if (!$mail)
			return new JsonResponse(['success' => false, 'error' => 'Mail not found.'], 200);

		$list = $mail->getNextRecipients($request->get('max', 1));

		if (!count($list))
			return new JsonResponse(['success' => true, 'end' => true], 200);

		return new JsonResponse(['success' => true, 'end' => false, 'list' => $list]);
	}

	public function sendNextAction(Request $request)
	{
		if (false === $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
			return $this->redirect($this->generateUrl('maci_homepage'));

		if (!$request->isXmlHttpRequest())
			return $this->redirect($this->generateUrl('maci_homepage'));

		if ($request->getMethod() !== 'POST')
			return new JsonResponse(['success' => false, 'error' => 'Bad Request.'], 200);

		$om = $this->getDoctrine()->getManager();
		$mail = $om->getRepository('MaciPageBundle:Mailer\Mail')
			->findOneById($request->get('id'));

		if (!$mail)
			return new JsonResponse(['success' => false, 'error' => 'Mail not found.'], 200);

		if (!$mail->hasNextRecipients())
			return new JsonResponse(['success' => true, 'end' => true], 200);

		$subscriber = $om->getRepository('MaciPageBundle:Mailer\Subscriber')
			->findOneByMail($mail->getNextRecipientMail());

		if (!$subscriber)
			return new JsonResponse(['success' => false, 'error' => 'Subscriber not found.'], 200);

		$this->get('maci.mailer')->sendNext($mail, $subscriber);

		return new JsonResponse(['success' => true, 'end' => !$mail->hasNextRecipients()], 200);
	}

	public function importAction()
	{
		if (false === $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
			return $this->redirect($this->generateUrl('maci_homepage'));

		return $this->render('@MaciPage/Mailer/import.html.twig');
	}
}

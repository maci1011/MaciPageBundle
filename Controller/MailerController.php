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

	public function showAction($token)
	{
		$mail = $this->getDoctrine()->getManager()
			->getRepository('MaciPageBundle:Mailer\Mail')
			->findOneByToken($token);

		$user = $this->getUser();

		return $this->render('@MaciPage/Mailer/show.html.twig', [
			'mail' => $mail,
			'user' => $user
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

			if ($item && !$item->getRemoved()) {
				$this->get('session')->getFlashBag()->add('danger', $this->get('maci.translator')->getText('error.subscribe-error', 'This mail cannot be added. Try with another one.'));
				return $this->render('@MaciPage/Mailer/subscribe.html.twig', array(
					'form' => $form->createView()
				));
			}

			$subscriber->setLocale($request->getLocale());

			$em->persist($subscriber);
			$em->flush();

			return $this->redirect($this->generateUrl('maci_page', array('path' => $this->get('maci.translator')->getRoute('newsletter.subscribe-completed', 'subscribtion-completed'))));
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

	public function sendPageAction()
	{
		$list = $this->getDoctrine()->getManager()
			->getRepository('MaciPageBundle:Mailer\Mail')
			->findBy(['sended' => false, 'removed' => false], ['id' => 'DESC']);

		return $this->render('@MaciPage/Mailer/send_page.html.twig', [
			'list' => $list
		]);
	}

	public function sendMailAction($token)
	{
		$item = $this->getDoctrine()->getManager()
			->getRepository('MaciPageBundle:Mailer\Mail')
			->findOneByToken($token);

		return $this->render('@MaciPage/Mailer/send_mail.html.twig', [
			'item' => $item
		]);
	}

	public function templatesAction()
	{
		return $this->render('@MaciPage/Mailer/templates.html.twig');
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

	public function getNextsAction(Request $request)
	{
		if (!$request->isXmlHttpRequest()) {
			return $this->redirect($this->generateUrl('homepage'));
		}

		if ($request->getMethod() !== 'POST') {
			return new JsonResponse(['success' => false, 'error' => 'Bad Request.'], 200);
		}

		$mail = $this->getDoctrine()->getManager()
			->getRepository('MaciPageBundle:Mailer\Mail')
			->findOneById($request->get('id'));

		if (!$mail) {
			return new JsonResponse(['success' => false, 'error' => 'Mail not found.'], 200);
		}

		$data = $mail->getData();
		$list = [];

		for ($i=0; $i < count($data['recipients']); $i++) {
			if ($data['recipients'][$i]['sent'] === "false") {
				$list[count($list)] = intval($data['recipients'][$i]['id']);
			}
			if (7 < count($list)) {
				break;
			}
		}

		if (!count($list)) {
			return new JsonResponse(['success' => true, 'end' => true], 200);
		}

		return new JsonResponse(['success' => true, 'list' => $list]);
	}

	public function sendNextAction(Request $request)
	{
		if (!$request->isXmlHttpRequest()) {
			return $this->redirect($this->generateUrl('homepage'));
		}

		if ($request->getMethod() !== 'POST') {
			return new JsonResponse(['success' => false, 'error' => 'Bad Request.'], 200);
		}

		$mail = $this->getDoctrine()->getManager()
			->getRepository('MaciPageBundle:Mailer\Mail')
			->findOneById($request->get('id'));

		if (!$mail) {
			return new JsonResponse(['success' => false, 'error' => 'Mail not found.'], 200);
		}

		$data = $mail->getData();

		$index = -1;
		$id = false;

		for ($i=0; $i < count($data['recipients']); $i++) {
			if ($data['recipients'][$i]['sent'] === "false") {
				$index = $i;
				$id = intval($data['recipients'][$i]['id']);
				break;
			}
		}

		if (!$id) {
			return new JsonResponse(['success' => true, 'end' => true], 200);
		}

		$subscriber = $this->getDoctrine()->getManager()
			->getRepository('MaciPageBundle:Subscriber')
			->findOneById($id);

		if (!$subscriber) {
			return new JsonResponse(['success' => false, 'error' => 'Subscriber not found.'], 200);
		}

		$message = $mail->getSwiftMessage();

		if (!$mail->getSender()) {
			$message->setFrom(
				$this->get('service_container')->getParameter('server_email'),
				$this->get('service_container')->getParameter('server_email_int')
			);
		}

		if (!$mail->getContent()) {
			if (array_key_exists('template', $mail->getData())) {
				$message->setBody(
					$this->renderView($mail->getData()['template']['path'], array_merge(['mail' => $mail, 'subscriber' => $subscriber], $mail->getData()['template']['params']))
				);
			} else {
				$message->setBody(
					$this->renderView('@MaciPage/Mailer/show.html.twig', ['mail' => $mail, 'subscriber' => $subscriber])
				);
			}
		}

		$message->setTo($subscriber->getMail(), $subscriber->getHeader());

		// ---> send message
		if ($this->container->get('kernel')->getEnvironment() == "prod") $this->get('mailer')->send($message);

		$data['recipients'][$index]['sent'] = date("c", time());
		$data['recipients'][$index]['header'] = $subscriber->getHeader();
		$data['recipients'][$index]['mail'] = $subscriber->getMail();

		$mail->setData($data);

		$em = $this->getDoctrine()->getManager();
		$em->flush();

		return new JsonResponse(['success' => true, 'id' => $id, 'data' => $data['recipients'][$index]], 200);
	}

	public function importAction()
	{
		return $this->render('@MaciPage/Mailer/import.html.twig');
	}
}

<?php

namespace Maci\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class PageController extends AbstractController
{
	public function indexAction()
	{
		return $this->redirect($this->generateUrl('maci_homepage'));
	}

	public function localIndexAction(Request $request)
	{
		return $this->renderByPath($request, 'homepage', false);
	}

	public function pageAction(Request $request, $path)
	{
		return $this->renderByPath($request, $path, false);
	}

	public function redirectAction(Request $request, $redirect)
	{
		$page = $this->getDoctrine()->getManager()->getRepository('MaciPageBundle:Page\Page')
			->findOneBy(array(
				'redirect' => $redirect,
				'removed' => false
			));

		if ($page) {
			return $this->redirectTo($page);
		}

		$album = $this->getDoctrine()->getManager()->getRepository('MaciPageBundle:Media\Album')
			->findOneBy(array(
				'redirect' => $redirect
			));

		if ($album) {
			return $this->redirect($this->generateUrl('maci_media_album', array('id' => $album->getId())));
		}

		return $this->render('@MaciPage/Page/notfound.html.twig');
	}

	public function pageNotFoundAction()
	{
		$page = $this->getDoctrine()->getManager()->getRepository('MaciPageBundle:Page\Page')
			->findOneBy(array(
				'path' => 'page-not-found',
				'removed' => false
			));

		if (!$page)
			return $this->render('MaciPageBundle:Page:notfound.html.twig');

		$template = $page->getTemplate();

		if (!$template || !$this->get('templating')->exists($template))
			$template = '@MaciPage/Page/page.html.twig';

		return $this->render($template, ['page' => $page]);
	}

	public function renderByPath($request, $path, $template)
	{
		// var_dump( $this->get('maci.orders')->getCountriesArray() ); die();

		$page = $this->getDoctrine()->getManager()->getRepository('MaciPageBundle:Page\Page')
			->findOneBy([
				'locale' => $request->getLocale(),
				'path' => $path,
				'removed' => false
			]);

		if (!$page) {
			$default = $this->getTemplateByPath($request, $path);
			if($default) {
				return $this->render($default, array('page' => false));
			}
			return $this->redirect($this->generateUrl('maci_page_not_found'));
		}

		if (strlen($page->getMoved())) {
			return $this->redirectTo($page->getMoved());
		}

		$template = $page->getTemplate();

		if (!$template || !$this->get('templating')->exists($template)) {
			$template = '@MaciPage/Page/page.html.twig';
		}

		return $this->render($template, array( 'page' => $page ));
	}

	public function getTemplateByPath(Request $request, $path)
	{
		$templates = [];

		if ($request->getLocale() == 'en') $templates = [
			'subscribe-completed' => '@MaciPage/Page/subscribed.html.twig',
			'subscribtion-completed' => '@MaciPage/Page/subscribed.html.twig',
			'message-sent' => '@MaciPage/Page/message_sent.html.twig',
			'unsubscribed' => '@MaciPage/Page/unsubscribed.html.twig',
			'customer-service' => '@MaciPage/Terms/customer_service.html.twig',
			'shopping-guide' => '@MaciPage/Terms/shopping_guide.html.twig',
			'size-guide' => '@MaciPage/Terms/size_guide.html.twig',
			'shipping' => '@MaciPage/Terms/shipping.html.twig',
			'payment' => '@MaciPage/Terms/payment.html.twig',
			'returns-and-refunds' => '@MaciPage/Terms/refunds.html.twig',
			'general-conditions' => '@MaciPage/Terms/gcs.html.twig',
			'cookies' => '@MaciPage/Terms/cookies.html.twig',
			'privacy' => '@MaciPage/Terms/privacy.html.twig'
		];

		if ($request->getLocale() == 'it') $templates = [
			'iscrizione-completa' => '@MaciPage/Page/subscribed.html.twig',
			'messaggio-inviato' => '@MaciPage/Page/message_sent.html.twig',
			'iscrizione-cancellata' => '@MaciPage/Page/unsubscribed.html.twig',
			'servizio-clienti' => '@MaciPage/Terms/customer_service.html.twig',
			'guida-all-acquisto' => '@MaciPage/Terms/shopping_guide.html.twig',
			'guida-alle-taglie' => '@MaciPage/Terms/size_guide.html.twig',
			'spedizione' => '@MaciPage/Terms/shipping.html.twig',
			'pagamenti' => '@MaciPage/Terms/payment.html.twig',
			'resi-e-rimborsi' => '@MaciPage/Terms/refunds.html.twig',
			'condizioni-generali' => '@MaciPage/Terms/gcs.html.twig',
			'cookies' => '@MaciPage/Terms/cookies.html.twig',
			'privacy' => '@MaciPage/Terms/privacy.html.twig'
		];

		if(array_key_exists($path, $templates))
			return $templates[$path];

		return false;
	}

	public function setCookieAction(Request $request, $cookie)
	{
		$session = $this->get('session');

		if ($cookie === 'start-popup')
			$session->set('start-popup', true);

		else if ($cookie === 'cookie-message')
			$session->set('cookie-message', true);

		if ($request->isXmlHttpRequest())
			return new JsonResponse(array('success' => true), 200);

		return $this->redirect($this->generateUrl('homepage'));
	}

	public function redirectTo($page)
	{
		return $this->redirect($this->generateUrl('maci_page', ['path' => is_object($page) ? $page->getPath() : $page]));
	}
}

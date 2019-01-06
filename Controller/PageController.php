<?php

namespace Maci\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class PageController extends Controller
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
            return $this->redirect($this->generateUrl('maci_page', array('path' => $page->getPath())));
        }

        $album = $this->getDoctrine()->getManager()->getRepository('MaciPageBundle:Media\Album')
            ->findOneBy(array(
                'redirect' => $redirect
            ));

        if ($album) {
            return $this->redirect($this->generateUrl('maci_media_album', array('id' => $album->getId())));
        }

        return $this->render('MaciPageBundle:Page:notfound.html.twig');
    }

    public function pageNotFoundAction()
    {
        $page = $this->getDoctrine()->getManager()->getRepository('MaciPageBundle:Page\Page')
            ->findOneBy(array(
                'path' => 'page-not-found',
                'removed' => false
            ));

        if (!$page) {
            return $this->render('MaciPageBundle:Page:notfound.html.twig');
        }

        $template = $page->getTemplate();

        if (!$template || !$this->get('templating')->exists($template)) {
            $template = 'MaciPageBundle:Page:page.html.twig';
        }

        return $this->render($template, array( 'page' => $page ));
    }

    public function renderByPath($request, $path, $template)
    {
        // var_dump( $this->get('maci.orders')->getCountriesArray() ); die();

        $page = $this->getDoctrine()->getManager()->getRepository('MaciPageBundle:Page\Page')
                ->findOneBy(array(
                    'locale' => $request->getLocale(),
                    'path' => $path,
                    'removed' => false
                ));

        if (!$page) {
            return $this->redirect($this->generateUrl('maci_page_not_found'));
        }

        $template = $page->getTemplate();

        if (!$template || !$this->get('templating')->exists($template)) {
            $template = 'MaciPageBundle:Page:page.html.twig';
        }

        return $this->render($template, array( 'page' => $page ));
    }

    public function setCookieAction(Request $request, $cookie)
    {
        $session = $this->get('session');

        if ($cookie === 'start-popup') {
            $session->set('start-popup', true);
        } else if ($cookie === 'cookie-message') {
            $session->set('cookie-message', true);
        }

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(array('success' => true), 200);
        }

        return $this->redirect($this->generateUrl('homepage'));
    }
}

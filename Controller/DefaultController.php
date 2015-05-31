<?php

namespace Maci\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->redirect($this->generateUrl('maci_homepage'));
    }

    public function localIndexAction()
    {
        return $this->renderByPath('homepage', false);
    }

    public function pageAction($path)
    {
        return $this->renderByPath($path, false);
    }

    public function pageNotFoundAction()
    {
        $page = $this->getDoctrine()->getManager()->getRepository('MaciPageBundle:Page')
            ->findOneBy(array(
                'path' => 'page-not-found',
                'removed' => false
            ));

        if (!$page) {
            return $this->render('MaciPageBundle:Default:notfound.html.twig');
        }

        $template = $page->getTemplate();

        if (!$template || !$this->get('templating')->exists($template)) {
            $template = 'MaciPageBundle:Default:page.html.twig';
        }

        return $this->render($template, array( 'page' => $page ));
    }

    public function renderByPath($path, $template)
    {
        $page = $this->getDoctrine()->getManager()->getRepository('MaciPageBundle:Page')
                ->findOneBy(array(
                    'path' => $path,
                    'removed' => false
                ));

        if (!$page) {
            return $this->redirect($this->generateUrl('maci_page_not_found'));
        }

        $template = $page->getTemplate();

        if (!$template || !$this->get('templating')->exists($template)) {
            $template = 'MaciPageBundle:Default:page.html.twig';
        }

        return $this->render($template, array( 'page' => $page ));
    }

    public function startPopupAction()
    {
        $session = $this->get('session');
        $session->set('start-popup', true);
        return $this->redirect($this->generateUrl('homepage'));
    }
}

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

    public function contactsAction()
    {
        return $this->renderByPath('contacts', false);
    }

    public function privacyAction()
    {
        return $this->render('MaciPageBundle:Default:privacy.html.twig');
    }

    public function renderByPath($path, $template)
    {
        $page = $this->getDoctrine()->getManager()->getRepository('MaciPageBundle:Page')
                ->findOneByPath($path);

        if (!$page) {
            return $this->render('MaciPageBundle:Default:notfound.html.twig');
        }

        if (!$template = $page->getTemplate()) {
            $template = 'MaciPageBundle:Default:page.html.twig';
        }

        return $this->render($template, array( 'page' => $page ));
    }
}

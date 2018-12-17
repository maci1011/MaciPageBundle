<?php

namespace Maci\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends Controller
{
    public function indexAction()
    {
        return $this->render('MaciPageBundle:Search:index.html.twig');
    }

    public function pageAction(Request $request, $query)
    {
        return $this->render('MaciPageBundle:Page:search.html.twig', array(
            'query' => $query,
            'list' => $this->search($request, 'MaciPageBundle:Page')
        ));
    }

    public function mediaAction(Request $request, $query)
    {
        return $this->render('MaciMediaBundle:Default:search.html.twig', array(
            'query' => $query,
            'list' => $this->search($request, 'MaciMediaBundle:Media')
        ));
    }

    public function blogAction(Request $request, $query)
    {
        return $this->render('MaciBlogBundle:Default:search.html.twig', array(
            'query' => $query,
            'list' => $this->search($request, 'MaciBlogBundle:Post')
        ));
    }

    public function shopAction(Request $request, $query)
    {
        return $this->render('MaciProductBundle:Default:search.html.twig', array(
            'query' => $query,
            'list' => $this->search($request, 'MaciProductBundle:Product')
        ));
    }

    public function search(Request $request, $repo)
    {
        $repo = $this->getDoctrine()->getManager()->getRepository($repo);
        return $repo->search($request);
    }

    public function formAction(Request $request, $section = null, $label = null, $query = null)
    {
        $form = $this->createForm('search');

        $form->handleRequest($request);

        if ($form->isValid()) {
            $redirect = $form['section']->getData();
            $query = $form['query']->getData();

            if ( $redirect === 'media' ) {
                return $this->redirect($this->generateUrl('maci_search_media', array('query' => $query)));
            } else if ( $redirect === 'blog' ) {
                return $this->redirect($this->generateUrl('maci_search_blog', array('query' => $query)));
            } else if ( $redirect === 'shop' ) {
                return $this->redirect($this->generateUrl('maci_search_shop', array('query' => $query)));
            }

            return $this->redirect($this->generateUrl('maci_search_page', array('query' => $query)));
        }

        return $this->render('MaciPageBundle:Search:_form.html.twig', array(
            'form' => $form->createView(),
            'section' => $section,
            'label' => $label,
            'query' => $query
        ));
    }
}

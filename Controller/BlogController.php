<?php

namespace Maci\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class BlogController extends Controller
{
    public function indexAction(Request $request)
    {
        return $this->render('@MaciPage/Blog/index.html.twig', array(
        	'list' => $this->getDoctrine()->getManager()->getRepository('MaciPageBundle:Blog\Post')
        		->getLatestPosts($request->getLocale())
    	));
    }

    public function tagAction($id)
    {
        $tag = $this->getDoctrine()->getManager()->getRepository('MaciPageBundle:Blog\Tag')
            ->findOneById($id);

        if (!$tag) {
            return $this->redirect($this->generateUrl('maci_blog'));
        }

        return $this->render('@MaciPage/Blog/tag.html.twig', array(
            'tag' => $tag
        ));
    }

    public function showAction($_locale, $path)
    {
        $post = $this->getDoctrine()->getManager()->getRepository('MaciPageBundle:Blog\Post')
            ->findOneBy(array(
                'path' => $path,
                'locale' => $_locale,
                'removed' => false
            ));

        if (!$post) {
            return $this->redirect($this->generateUrl('maci_blog'));
        }

        return $this->render('@MaciPage/Blog/show.html.twig', array(
            'post' => $post
        ));
    }

    public function postPermalinkAction($id)
    {
        $params = array('id' => $id);
        if (!$this->getUser()->isGranted('ROLE_ADMIN')) $params['removed'] = false;

        $post = $this->getDoctrine()->getManager()->getRepository('MaciPageBundle:Blog\Post')->findOneBy($params);

        if (!$post) {
            return $this->redirect($this->generateUrl('maci_page', array('path' => 'post-not-found')));
        }

        return $this->redirect($this->generateUrl('maci_blog_show', array('path' => $post->getPath(), '_locale' => $post->getLocale())));
    }
}

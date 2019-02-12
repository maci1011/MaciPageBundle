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

    public function showAction($id)
    {
        $post = $this->getDoctrine()->getManager()->getRepository('MaciPageBundle:Blog\Post')
            ->findOneBy(array(
                'id' => $id,
                'removed' => false
            ));

        if (!$post) {
            return $this->redirect($this->generateUrl('maci_blog'));
        }

        return $this->render('@MaciPage/Blog/show.html.twig', array(
            'post' => $post
        ));
    }
}

<?php

namespace Maci\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class BlogController extends AbstractController
{
	public function indexAction(Request $request)
	{
		return $this->render('@MaciPage/Blog/index.html.twig', array(
			'list' => $this->getDoctrine()->getManager()->getRepository('MaciPageBundle:Blog\Post')
				->getLatestPosts($request->getLocale())
		));
	}

	public function lastPostsAction(Request $request)
	{
		return $this->render('@MaciPage/Blog/last_posts.html.twig', array(
			'list' => $this->getDoctrine()->getManager()->getRepository('MaciPageBundle:Blog\Post')
				->getLatestPosts($request->getLocale(), 6)
		));
	}

	public function tagAction($path)
	{
		$tag = $this->getDoctrine()->getManager()->getRepository('MaciPageBundle:Blog\Tag')
			->findOneByPath($path);

		if (!$tag)
			return $this->redirect($this->generateUrl('maci_blog'));

		return $this->render('@MaciPage/Blog/tag.html.twig', array(
			'tag' => $tag
		));
	}

	public function authorAction($path)
	{
		$om = $this->getDoctrine()->getManager();
		$author = $om->getRepository('MaciPageBundle:Blog\Author')
			->findOneByPath($path);

		if (!$author)
			return $this->redirect($this->generateUrl('maci_blog'));

		$list = $om->getRepository('MaciPageBundle:Blog\Post')
			->getByAuthor($author);

		return $this->render('@MaciPage/Blog/author.html.twig', [
			'list' => $list,
			'author' => $author
		]);
	}

	public function showAction(Request $request, $path)
	{
		$post = $this->getDoctrine()->getManager()->getRepository('MaciPageBundle:Blog\Post')
			->findOneBy(array(
				'path' => $path,
				'locale' => $request->getLocale(),
				'removed' => false
			));

		if (!$post) {
			return $this->redirect($this->generateUrl('maci_blog'));
		}

		return $this->render('@MaciPage/Blog/show.html.twig', array(
			'post' => $post
		));
	}

	public function postShortlinkAction($link)
	{
		$params = array('link' => $link);
		if (!$this->isGranted('ROLE_ADMIN')) $params['removed'] = false;

		$post = $this->getDoctrine()->getManager()->getRepository('MaciPageBundle:Blog\Post')->findOneBy($params);

		if (!$post) {
			return $this->redirect($this->generateUrl('maci_page', array('path' => 'post-not-found')));
		}

		return $this->redirect($this->generateUrl('maci_blog_show', array('path' => $post->getPath(), '_locale' => $post->getLocale())));
	}
}

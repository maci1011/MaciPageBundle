<?php

namespace Maci\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

use Maci\PageBundle\Entity\Blog\Comment;
use Maci\PageBundle\Form\Blog\CommentType;

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
		$om = $this->getDoctrine()->getManager();
		$tag = $om->getRepository('MaciPageBundle:Blog\Tag')
			->findOneByPath($path);

		if (!$tag)
			return $this->redirect($this->generateUrl('maci_blog'));

		$list = $om->getRepository('MaciPageBundle:Blog\Post')
			->getByTag($tag);

		return $this->render('@MaciPage/Blog/tag.html.twig', [
			'list' => $list,
			'tag' => $tag
		]);
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

		if (!$post)
			return $this->redirect($this->generateUrl('maci_blog'));

		$comment = new Comment();

		$commentForm = $this->createForm(CommentType::class, $comment, [
			'action' => $this->generateUrl('maci_blog_add_comment', ['id' => $post->getId()]),
			'method' => 'POST'
		]);

		return $this->render('@MaciPage/Blog/show.html.twig', array(
			'post' => $post,
			'commentForm' => $commentForm->createView()
		));
	}

	public function postShortlinkAction($link)
	{
		$params = ['link' => $link];
		if (!$this->isGranted('ROLE_ADMIN')) $params['removed'] = false;

		$post = $this->getDoctrine()->getManager()->getRepository('MaciPageBundle:Blog\Post')->findOneBy($params);

		if (!$post)
			return $this->redirect($this->generateUrl('maci_page', array('path' => 'post-not-found')));

		return $this->redirect($this->generateUrl('maci_blog_show', array('path' => $post->getPath(), '_locale' => $post->getLocale())));
	}

	public function addCommentAction(Request $request, $id)
	{
		$em = $this->getDoctrine()->getManager();
		$post = $em->getRepository('MaciPageBundle:Blog\Post')->findOneBy([
			'id' => $id, 'removed' => false
		]);

		if (!$post)
			return $this->redirect($this->generateUrl('maci_blog'));

		$comment = new Comment();

		$form = $this->createForm(CommentType::class, $comment, [
			'action' => $this->generateUrl('maci_blog_add_comment', ['id' => $post->getId()]),
			'method' => 'POST'
		]);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			if ($this->isGranted('ROLE_USER'))
				$comment->setUser($this->getUser());

			$hash = $form['_parent']->getData();
			if (strlen($hash))
			{
				$rto = $em->getRepository('MaciPageBundle:Blog\Comment')->findOneBy([
					'hash' => $hash //, 'removed' => false
				]);
				if ($rto)
					$comment->setParent($rto);
			}

			if (!$comment->getParent())
				$comment->setPost($post);

			$em->persist($comment);
			$em->flush();

			return $this->redirect($this->generateUrl('maci_blog_show', [
				'_locale' => $post->getLocale(), 'path' => $post->getPath()
			]));
		}

		return $this->render('MaciPageBundle:Contact:form.html.twig', [
			'form' => $form->createView(),
			'send' => false
		]);
	}

	public function sendNotify(Request $request, $comment)
	{
		$mt = $this->get('maci.translator');

		$mail = new Mail();
		$mail
			->setName('ContactForm')
			->setType('message')
			->setSubject(str_replace('%name%', $comment->getFullName(), $mt->getLabel('contacts.mail-title', 'Messagge from: %name%')))
			->setReplyTo($comment->getRecipient())
			->setSender([$this->get('service_container')->getParameter('server_email') => $this->get('service_container')->getParameter('server_email_int')])
			->addRecipients([$this->get('service_container')->getParameter('contact_email') => $this->get('service_container')->getParameter('contact_email_int')])
			->setLocale($request->getLocale())
			->setText($this->renderView('@MaciPage/Contact/email.txt.twig', ['comment' => $comment]))
			->setContent($this->renderView('@MaciPage/Contact/email.html.twig', ['comment' => $comment]))
		;

		$this->get('maci.mailer')->send($mail);
	}
}

<?php

namespace Maci\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

use Maci\AdminBundle\MaciPager as Pager;
use Maci\PageBundle\Entity\Blog\Comment;
use Maci\PageBundle\Entity\Mailer\Mail;
use Maci\PageBundle\Entity\Mailer\Subscriber;
use Maci\PageBundle\Form\Blog\CommentType;

class BlogController extends AbstractController
{
	public function indexAction(Request $request)
	{
		return $this->render('@MaciPage/Blog/index.html.twig', [
			'pager' => $this->getPager($request, $this->getDoctrine()->getManager()
				->getRepository('MaciPageBundle:Blog\Post')->getLatestPosts($request->getLocale())
			)
		]);
	}

	public function lastPostsAction(Request $request)
	{
		return $this->render('@MaciPage/Blog/last_posts.html.twig', [
			'list' => $this->getDoctrine()->getManager()->getRepository('MaciPageBundle:Blog\Post')
				->getLatestPosts($request->getLocale(), 6)
		]);
	}

	public function tagAction(Request $request, $path)
	{
		$om = $this->getDoctrine()->getManager();
		$tag = $om->getRepository('MaciPageBundle:Blog\Tag')
			->findOneByPath($path);

		if (!$tag)
			return $this->redirect($this->generateUrl('maci_blog'));

		$list = $om->getRepository('MaciPageBundle:Blog\Post')
			->getByTag($tag);

		return $this->render('@MaciPage/Blog/tag.html.twig', [
			'pager' => $this->getPager($request, $list),
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

	public function getPager($request, $list)
	{
		return new Pager($list, 17, 5, intval($request->get('p', 1)));
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
			'method' => 'POST',
			'env' => $this->get('kernel')->getEnvironment()
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
			return $this->redirect($this->generateUrl('maci_page', ['path' => 'post-not-found']));

		return $this->redirect($this->generateUrl('maci_blog_show', ['path' => $post->getPath(), '_locale' => $post->getLocale()]));
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
			'method' => 'POST',
			'env' => $this->get('kernel')->getEnvironment()
		]);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			if ($this->isGranted('ROLE_USER'))
				$comment->setUser($this->getUser());

			if ($form->has('newsletter') && $form['newsletter']->getData())
			{
				$sub = $em->getRepository('MaciPageBundle:Mailer\Subscriber')->findOneBy([
					'mail' => $comment->getEmail()
				]);

				if ($sub)
				{
					$this->get('session')->getFlashBag()->add('danger', $this->get('maci.translator')->getText('error.subscribe-error-comment', 'We are sorry, but this email can not be added to our newsletter. Contact us for more informations.'));
				}
				else
				{
					$sub = new Subscriber();
					$sub->setName($comment->getName());
					$sub->setMail($comment->getEmail());
					$sub->setLocale($request->getLocale());
					if ($this->isGranted('ROLE_USER'))
						$sub->setUser($this->getUser());
					$em->persist($sub);
					$em->flush();
					$this->get('maci.mailer')->notifyNewSubscription($sub);
					$this->get('session')->getFlashBag()->add('success', $this->get('maci.translator')->getText('message.subscribe-success-comment', 'Thank you for signing up to our newsletter.'));
				}
			}

			$hash = $form['_parent']->getData();
			if (strlen($hash))
			{
				$rto = $em->getRepository('MaciPageBundle:Blog\Comment')->findOneBy([
					'hash' => $hash //, 'removed' => false
				]);
				if ($rto)
				{
					$comment->setParent($rto);
					if ($rto->getUser())
						$this->get('maci.notify')->notifyTo($rto->getUser(), $this->get('maci.translator')->getText('notify.blog.new-reply', 'There is a new reply to your comment.'));
				}
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

	public function approveCommentAction(Request $request, $hash)
	{
		$em = $this->getDoctrine()->getManager();
		$comment = $em->getRepository('MaciPageBundle:Blog\Comment')->findOneBy([
			'hash' => $hash, 'approved' => false //, 'removed' => false
		]);

		if (!$comment)
			return $this->redirect($this->generateUrl('maci_blog'));

		$post = $comment->getPostRec();
		if (!$post)
			return $this->redirect($this->generateUrl('maci_blog'));

		$comment->setApproved(true);
		$em->flush();

		$this->sendNotify($comment);

		return $this->redirect($this->generateUrl('maci_blog_show', [
			'path' => $post->getPath(), '_locale' => $post->getLocale()
		]) . '#cmm-' . $hash);
	}

	public function sendNotify($reply)
	{
		if (!$reply->getParent() || !$reply->getParent()->getNotify())
			return;

		$mail = new Mail();
		$mail
			->setName('CommentNotify')
			->setType('message')
			->setSubject(str_replace('%name%', $reply->getUsername(), $this->get('maci.translator')->getLabel('comments.mail-title', 'Reply from: %name%')))
			->setSender([$this->get('service_container')->getParameter('server_email') => $this->get('service_container')->getParameter('server_email_int')])
			->addRecipients($reply->getParent()->getRecipient())
			->setLocale($reply->getPostRec()->getLocale())
			// ->setText($this->renderView('@MaciPage/Email/comment_reply.txt.twig', ['_locale' => $locale, 'comment' => $reply->getParent(), 'reply' => $reply, 'post' => $reply->getPostRec()]))
			->setContent($this->renderView('@MaciPage/Email/comment_reply.html.twig', ['_locale' => $locale, 'comment' => $reply->getParent(), 'reply' => $reply, 'post' => $reply->getPostRec()]))
		;
		$this->get('maci.mailer')->send($mail);
	}
}

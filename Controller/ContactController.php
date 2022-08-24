<?php

namespace Maci\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Maci\PageBundle\Entity\Contact\Contact;
use Maci\PageBundle\Entity\Mailer\Mail;
use Maci\PageBundle\Entity\Media\Media;
use Maci\PageBundle\Form\Contact\ContactType;

class ContactController extends AbstractController
{
	public function formAction(Request $request)
	{
		$contact = new Contact();

		$form = $this->createForm(ContactType::class, $contact, [
			'action' => $this->generateUrl('MaciPageBundle_ContactForm'),
			'method' => 'POST',
			'env' => $this->get('kernel')->getEnvironment()
		]);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			$data = $form->getData();
			$em = $this->getDoctrine()->getManager();

			if ($file = $form['media']->getData()) {

				$media = new Media();

				$media->setFile($file);
				$media->setType('attachment');
				$contact->addMedia($media);
				$em->persist($media);
			}

			$em->persist($contact);
			$em->flush();

			$mt = $this->get('maci.translator');

			$mail = new Mail();
			$mail
				->setName('ContactForm')
				->setType('message')
				->setSubject(str_replace('%name%', $contact->getFullName(), $mt->getLabel('contacts.mail-title', 'Messagge from: %name%')))
				->setReplyTo($contact->getRecipient())
				->setSender([$this->get('service_container')->getParameter('server_email') => $this->get('service_container')->getParameter('server_email_int')])
				->addRecipients([$this->get('service_container')->getParameter('contact_email') => $this->get('service_container')->getParameter('contact_email_int')])
				->setLocale($request->getLocale())
				->setText($this->renderView('@MaciPage/Contact/email.txt.twig', ['contact' => $contact]))
				->setContent($this->renderView('@MaciPage/Contact/email.html.twig', ['contact' => $contact]))
			;

			$this->get('maci.mailer')->send($mail);

			return $this->redirect($this->generateUrl(
				'maci_page', ['path' => $mt->getRoute('contacts.message-sent', 'message-sent')]
			));
		}

		return $this->render('MaciPageBundle:Contact:form.html.twig', [
			'form' => $form->createView(),
			'send' => false
		]);
	}
}

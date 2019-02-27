<?php

namespace Maci\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Maci\PageBundle\Entity\Media\Media;

use Maci\PageBundle\Entity\Contact\Contact;
use Maci\PageBundle\Form\Contact\ContactType;

class ContactController extends Controller
{
    public function formAction(Request $request)
    {
        $contact = new Contact();

        $form = $this->createForm(ContactType::class, $contact, array(
            'action' => $this->generateUrl('MaciPageBundle_ContactForm'),
            'method' => 'POST'
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();
            $em = $this->getDoctrine()->getEntityManager();

            if ($file = $form['media']->getData()) {

                $media = new Media();

                $media->setFile($file);
                $media->setType('attachment');
                $contact->addMedia($media);
                $em->persist($media);
            }

            $em->persist($contact);
            $em->flush();

            $name = trim($contact->getName().' '.$contact->getSurname());
            $message = (new \Swift_Message());
            $message->setSubject('Contatti da '.$name);
            $message->setReplyTo(array($contact->getEmail()));
            $message->setFrom($this->get('service_container')->getParameter('server_email'), $this->get('service_container')->getParameter('server_email_int'));
            $message->setTo([$this->get('service_container')->getParameter('contact_email') => $name]);
            $message->setBody($this->renderView('MaciPageBundle:Contact:email.txt.twig', array('contact' => $contact)));

            $this->get('mailer')->send($message);

            return $this->redirect($this->generateUrl(
                'maci_page', array('path' => $this->get('maci.translator')->getRoute('contacts.message-sent', 'message-sent'))
            ));
        }

        return $this->render('MaciPageBundle:Contact:form.html.twig', array(
            'form' => $form->createView(),
            'added' => false
        ));
    }
}

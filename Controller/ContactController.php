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

            $message = \Swift_Message::newInstance()
                ->setSubject('Contatti da '.$contact->getName().' '.$contact->getSurname())
                ->setReplyTo(array($contact->getEmail()))

                /**
                 * Set From
                 */
                ->setFrom($this->get('service_container')->getParameter('server_email'), $this->get('service_container')->getParameter('server_email_int'))

                /**
                 * Set To
                 */
                ->setTo($this->get('service_container')->getParameter('contact_email'))

                ->setBody($this->renderView('MaciPageBundle:Contact:email.txt.twig', array('contact' => $contact)))
            ;

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

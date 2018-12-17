<?php

namespace Maci\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MediaController extends Controller
{
    public function indexAction()
    {
    	$list = $this->getDoctrine()->getManager()
            ->getRepository('MaciPageBundle:Media\Media')
            ->getList();

        return $this->render('MaciPageBundle:Media:index.html.twig', array('list' => $list));
    }

    public function tagAction($id)
    {
        $tag = $this->getDoctrine()->getManager()
            ->getRepository('MaciPageBundle:Media\Tag')
            ->findOneById($id);

        if (!$tag) {
            return $this->redirect($this->generateUrl('maci_media'));
        }

        return $this->render('MaciPageBundle:Media:tag.html.twig', array('tag' => $tag));
    }

    public function tagsAction($type)
    {
        $list = $this->getDoctrine()->getManager()
            ->getRepository('MaciPageBundle:Media\Tag')
            ->findBy(array(
                'type' => $type
            ));

        return $this->render('MaciPageBundle:Media:tags.html.twig', array('list' => $list));
    }

    public function brandsAction()
    {
        $list = $this->getDoctrine()->getManager()
            ->getRepository('MaciPageBundle:Media\Tag')
            ->findBy(array(
                'type' => 'brand'
            ));

        return $this->render('MaciPageBundle:Media:brands.html.twig', array('list' => $list));
    }

    public function galleryAction($type)
    {
        $list = $this->getDoctrine()->getManager()
            ->getRepository('MaciPageBundle:Media\Album')
            ->getGallery($type);

        return $this->render('MaciPageBundle:Media:gallery.html.twig', array('list' => $list));
    }

    public function albumAction($id)
    {
        $album = $this->getDoctrine()->getManager()
            ->getRepository('MaciPageBundle:Media\Album')
            ->findOneById($id);

        if (!$album) {
            return $this->redirect($this->generateUrl('maci_media'));
        }

        return $this->render('MaciPageBundle:Media:album.html.twig', array('album' => $album));
    }

    public function sliderAction($id)
    {
        $list = $this->getDoctrine()->getManager()
            ->getRepository('MaciPageBundle:Media\Album')
            ->getOneById($id);

        return $this->render('MaciPageBundle:Media:_slider.html.twig', array('album' => $album));
    }

    public function libraryAction()
    {
        $list = $this->getDoctrine()->getManager()
            ->getRepository('MaciPageBundle:Media\Permission')
            ->findBy(array(
                'user' => $this->getUser(),
                'status' => 'end'
            ));

        return $this->render('MaciPageBundle:Media:library.html.twig', array('list' => $list));
    }

    public function getAction($path)
    {
        $media = $this->getDoctrine()->getManager()
            ->getRepository('MaciPageBundle:Media\Media')
            ->findOneByPath($path);

        if ($media) {
            if (!$media->getPublic()) {
                $permission = $this->getDoctrine()->getManager()
                    ->getRepository('MaciPageBundle:Media\Permission')
                    ->findOneBy(array(
                        'user' => $this->getUser(),
                        'media' => $media,
                        'status' => 'end'
                    ));
                if ($permission) {
                    return new BinaryFileResponse($media->getAbsolutePath());
                }
                return new Response();
            } else {
                return new BinaryFileResponse($media->getAbsolutePath());
            }
        }
        return new Response();
    }
}

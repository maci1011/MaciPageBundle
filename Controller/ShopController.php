<?php

namespace Maci\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ShopController extends AbstractController
{
    public function indexAction()
    {
        return $this->render('MaciPageBundle:Shop:index.html.twig', array(
            'list' => $this->getDoctrine()->getManager()
                ->getRepository('MaciPageBundle:Shop\Product')->getList()
        ));
    }

    public function categoryAction($id)
    {

        $category = $this->getDoctrine()->getManager()
            ->getRepository('MaciPageBundle:Shop\Category')
            ->findOneBy(array(
                'id' => $id
            ));

        if (!$category) {
            return $this->redirect($this->generateUrl('maci_product'));
        }

        return $this->render('MaciPageBundle:Shop:category.html.twig', array(
            'category' => $category
        ));
    }

    public function showAction($id)
    {

        $product = $this->getDoctrine()->getManager()
            ->getRepository('MaciPageBundle:Shop\Product')->getById($id);

        if (!$product) {
            return $this->redirect($this->generateUrl('maci_product'));
        }

        return $this->render('MaciPageBundle:Shop:show.html.twig', array(
            'item' => $product
        ));
    }
}

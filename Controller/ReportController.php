<?php

namespace Maci\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf as Snappy;

class ReportController extends AbstractController
{
	public function indexAction()
	{
		if (!$this->isGranted('ROLE_ADMIN'))
			return $this->redirect($this->generateUrl('maci_homepage'));

		return $this->render('@MaciPage/Record/reports.html.twig');
	}

	public function storeAction()
	{
		if (!$this->isGranted('ROLE_ADMIN'))
			return $this->redirect($this->generateUrl('maci_homepage'));

		$om = $this->getDoctrine()->getManager();

		$products = $om->getRepository('MaciPageBundle:Shop\Product')->findAll();

		$titles = [
			'Category',
			'Buyed',
			'Quantity',
			'Selled',
			'N. of Products',
			'Avg. Price',
			'Tot. Selled'
		];
		$list = [];

		foreach ($products as $product)
		{
			$category = explode(' ', $product->getName())[0];

			$index = -1;
			foreach ($list as $key => $el)
			{
				if ($el[0] == $category)
				{
					$index = $key;
					break;
				}
			}

			if ($index == -1)
			{
				$index = count($list);
				array_push($list, [
					0 => $category,
					1 => 0, // buyed
					2 => 0, // quantity
					3 => 0, // selled
					4 => 0, // products number
					5 => 0, // avg. price
					6 => 0
				]);
			}

			$row = $list[$index];

			$row[1] += $product->getBuyed();
			$row[2] += $product->getQuantity();
			$row[3] += $product->getSelled();
			$row[4] += 1;
			$row[5] += $product->getPrice();
			$row[6] += $product->getSelled() * $product->getPrice();

			$list[$index] = $row;
		}

		$tot = [
			0 => 'Amount:',
			1 => 0, // buyed
			2 => 0, // quantity
			3 => 0, // selled
			4 => 0, // products number
			5 => 0, // avg. price
			6 => 0
		];

		foreach ($list as $index => $el)
		{
			$list[$index][5] = number_format($el[5] / $el[4], 2);
			$list[$index][6] = number_format($el[6], 2);

			$tot[1] += $el[1];
			$tot[2] += $el[2];
			$tot[3] += $el[3];
			$tot[4] += $el[4];
			$tot[5] += $list[$index][5];
			$tot[6] += $el[6];
		}

		$tot[5] = number_format($tot[5] / count($list), 2);
		$tot[6] = number_format($tot[6], 2);

		return $this->getPDF([
			'headers' => $titles,
			'list' => $list,
			'amounts' => $tot,
			'footers' => [
				$this->container->getParameter('company_title'),
				'REPORT: Shop > Products',
				date("Y/m/d H:i:s")
			],
			'filename' => 'report-shop-products.pdf'
		]);
	}

	public function getPDF($params, $options = [])
	{
		$snappy = new Snappy($this->container->getParameter('knp_snappy.pdf.binary'));

		$params = array_merge([
			'headers' => [],
			'list' => [],
			'amounts' => [],
			'footers' => [],
			'template' => '@MaciPage/Report/report.html.twig',
			'filename' => 'report.pdf'
		], $params);

		$options = array_merge([
			'orientation' => 'portrait',
			'enable-javascript' => true,
			'javascript-delay' => 1000,
			'no-stop-slow-scripts' => true,
			'no-background' => false,
			'lowquality' => false,
			'margin-top'  => 0,
			'margin-right'  => 0,
			'margin-bottom'  => 0,
			'margin-left'  => 0,
			'encoding' => 'utf-8',
			'images' => true,
			'cookie' => array(),
			'dpi' => 300,
			'page-size' => 'A4',
			'enable-external-links' => true,
			'enable-internal-links' => true
		], $options);

		return new PdfResponse(
			$snappy->getOutputFromHtml($this->renderView($params['template'], $params), $options),
			$params['filename']
		);
	}
}

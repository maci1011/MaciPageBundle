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

	public function recordsAction(Request $request)
	{
		if (!$this->isGranted('ROLE_ADMIN'))
			return $this->redirect($this->generateUrl('maci_homepage'));

		$om = $this->getDoctrine()->getManager();
		$list = [];

		$after = $request->get('after');
		$before = $request->get('before');
		if ($after) $after = date("Y/m/d", strtotime($after));
		if ($before) $before = date("Y/m/d", strtotime($before));

		$records = $om->getRepository('MaciPageBundle:Shop\Record')->fromTo($after, $before);

		$titles = [
			'Category',
			'Purchase',
			'Sale',
			'Customer Return',
			'Supplier Return',
			'Buyed',
			'Selled'
		];

		$list = [];

		foreach ($records as $record)
		{
			$category = explode(' ', $record->getCategory())[0];

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
					1 => 0, // Purchase
					2 => 0, // Sale
					3 => 0, // Customer Return
					4 => 0, // Supplier Return
					5 => [],// Pur. Price
					6 => [] // Sale Price
				]);
			}

			$row = $list[$index];

			$row[1] += $record->isPurchase() ? $record->getQuantity() : 0;
			$row[2] += $record->isSale() ? $record->getQuantity() : 0;
			$row[3] += $record->isReturn() ? $record->getQuantity() : 0;
			$row[4] += $record->isBack() ? $record->getQuantity() : 0;
			if ($record->isPurchase()) array_push($row[5], [$record->getQuantity(), $record->getPrice()]);
			if ($record->isSale()) array_push($row[6], [$record->getQuantity(), $record->getPrice()]);
			if ($record->isReturn()) $el[5] = $this->sub($el[5], $record->getQuantity());
			if ($record->isBack()) $el[6] = $this->sub($el[6], $record->getQuantity());

			$list[$index] = $row;
		}

		// Amounts

		$tot = [
			0 => 'Amount:',
			1 => 0, // Purchase
			2 => 0, // Sale
			3 => 0, // Customer Return
			4 => 0, // Supplier Return
			5 => 0, // Buyed
			6 => 0  // Selled
		];

		$cats = [];

		foreach ($list as $index => $el)
		{
			$list[$index][0] = ucfirst(strtolower($el[0]));
			array_push($cats, $list[$index][0]);

			$list[$index][5] = $this->tot($el[5]);
			$list[$index][6] = $this->tot($el[6]);

			$tot[1] += $el[1];
			$tot[2] += $el[2];
			$tot[3] += $el[3];
			$tot[4] += $el[4];
			$tot[5] += $list[$index][5];
			$tot[6] += $list[$index][6];
		}

		// Sort

		sort($cats);
		$slist = [];

		foreach ($cats as $cat)
		{
			foreach ($list as $index => $el)
			{
				if ($el[0] == $cat)
				{
					array_push($slist, $el);
					unset($list[$index]);
					break;
				}
			}
		}

		// return PDF

		return $this->getPDF([
			'headers' => $titles,
			'list' => $slist,
			'amounts' => [$tot],
			'footers' => [
				$this->container->getParameter('company_title'),
				'REPORT: Records',
				date("Y/m/d H:i:s")
			],
			'filename' => 'report-records.pdf'
		]);
	}

	public function sub($list, $quantity)
	{
		for ($i = count($list) - 1; 0 <= $i; $i--)
		{ 
			if ($quantity <= $list[$i][0])
			{
				$list[$i][0] -= $quantity;
				break;
			}
			else
			{
				$quantity -= $list[$i][0];
				$list[$i][0] = 0;
			}
		}
		return $list;
	}

	public function tot($list)
	{
		$t = 0;
		foreach ($list as $value)
			$t += $value[0] * $value[1];
		return $t;
	}

	public function inventoryAction()
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
			'Ends'
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
					5 => 0  // ends
				]);
			}

			$row = $list[$index];

			$row[1] += $product->getBuyed();
			$row[2] += $product->getQuantity();
			$row[3] += $product->getSelled();
			$row[4] += 1;
			$row[5] += $product->getQuantity() == 0 ? 1 : 0;

			$list[$index] = $row;
		}

		// Amounts

		$tot = [
			0 => 'Amount:',
			1 => 0, // buyed
			2 => 0, // quantity
			3 => 0, // selled
			4 => 0, // products number
			5 => 0
		];

		$cats = [];

		foreach ($list as $index => $el)
		{
			array_push($cats, $el[0]);

			$tot[1] += $el[1];
			$tot[2] += $el[2];
			$tot[3] += $el[3];
			$tot[4] += $el[4];
			$tot[5] += $el[5];
		}

		// Sort

		sort($cats);
		$slist = [];

		foreach ($cats as $cat)
		{
			foreach ($list as $index => $el)
			{
				if ($el[0] == $cat)
				{
					array_push($slist, $el);
					unset($list[$index]);
					break;
				}
			}
		}

		// return PDF

		return $this->getPDF([
			'headers' => $titles,
			'list' => $slist,
			'amounts' => [$tot],
			'footers' => [
				$this->container->getParameter('company_title'),
				'REPORT: Shop > Inventory',
				date("Y/m/d H:i:s")
			],
			'filename' => 'report-shop-inventory.pdf'
		]);
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

		// Amounts

		$tot = [
			0 => 'Amount:',
			1 => 0, // buyed
			2 => 0, // quantity
			3 => 0, // selled
			4 => 0, // products number
			5 => 0, // avg. price
			6 => 0
		];

		$cats = [];

		foreach ($list as $index => $el)
		{
			array_push($cats, $el[0]);
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

		// Sort

		sort($cats);
		$slist = [];

		foreach ($cats as $cat)
		{
			foreach ($list as $index => $el)
			{
				if ($el[0] == $cat)
				{
					array_push($slist, $el);
					unset($list[$index]);
					break;
				}
			}
		}

		// return PDF

		return $this->getPDF([
			'headers' => $titles,
			'list' => $slist,
			'amounts' => [$tot],
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

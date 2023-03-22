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

		$after = $request->get('after', '');
		$before = $request->get('before', '');
		$after = strlen($after) ? date("Y/m/d", strtotime($after)) : false;
		$before = strlen($before) ? date("Y/m/d", strtotime($before)) : false;
		$collection = $request->get('collection', '');

		$records = $om->getRepository('MaciPageBundle:Shop\Record')
			->fromTo($after, $before, is_string($collection) ? ($collection == '' ? null : $collection) : false);

		$report = $request->get('report');

		if ($report == 'totals')
			return $this->totalsReport($request, $records);

		$titles = [
			'Category',
			'Purchase',
			'Sale',
			'Cust.Ret.',
			'Supp.Ret.',
			'Buyed',
			'Selled'
		];

		$list = [];

		foreach ($records as $record)
		{
			$category = ucfirst(strtolower(explode(' ', $record->getCategory())[0]));

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
			if ($record->isBack()) $el[5] = $this->sub($el[5], $record->getQuantity());
			if ($record->isReturn()) $el[6] = $this->sub($el[6], $record->getQuantity());

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

			$t5 = $this->tot($el[5]);
			$t6 = $this->tot($el[6]);
			$list[$index][5] = number_format($t5, 2);
			$list[$index][6] = number_format($t6, 2);

			$tot[1] += $el[1];
			$tot[2] += $el[2];
			$tot[3] += $el[3];
			$tot[4] += $el[4];
			$tot[5] += $t5;
			$tot[6] += $t6;
		}

		$tot[5] = number_format($tot[5], 2);
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
				'REPORT: Records',
				date("Y/m/d H:i:s")
			],
			'filename' => 'report-records.pdf'
		]);
	}

	public function totalsReport(Request $request, $records)
	{
		$list = [];
		$categories = [];

		foreach ($records as $record)
		{
			$id = $record->getIdentifier();
			$item = false;

			if (array_key_exists($id, $list)) $item = $list[$id];
			else
			{
				$category = ucfirst(strtolower(explode(' ', $record->getCategory())[0]));
				$item = [
					'category' => $category,
					'buyprc' => false,
					'sllprc' => false,
					'lstbuy' => 0,
					'buyed' => 0,
					'selled' => 0,
					'buytot' => 0,
					'slltot' => 0,
					'buyamt' => 0,
					'buyval' => 0,
					'sllamt' => 0,
					'sllval' => 0
				];

				if (!array_search($category, $categories))
					array_push($categories, $category);
			}

			if ($record->isPurchase() && !$item['buyprc'])
			{
				$item['buyprc'] = $record->getPrice();
			}

			if ($record->isSale() && !$item['sllprc'])
			{
				$item['sllprc'] = $record->getPrice();
			}

			if ($record->isPurchase())
			{
				$item['lstbuy'] = $record->getPrice();
				$item['buyed'] += $record->getQuantity();
				$item['buytot'] += $record->getPrice() * $record->getQuantity();
			}

			if ($record->isSale())
			{
				$item['selled'] += $record->getQuantity();
				$item['slltot'] += $record->getPrice() * $record->getQuantity();
			}

			if ($record->isReturn())
			{
				$item['selled'] -= $record->getQuantity();
				$item['slltot'] -= $record->getPrice() * $record->getQuantity();
			}

			if ($record->isBack())
			{
				$item['buyed'] -= $record->getQuantity();
				$item['buytot'] -= $item['lstbuy'] * $record->getQuantity();
			}

			$list[$id] = $item;
		}

		foreach ($list as $id => $el)
		{
			$el['errors'] = [];

			if (!$el['buyprc'])
			{
				array_push($el['errors'], [$id, 'Buy price is null.']);
			}
			else
			{
				$el['buyamt'] += $el['buyprc'] * $el['buyed'];
				$el['sllval'] += $el['buyprc'] * $el['selled'];
			}

			if (!$el['sllprc'])
			{
				array_push($el['errors'], [$id, 'Sell price is null.']);
			}
			else
			{
				$el['buyval'] += $el['sllprc'] * $el['buyed'];
				$el['sllamt'] += $el['sllprc'] * $el['selled'];
			}

			$list[$id] = $el;
		}

		// Sort

		sort($categories);

		// Result List

		$titles = [
			'Category',
			'Buy',
			'Sell',
			'B.Tot.',
			'B.Amt.',
			'B.Val.',
			'S.Tot.',
			'S.Amt.',
			'S.Val.',
			'E'
		];

		$resl = [];
		foreach ($categories as $category)
		{
			$row = [
				0 => $category,
				1 => 0,
				2 => 0,
				3 => 0,
				4 => 0,
				5 => 0,
				6 => 0,
				7 => 0,
				8 => 0,
				9 => 0
			];

			foreach ($list as $el)
			{
				if ($el['category'] != $category)
					continue;

				$row[1] += $el['buyed'];
				$row[2] += $el['selled'];
				$row[3] += $el['buytot'];
				$row[4] += $el['buyamt'];
				$row[5] += $el['buyval'];
				$row[6] += $el['slltot'];
				$row[7] += $el['sllamt'];
				$row[8] += $el['sllval'];
				$row[9] += count($el['errors']);
			}

			array_push($resl, $row);
		}

		// Amounts

		$tot = [
			0 => 'Amount:',
			1 => 0,
			2 => 0,
			3 => 0,
			4 => 0,
			5 => 0,
			6 => 0,
			7 => 0,
			8 => 0,
			9 => 0
		];

		foreach ($resl as $row)
		{
			$tot[1] += $row[1];
			$tot[2] += $row[2];
			$tot[3] += $row[3];
			$tot[4] += $row[4];
			$tot[5] += $row[5];
			$tot[6] += $row[6];
			$tot[7] += $row[7];
			$tot[8] += $row[8];
			$tot[9] += $row[9];
		}

		// return PDF

		return $this->getPDF([
			'headers' => $titles,
			'list' => $resl,
			'amounts' => [$tot],
			'footers' => [
				$this->container->getParameter('company_title'),
				'REPORT: Totals',
				date("Y/m/d H:i:s")
			],
			'filename' => 'report-totals.pdf'
		]);
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

	public function backsAction(Request $request, $id)
	{
		if (!$this->isGranted('ROLE_ADMIN'))
			return $this->redirect($this->generateUrl('maci_homepage'));

		$om = $this->getDoctrine()->getManager();
		$records = $om->getRepository('MaciPageBundle:Shop\Record')->findBy(
			['parent' => $id], ['code' => 'ASC']
		);

		$titles = [
			'Code',
			'Variant',
			'Category',
			'Quantity'
		];

		$list = [];
		$qta = 0;
		foreach ($records as $record)
		{
			$label = $record->getCode() . '-' . $record->getVariantLabel();
			$variant = (1 < $record->getQuantity() ? $record->getQuantity() . ' ' : '') . $record->getVariantName();
			if (strtolower($variant) == 'simple') $variant = '-';
			if (array_key_exists($label, $list))
			{
				$x = $list[$label][3];
				$list[$label][3] = $x + $record->getQuantity();
			}
			else
			{
				$list[$label] = [
					0 => $record->getCode(),
					1 => $record->getVariantLabel(),
					2 => $record->getCategory(),
					3 => $record->getQuantity()
				];
			}
			$qta += $record->getQuantity();
		}

		// Amounts

		$tot = [
			0 => 'Amount:',
			1 => '',
			2 => '',
			3 => $qta
		];

		// return PDF

		return $this->getPDF([
			'headers' => $titles,
			'list' => $list,
			'amounts' => [$tot],
			'footers' => [
				$this->container->getParameter('company_title'),
				'REPORT: Export > Set',
				date("Y/m/d H:i:s")
			],
			'filename' => 'report-export-set.pdf'
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
			'cookie' => [],
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

	public function getProduct($record, &$list)
	{
		$product = false;
		foreach ($list as $item)
		{
			if ($item->checkRecord($record) && !$product)
			{
				$product = $item;
				break;
			}
		}
		if (!$product)
		{
			$product = $this->findProduct($record);
			if ($product)
				array_push($list, $product);
		}
		return $product;
	}

	public function findProduct($record)
	{
		$list = $this->getDoctrine()->getManager()->getRepository('MaciPageBundle:Shop\Product')->findBy([
			'code' => $record->getCode()
		]);
		$product = false;
		foreach ($list as $item)
		{
			if ($item->checkRecord($record))
			{
				$product = $item;
				break;
			}
		}
		return $product;
	}

	public static function sub($list, $quantity)
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

	public static function tot($list)
	{
		$t = 0;
		foreach ($list as $value)
			$t += $value[0] * $value[1];
		return $t;
	}
}

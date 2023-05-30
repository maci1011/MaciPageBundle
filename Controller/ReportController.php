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

		$list = [];
		$after = $request->get('after', '');
		$before = $request->get('before', '');
		$after = strlen($after) ? date("Y/m/d", strtotime($after)) : false;
		$before = strlen($before) ? date("Y/m/d", strtotime($before)) : false;
		$collection = $request->get('collection', '');
		$report = $request->get('report');

		$om = $this->getDoctrine()->getManager();
		$records = $om->getRepository('MaciPageBundle:Shop\Record')
			->fromTo($after, $before, is_string($collection) ? (
				$collection == '' ? null : $collection
			) : false);

		if ($report == 'buyed')
			return $this->buyedReport($records);

		if ($report == 'selled')
			return $this->selledReport($records);

		if ($report == 'buysell')
			return $this->buysellReport($records);

		if ($report == 'records')
			return $this->checkRecordsReport($records);

		return $this->defaultReport($records);
	}

	public function buyedReport($records)
	{
		$list = $this->recordsAmounts($records);
		$categories = $this->categories;

		// Result List

		$titles = [
			'Category',
			'Buy',
			'Back',
			'Qta',
			'B.Tot.',
			'B.Val.',
			'B.Amt.'
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
				6 => 0
			];

			foreach ($list as $el)
			{
				if ($el['category'] != $category)
					continue;

				$row[1] += $el['buyed'];
				$row[2] += $el['backed'];
				$row[3] += $el['blb'];
				$row[4] += $el['buytot'];
				$row[5] += $el['buyval'];
				$row[6] += $el['buyamt'];
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
			6 => 0
		];

		foreach ($resl as $row)
		{
			$tot[1] += $row[1];
			$tot[2] += $row[2];
			$tot[3] += $row[3];
			$tot[4] += $row[4];
			$tot[5] += $row[5];
			$tot[6] += $row[6];
		}

		$tot[3] = number_format($tot[3], 2);
		$tot[4] = number_format($tot[4], 2);
		$tot[5] = number_format($tot[5], 2);
		$tot[6] = number_format($tot[6], 2);

		// return PDF

		return $this->getPDF([
			'headers' => $titles,
			'list' => $resl,
			'amounts' => [$tot],
			'footers' => [
				$this->container->getParameter('company_title'),
				'REPORT: Totals / Buyed',
				date("Y/m/d H:i:s")
			],
			'filename' => 'report-totals-buyed.pdf'
		]);
	}

	public function selledReport($records)
	{
		$list = $this->recordsAmounts($records);
		$categories = $this->categories;

		// Result List

		$titles = [
			'Category',
			'Sell',
			'Return',
			'Qta',
			'S.Tot.',
			'S.Val.',
			'S.Amt.'
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
				6 => 0
			];

			foreach ($list as $el)
			{
				if ($el['category'] != $category)
					continue;

				$row[1] += $el['selled'];
				$row[2] += $el['return'];
				$row[3] += $el['slr'];
				$row[4] += $el['slltot'];
				$row[5] += $el['sllval'];
				$row[6] += $el['sllamt'];
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
			6 => 0
		];

		foreach ($resl as $row)
		{
			$tot[1] += $row[1];
			$tot[2] += $row[2];
			$tot[3] += $row[3];
			$tot[4] += $row[4];
			$tot[5] += $row[5];
			$tot[6] += $row[6];
		}

		$tot[3] = number_format($tot[3], 2);
		$tot[4] = number_format($tot[4], 2);
		$tot[5] = number_format($tot[5], 2);
		$tot[6] = number_format($tot[6], 2);

		// return PDF

		return $this->getPDF([
			'headers' => $titles,
			'list' => $resl,
			'amounts' => [$tot],
			'footers' => [
				$this->container->getParameter('company_title'),
				'REPORT: Totals / Selled',
				date("Y/m/d H:i:s")
			],
			'filename' => 'report-totals-selled.pdf'
		]);
	}

	public function buysellReport($records)
	{
		$list = $this->recordsAmounts($records);
		$categories = $this->categories;

		// Result List

		$titles = [
			'Category',
			'Buy',
			'Sell',
			'B.Tot.',
			'B.Amt.',
			'S.Tot.',
			'S.Val.'
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
				6 => 0
			];

			foreach ($list as $el)
			{
				if ($el['category'] != $category)
					continue;

				$row[1] += $el['buyed'];
				$row[2] += $el['selled'];
				$row[3] += $el['buytot'];
				$row[4] += $el['buyamt'];
				$row[5] += $el['slltot'];
				$row[6] += $el['sllval'];
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
			6 => 0
		];

		foreach ($resl as $row)
		{
			$tot[1] += $row[1];
			$tot[2] += $row[2];
			$tot[3] += $row[3];
			$tot[4] += $row[4];
			$tot[5] += $row[5];
			$tot[6] += $row[6];
		}

		$tot[3] = number_format($tot[3], 2);
		$tot[4] = number_format($tot[4], 2);
		$tot[5] = number_format($tot[5], 2);
		$tot[6] = number_format($tot[6], 2);

		// return PDF

		return $this->getPDF([
			'headers' => $titles,
			'list' => $resl,
			'amounts' => [$tot],
			'footers' => [
				$this->container->getParameter('company_title'),
				'REPORT: Totals / Buyed & Selled',
				date("Y/m/d H:i:s")
			],
			'filename' => 'report-totals-buy-and-sell.pdf'
		]);
	}

	public function checkRecordsReport($records)
	{
		$list = $this->recordsAmounts($records);
		$categories = $this->categories;

		// Result List

		$titles = [
			'Category',
			'Buy',
			'Sell',
			'B.Tot.',
			'S.Tot.',
			'B.Val.',
			'S.Val.',
			'B.Amt.',
			'S.Amt.',
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

				$row[1] += $el['blb'];
				$row[2] += $el['slr'];
				$row[3] += $el['buytot'];
				$row[4] += $el['slltot'];
				$row[5] += $el['buyval'];
				$row[6] += $el['sllval'];
				$row[7] += $el['buyamt'];
				$row[8] += $el['sllamt'];
				// $row[7] += $el['bcktot'];
				// $row[8] += $el['rettot'];
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

		$tot[3] = number_format($tot[3], 2);
		$tot[4] = number_format($tot[4], 2);
		$tot[5] = number_format($tot[5], 2);
		$tot[6] = number_format($tot[6], 2);
		$tot[7] = number_format($tot[7], 2);
		$tot[8] = number_format($tot[8], 2);

		// return PDF

		return $this->getPDF([
			'headers' => $titles,
			'list' => $resl,
			'amounts' => [$tot],
			'footers' => [
				$this->container->getParameter('company_title'),
				'REPORT: Totals / Check Records',
				date("Y/m/d H:i:s")
			],
			'filename' => 'report-totals-check-records.pdf'
		]);
	}

	public function defaultReport($records)
	{
		$list = $this->recordsAmounts($records);
		$categories = $this->categories;

		// Result List

		$titles = [
			'Category',
			'Buyed',
			'Selled',
			'Returns',
			'Backs',
			'Buyed Tot.',
			'Selled Tot.'
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
				6 => 0
			];

			foreach ($list as $el)
			{
				if ($el['category'] != $category)
					continue;

				$row[1] += $el['buyed'];
				$row[2] += $el['selled'];
				$row[3] += $el['return'];
				$row[4] += $el['backed'];
				$row[5] += $el['buytot'];
				$row[6] += $el['slltot'];
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
			6 => 0
		];

		foreach ($resl as $row)
		{
			$tot[1] += $row[1];
			$tot[2] += $row[2];
			$tot[3] += $row[3];
			$tot[4] += $row[4];
			$tot[5] += $row[5];
			$tot[6] += $row[6];
		}

		$tot[5] = number_format($tot[5], 2);
		$tot[6] = number_format($tot[6], 2);

		// return PDF

		return $this->getPDF([
			'headers' => $titles,
			'list' => $resl,
			'amounts' => [$tot],
			'footers' => [
				$this->container->getParameter('company_title'),
				'REPORT: Totals / Default',
				date("Y/m/d H:i:s")
			],
			'filename' => 'report-totals-default.pdf'
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
		$set = $om->getRepository('MaciPageBundle:Shop\RecordSet')->findOneById($id);
		if (!$set)
			return $this->redirect($this->generateUrl('maci_admin_view'));

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
			'main_title' => $set->getLabel(),
			'recipient' => $set->getSupplier() ? $set->getSupplier()->getAddress() : null,
			'headers' => $titles,
			'list' => $list,
			'amounts' => [$tot],
			'footers' => [
				$this->container->getParameter('company_title'),
				'REPORT: Export > ' . $set->getLabel(),
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

	public function getPurchaseRecord($record)
	{
		$om = $this->getDoctrine()->getManager();
		$purchases = $om->getRepository('MaciPageBundle:Shop\Record')
			->findBy([
				'code' => $record->getCode(),
				'type' => 'purchas'
			]);
		$purchase = false;
		foreach ($purchases as $rec)
		{
			if ($rec->getProductVariant() == $record->getProductVariant() &&
				$rec->getVariantName() == $record->getVariantName())
			{
				$purchase = $rec;
				break;
			}
		}
		if (!$purchase) foreach ($purchases as $rec)
		{
			if ($rec->getProductVariant() == $record->getProductVariant())
			{
				$purchase = $rec;
				break;
			}
		}
		return $purchase;
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
		$list = $this->getDoctrine()->getManager()
			->getRepository('MaciPageBundle:Shop\Product')->findBy([
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

	public function recordsAmounts($records)
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
					'lstrec' => false,
					'lstbuy' => 0,
					'buyed'  => 0,
					'selled' => 0,
					'backed' => 0,
					'return' => 0,
					'blb'    => 0,
					'slr'    => 0,
					'buytot' => 0,
					'slltot' => 0,
					'rettot' => 0,
					'bcktot' => 0,
					'buyamt' => 0,
					'buyval' => 0,
					'sllamt' => 0,
					'sllval' => 0
				];

				if (false === array_search($category, $categories))
					array_push($categories, $category);
			}

			if ($record->isPurchase() && (!$item['buyprc'] || $item['buyprc'] < $record->getPrice()))
			{
				$item['buyprc'] = $record->getPrice();
			}

			else if (!$record->isPurchase() && (!$item['sllprc'] || $item['sllprc'] < $record->getPrice()))
			{
				$item['sllprc'] = $record->getPrice();
			}

			if ($record->isPurchase())
			{
				$item['lstbuy'] = $record->getPrice();
				$item['buyed'] += $record->getQuantity();
				$item['buytot'] += $record->getPrice() * $record->getQuantity();
			}

			else if ($record->isSale())
			{
				$item['selled'] += $record->getQuantity();
				$item['slltot'] += $record->getPrice() * $record->getQuantity();
			}

			else if ($record->isReturn())
			{
				$item['return'] += $record->getQuantity();
				$item['slltot'] -= $record->getPrice() * $record->getQuantity();
				$item['rettot'] += $record->getPrice() * $record->getQuantity();
			}

			else if ($record->isBack())
			{
				$item['backed'] += $record->getQuantity();
				$item['buytot'] -= $item['lstbuy'] * $record->getQuantity();
				$item['bcktot'] += $item['lstbuy'] * $record->getQuantity();
			}

			$item['lstrec'] = $record;

			$list[$id] = $item;
		}

		$products = [];
		foreach ($list as $id => $el)
		{
			$el['errors'] = [];

			$el['blb'] = $el['buyed'] - $el['backed'];
			$el['slr'] = $el['selled'] - $el['return'];

			if ($el['buyprc'] == null)
			{
				$purchase = $this->getPurchaseRecord($el['lstrec']);
				if (!$purchase)
					array_push($el['errors'], [$id, 'Sell price is null.']);
				else $el['buyprc'] = $purchase->getPrice();
			}

			if ($el['buyprc'] != null)
			{
				$el['buyval'] += $el['buyprc'] * $el['buyed'];
				$el['sllval'] += $el['buyprc'] * $el['selled'];
			}

			if ($el['sllprc'] == null)
			{
				$product = $this->getProduct($el['lstrec'], $products);
				if (!$product)
					array_push($el['errors'], [$id, 'Sell price is null.']);
				else $el['sllprc'] = $product->getPrice();
			}

			if ($el['sllprc'] != null)
			{
				$el['buyamt'] += $el['sllprc'] * $el['buyed'];
				$el['sllamt'] += $el['sllprc'] * $el['selled'];
			}

			$list[$id] = $el;
		}

		sort($categories);
		$this->categories = $categories;

		return $list;
	}
}

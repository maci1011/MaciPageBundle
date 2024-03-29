<?php

namespace Maci\PageBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Intl\Countries;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Maci\UserBundle\Controller\AddressServiceController;
use Maci\UserBundle\Entity\Address;
use Maci\PageBundle\Entity\Order\Order;
use Maci\PageBundle\Entity\Order\Item;
use Maci\TranslatorBundle\Controller\TranslatorController;

class OrderService extends AbstractController
{
	private $om;

	private $request;

	private $authorizationChecker;

	private $tokenStorage;

	private $user;

	private $session;

	private $kernel;

	private $ac;

	private $cart;

	private $configs;

	private $shippings;

	private $countries;

	public function __construct(ObjectManager $objectManager, RequestStack $requestStack, AuthorizationCheckerInterface $authorizationChecker, TokenStorageInterface $tokenStorage, Session $session, \App\Kernel $kernel, AddressServiceController $ac, TranslatorController $translator, $configs)
	{
		$this->om = $objectManager;
		$this->request = $requestStack->getCurrentRequest();
		$this->authorizationChecker = $authorizationChecker;
		$this->tokenStorage = $tokenStorage;
		$this->session = $session;
		$this->kernel = $kernel;
		$this->ac = $ac;
		$this->translator = $translator;
		$this->configs = $configs;
		$this->cart = false;
	}

	public function addToCart($product, $quantity, $variant)
	{
		$cart = $this->getCurrentCart();

		if (!$this->addProduct($cart, $product, $quantity, $variant))
			return false;

		$cart->refreshAmount();

		$this->saveCart($cart);

		return true;
	}

	public function addProduct($order, $product, $quantity, $variant)
	{
		$item = $order->addProduct($product, $quantity, $variant);

		if (!$item)
			return false;

		if (is_object($item) && true === $this->authorizationChecker->isGranted('ROLE_USER'))
			$this->om->persist($item);

		return true;
	}

	public function editItemQuantity($id, $quantity)
	{
		if (true === $this->authorizationChecker->isGranted('ROLE_USER')) {
			$item = $this->om->getRepository('MaciPageBundle:Order\Item')
				->findOneById($id);

			if (!$item) {
				return false;
			}

			$item->setQuantity($quantity);

			if (!$item->checkAvailability($quantity)) {
				return false;
			}

			$item->getOrder()->refreshAmount();

			$this->om->flush();
		} else {
			$items = $this->session->get('order_items');

			if (!is_array($items) || !count($items) || count($items) <= $id) {
				return false;
			}

			$items[$id]['quantity'] = $quantity;

			$this->session->set('order_items', $items);
		}

		return true;
	}

	public function removeItem($id)
	{
		if (true === $this->authorizationChecker->isGranted('ROLE_USER')) {
			$cart = $this->getCurrentCart();
			$item = false;
			foreach ($cart->getItems() as $_item) {
				if ( $_item->getId() === intval($id) ) {
					$item = $_item;
					break;
				}
			}
			if (
				!$item ||
				(
					$item->getOrder()->getUser() &&
					$item->getOrder()->getUser()->getId() !== $this->tokenStorage->getToken()->getUser()->getId() &&
					false === $this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')
				)
			) {
				return false;
			}
			$this->om->remove($item);
			$this->om->flush();
			$this->saveCart();
		} else {
			$items = $this->session->get('order_items');
			if (!is_array($items) || !count($items) || count($items) <= $id) {
				return false;
			}
			array_splice($items, $id);
			$this->session->set('order_items', $items);
		}
		return true;
	}

	public function setCartMail($mail)
	{
		$this->getCurrentCart();
		$this->cart->setMail($mail);
		$this->saveCart();
	}

	public function setCartPayment($payment, $cost)
	{
		$this->getCurrentCart();
		$this->cart->setPayment($payment);
		$this->cart->setPaymentCost($cost);
		$this->saveCart();
	}

	public function setCartShipping($shipping)
	{
		$item = $this->getShippingItem($shipping);
		if(!$item) { return; }

		$this->getCurrentCart();

		$this->cart->setShipping($shipping);

		$payment = $this->cart->getPayment();
		if($payment && !in_array($payment, array_keys($this->getCartShippingPayments())))
			$this->cart->setPayment(null);

		if (0 < $this->configs['free_shipping_over'] &&
			$this->configs['free_shipping_over'] < $this->cart->getAmount()
		) $this->cart->setShippingCost(0);
		else $this->cart->setShippingCost($item['cost']);

		$this->saveCart();
	}

	public function startCartCheckout($type)
	{
		$this->getCurrentCart();

		$this->cart->setShippingAddress(null);
		$this->cart->setBillingAddress(null);
		$this->cart->setShipping(null);
		$this->cart->setPayment(null);

		switch ($type)
		{
			case 'pickup':
				$this->cart->setShipping(null);
				$this->cart->setPayment('pickup_in_store');
				break;

			case 'booking':
				break;

			default:
				$type = 'checkout';
				break;
		}

		$this->cart->setCheckout($type);

		$this->saveCart();
	}

	// public function setCartCheckout($checkout)
	// {
	// 	$this->getCurrentCart();
	// 	$this->cart->setCheckout($checkout);
	// 	$this->saveCart();
	// }

	public function setCartLocale($locale = false)
	{
		$this->getCurrentCart();
		$this->cart->setLocale($locale ? $locale : $this->request->getLocale());
		$this->saveCart();
	}

	public function setCartShippingAddress($address)
	{
		$this->getCurrentCart();
		$this->cart->setShippingAddress($address);
		$this->saveCart();
	}

	public function setCartBillingAddress($address)
	{
		$this->getCurrentCart();
		$this->cart->setBillingAddress($address);
		$this->saveCart();
	}

	public function refreshCartAmount()
	{
		$this->getCurrentCart();
		$this->cart->refreshAmount();
		$this->saveCart();
	}

	public function confirmCart()
	{
		$cart = $this->getCurrentCart();
		if ($cart->confirmOrder()) {
			if (!$cart->getId()) {
				$this->om->persist($cart);
			}
			foreach ($cart->getItems() as $item) {
				if (!$item->getId()) {
					$this->om->persist($item);
				}
			}
			$this->saveCart();
			$this->resetCart();
			return $cart;
		}
		return false;
	}

	public function completeOrder($cart)
	{
		if ($cart->completeOrder()) {
			$this->saveCart($cart);
			return $cart;
		}
		return false;
	}

	public function resetCart()
	{
		$this->cart = false;
		$this->session->remove('order');
		$this->session->remove('order_items');
	}

	public function saveCart($cart = false)
	{
		if ($cart === false) $cart = $this->getCurrentCart();
		if (true === $this->authorizationChecker->isGranted('ROLE_USER') || $cart->getStatus() === 'confirm')
		{
			if (!$cart->getid() ) {
				$this->om->persist($cart);
			}
			$this->om->flush();
		}
		else
		{
			$cart->setStatus('session');
			$this->refreshSession($cart);
		}
	}

	public function getCurrentCart()
	{
		if ($this->cart)
		{
			return $this->cart;
		}

		if (true === $this->authorizationChecker->isGranted('ROLE_USER'))
		{

			$cart = $this->om->getRepository('MaciPageBundle:Order\Order')
				->findOneBy(array('user'=>$this->tokenStorage->getToken()->getUser(), 'type'=>'cart', 'status'=>'current'));

			if (!$cart)
			{
				$cart = $this->getNewCart();
				$cart->setUser($this->tokenStorage->getToken()->getUser());
				$cart->setStatus('current');
			}

			$order_arr = $this->getSessionArray();
			if ($order_arr['status'] == 'session')
			{
				$cart = $this->loadCartFromSession($cart);
				$this->resetCart();
			}

			if (!$cart->getLocale())
			{
				$cart->setLocale($this->request->getLocale());
			}

			$this->saveCart($cart);
		}
		else
		{
			$cart = $this->loadCartFromSession();
		}

		$this->cart = $cart;
		return $this->cart;
	}

	public static function getCartDefaultValues()
	{
		return [
			'name' => 'My Cart',
			'code' => 'CRT-' . rand(10000, 99999) . '-' . 
				date('h') . date('i') . date('s') . date('m') . date('d') . date('Y'),
			'status' => 'new',
			'type' => 'cart',
			'mail' => null,
			'checkout' => null,
			'amount' => 0,
			'shipping' => null,
			'shipping_cost' => 0,
			'payment' => null,
			'payment_cost' => 0,
			'shippingAddress' => null,
			'billingAddress' => null
		];
	}

	public function orderToArray($order, $info = false)
	{
		return [
			'name' => $order->getName(),
			'code' => $order->getCode(),
			'status' => $order->getStatus(),
			'type' => $order->getType(),
			'mail' => $order->getMail(),
			'checkout' => $order->getCheckout(),
			'amount' => $order->getAmount(),
			'shipping' => $order->getShipping(),
			'shipping_cost' => $order->getShippingCost(),
			'payment' => $order->getPayment(),
			'payment_cost' => $order->getPaymentCost(),
			'shippingAddress' => $info ? $info['shippingAddress'] :
				AddressServiceController::getArrayFromAddress($order->getShippingAddress()),
			'billingAddress' => $info ? $info['billingAddress'] :
				AddressServiceController::getArrayFromAddress($order->getBillingAddress())
		];
	}

	public function setOrderValues($order, $values = false)
	{
		if (!$values) $values = $this->getCartDefaultValues();
		$order->setName($values['name']);
		$order->setCode($values['code']);
		$order->setStatus($values['status']);
		$order->setType($values['type']);
		$order->setMail($values['mail']);
		$order->setCheckout($values['checkout']);
		$order->setShipping($values['shipping']);
		$order->setShippingCost($values['shipping_cost']);
		$order->setPayment($values['payment']);
		$order->setPaymentCost($values['payment_cost']);
		$order->setShippingAddress(
			AddressServiceController::getAddressFromArray($values['shippingAddress'])
		);
		$order->setBillingAddress(
			AddressServiceController::getAddressFromArray($values['billingAddress'])
		);
		return $order;
	}

	public function getNewCart()
	{
		return $this->setOrderValues(new Order);
	}

	public function loadCartFromSession($cart = false)
	{
		$values = $this->getSessionArray();
		$items = $this->getSessionItems();

		if (!$cart) $cart = $this->setOrderValues(new Order, $values);

		foreach ($items as $item) {
			$quantity = $item['quantity'];
			$product = $this->om->getRepository('MaciPageBundle:Shop\Product')
				->findOneById($item['id']);
			// if ($product && $product->isAvailable() && $product->checkQuantity($quantity))
			$this->addProduct($cart, $product, $quantity, $item['variant']);
		}

		if ($values['shippingAddress'] !== null)
		{
			$address = $values['shippingAddress'];
			if (is_numeric($address)) {
				$address = $this->ac->getAddress($address);
			}
			if ($address) {
				$cart->setShippingAddress(AddressServiceController::getAddressFromArray($address));
			}
		}

		if ($values['billingAddress'] !== null)
		{
			$address = $values['billingAddress'];
			if (is_numeric($address)) {
				$address = $this->ac->getAddress($address);
			}
			if ($address) {
				$cart->setBillingAddress(AddressServiceController::getAddressFromArray($address));
			}
		}

		$cart->refreshAmount();
		return $cart;
	}

	public function getSessionArray()
	{
		$order = $this->session->get('order');

		if (!is_array($order))
			return $this->getCartDefaultValues();

		return $order;
	}

	public function getSessionItems()
	{
		$items = $this->session->get('order_items');

		if (!is_array($items))
			return [];

		return $items;
	}

	public function refreshSession($order)
	{
		if (!$order->getStatus() == 'session')
		{
			$this->resetCart();
			return;
		}

		$order_arr = $this->orderToArray($order);
		// Save Order in Session
		$this->session->set('order', $order_arr);

		$items = array();
		foreach ($order->getItems() as $item) {
			if (is_object($product = $item->getProduct())) {
				array_push($items, [
					'id' => $product->getId(),
					'name' => $product->getName(),
					'sale' => $product->getSale(),
					'price' => $product->getAmount(),
					'quantity' => $item->getQuantity(),
					'variant' => $item->getVariant()
				]);
			}
		}
		// Save Order Items in Session
		$this->session->set('order_items', $items);
	}

	public function getConfigs()
	{
		return $this->configs;
	}

	public function getPaymentsArray()
	{
		return $this->configs['payments'];
	}

	public function getPaymentChoices($order)
	{
		$payments = $this->getOrderShippingPayments($order);
		if (!$payments) return [];

		$choices = [];
		foreach ($payments as $name => $pay)
		{
			if (!$this->checkPaymentCheckout($order, $pay) ||
				($pay['sandbox'] && false === $this->authorizationChecker->isGranted('ROLE_ADMIN') && $this->kernel->getEnvironment() == "prod")
			) continue;

			$choices[$this->getPaymentLabel($name, $pay) . (
				0 < $pay['cost'] ? ' (+' . $this->getPaymentCostLabel($pay) . ')' : ''
			)] = $name;
		}

		return $choices;
	}

	public function getPaymentLabel($name, $payment)
	{
		return $this->translator->getText('order.payments.' . $name, $payment['label']);
	}

	public function getPaymentCostLabel($payment)
	{
		return $payment['cost'] ? number_format($payment['cost'], 2, '.', ',') . ' €' : 'Free';
	}

	public function getPaymentLabelById($id, $f = 'getPaymentLabel')
	{
		return array_key_exists($id, $this->configs['payments']) ? $this->$f($this->configs['payments'][$id]) : null;
	}

	public function getPaymentCostLabelById($id)
	{
		return $this->getPaymentLabelById($id, 'getPaymentCostLabel');
	}

	public function getCartPaymentLabel($f = 'getPaymentLabelById')
	{
		if(!$this->cart->getPayment()) {
			return null;
		}
		return $this->$f($this->cart->getPayment());
	}

	public function getCartPaymentCostLabel()
	{
		return $this->getCartPaymentLabel('getPaymentCostLabelById');
	}

	public function getCouriersArray()
	{
		return $this->configs['shippings'];
	}

	public function getShippingsArray()
	{
		if ($this->shippings) {
			return $this->shippings;
		}

		$shippings = [];
		foreach ($this->getCouriersArray() as $name => $courier)
		{
			if (array_key_exists('countries', $courier)) {
				foreach ($courier['countries'] as $id => $country) {
					$shippings['shipping_'.count($shippings)] = [
						'country' => $id,
						'courier' => $name,
						'label' => $courier['label'],
						'cost' => (array_key_exists('cost', $country) ? $country['cost'] : $courier['default_cost'])
					];
				};
			}
		}
		$this->shippings = $shippings;

		return $shippings;
	}

	public function getShippingLabel($shipping)
	{
		return $shipping['label'];
	}

	public function getShippingCountryLabel($shipping)
	{
		return Countries::getName($shipping['country']);
	}

	public function getShippingCostLabel($shipping)
	{
		return (0 < $shipping['cost'] ? number_format($shipping['cost'], 2, '.', ',') . ' €' : 'Free');
	}

	public function getShippingLabelById($id, $f = 'getShippingLabel')
	{
		$this->getShippingsArray();
		return array_key_exists($id, $this->shippings) ? $this->$f($this->shippings[$id]) : null;
	}

	public function getShippingCountryLabelById($id)
	{
		return $this->getShippingLabelById($id, 'getShippingCountryLabel');
	}

	public function getShippingCostLabelById($id)
	{
		return $this->getShippingLabelById($id, 'getShippingCostLabel');
	}

	public function getShippingChoices($order)
	{
		$choices = [];
		$country = false;

		if($order->getShippingAddress())
			$country = $order->getShippingAddress()->getCountry();

		foreach ($this->getShippingsArray() as $key => $ship)
		{
			if ($country && $country != $ship['country'])
				continue;

			$choices[$this->getShippingLabel($ship) . (
				0 < $ship['cost'] ? ' (+' . $this->getShippingCostLabel($ship) . ')' : ''
			)] = $key;
		}

		return $choices;
	}

	public function getPaymentItem($id)
	{
		$list = $this->getPaymentsArray();

		if (!array_key_exists($id, $list))
			return false;

		return $list[$id];
	}

	public function getCartPaymentItem()
	{
		$this->getCurrentCart();

		if (!$this->cart->getPayment())
			return false;

		return $this->getPaymentItem($this->cart->getPayment());
	}

	public function getCartPaymentGateway()
	{
		$item = $this->getCartPaymentItem();

		if (!$item)
			return false;

		return $item['gateway'];
	}

	public function getShippingItem($id)
	{
		$list = $this->getShippingsArray();

		if (!array_key_exists($id, $list))
			return false;

		return $list[$id];
	}

	public function getOrderShippingItem($order)
	{
		if(!$order->getShipping())
			return false;

		return $this->getShippingItem($order->getShipping());
	}

	public function getCartShippingItem()
	{
		return $this->getOrderShippingItem($this->getCurrentCart());
	}

	public function getCartShippingLabel($f = 'getShippingLabelById')
	{
		$this->getCurrentCart();

		if(!$this->cart->getShipping())
			return null;

		return $this->$f($this->cart->getShipping());
	}

	public function getCartShippingCountry()
	{
		$item = $this->getCartShippingItem();

		if (!$item)
			return false;

		return $item['country'];
	}

	public function getCartShippingCountryLabel()
	{
		return $this->getCartShippingLabel('getShippingCountryLabelById');
	}

	public function getCartShippingCostLabel()
	{
		return $this->getCartShippingLabel('getShippingCostLabelById');
	}

	public function getCartShippingCourier()
	{
		$item = $this->getCartShippingItem();

		if (!$item)
			return false;

		return $item['courier'];
	}

	public function checkPaymentCheckout($order, $payment)
	{
		return !(array_key_exists('checkouts', $payment) && 0 < count($payment['checkouts']) && (
			!in_array('all', $payment['checkouts']) || !in_array($order->getType(), $payment['checkouts'])
		));
	}

	public function getOrderShippingPayments($order)
	{
		$item = $this->getOrderShippingItem($order);
		if (!$item) return false;

		$courier = $this->getCouriersArray()[$item['courier']];

		if (!$this->checkPaymentCheckout($order, $courier))
			return false;

		if(!count($courier['payments']) || in_array('all', $courier['payments']))
			return $this->getPaymentsArray();

		$list = [];
		$paymentNames = array_keys($this->getPaymentsArray());

		foreach ($courier['payments'] as $key => $value)
			if (in_array($value, $paymentNames))
				$list[$value] = $this->getPaymentsArray()[$value];

		return $list;
	}

	public function getCartShippingPayments()
	{
		return $this->getOrderShippingPayments($this->getCurrentCart());
	}

	public function getAvailableCountries()
	{
		if (!isset($this->available_countries))
		{
			$this->available_countries = [];
			foreach ($this->getCouriersArray() as $value)
			{
				if (array_key_exists('countries', $value))
					$this->available_countries = array_merge($this->available_countries, $value['countries']);
			}
		}
		return $this->available_countries;
	}

	public function getAvailableCountriesLabel()
	{
		$list = [];
		foreach ($this->getAvailableCountries() as $code => $v)
			array_push($list, $this->getCountryName($code));
		return implode(', ', $list);
	}

	public function getCountryChoices()
	{
		$choices = [];
		foreach ($this->getAvailableCountries() as $code => $value) {
			$choices[$this->getCountryName($code)] = $code;
		}
		return $choices;
	}

	public function getCountryName($code)
	{
		return Countries::getName($code);
	}
}

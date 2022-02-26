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

	public function __construct(ObjectManager $objectManager, RequestStack $requestStack, AuthorizationCheckerInterface $authorizationChecker, TokenStorageInterface $tokenStorage, Session $session, \App\Kernel $kernel, AddressServiceController $ac, $configs)
	{
		$this->om = $objectManager;
		$this->request = $requestStack->getCurrentRequest();
		$this->authorizationChecker = $authorizationChecker;
		$this->tokenStorage = $tokenStorage;
		$this->session = $session;
		$this->kernel = $kernel;
		$this->ac = $ac;
		$this->configs = $configs;
		$this->cart = false;
	}

	public function addToCart($product, $quantity, $variant)
	{
		$cart = $this->getCurrentCart();

		if (!$this->addProduct($cart, $product, $quantity, $variant)) {
			return false;
		}

		$cart->refreshAmount();

		$this->saveCart($cart);

		return true;
	}

	public function addProduct($order, $product, $quantity, $variant)
	{
		$item = $order->addProduct($product, $quantity, $variant);
		if (!$item) {
			// $this->removeItem($item);
			return false;
		}

		if (is_object($item) && true === $this->authorizationChecker->isGranted('ROLE_USER')) {
			$this->om->persist($item);
		}

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
		if($payment) {
			$payments = $this->getCartShippingPayments();
			if(!in_array($payment, $payments)) {
				$this->cart->setPayment(null);
			}
		}

		if ( 0 < $this->configs['free_shipping_over'] ) {
			if ( $this->configs['free_shipping_over'] < $this->cart->getAmount() ) {
				$this->cart->setShippingCost(0);
			} else {
				$this->cart->setShippingCost($item['cost']);
			}
		} else {
			$this->cart->setShippingCost($item['cost']);
		}

		$this->saveCart();
	}

	public function setCartCheckout($checkout)
	{
		$this->getCurrentCart();
		$this->cart->setCheckout($checkout);
		$this->saveCart();
	}

	public function setCartLocale($locale)
	{
		$this->getCurrentCart();
		$this->cart->setLocale($locale);
		$this->saveCart();
	}

	public function setCartShippingAddress($address)
	{
		if (true === $this->authorizationChecker->isGranted('ROLE_USER')) {
			$this->getCurrentCart();
			$this->cart->setShippingAddress($address);
			$this->om->flush();
		} else {
			$info = $this->getSessionArray();
			$info['shippingAddress'] = $address;
			$this->session->set('order', $info);
		}
	}

	public function setCartBillingAddress($address)
	{
		if (true === $this->authorizationChecker->isGranted('ROLE_USER')) {
			$this->getCurrentCart();
			$this->cart->setBillingAddress($address);
			$this->om->flush();
		} else {
			$info = $this->getSessionArray();
			$info['billingAddress'] = $address;
			$this->session->set('order', $info);
		}
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

	public function getSessionArray()
	{
		$order = $this->session->get('order');
		if (!is_array($order)) {
			return $this->getCartDefaultValues();
		}
		return $order;
	}

	public function getSessionItems()
	{
		$items = $this->session->get('order_items');
		if (!is_array($items)) {
			return [];
		}
		return $items;
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
			'shippingAddress' => $info ? $info['shippingAddress'] : null,
			'billingAddress' => $info ? $info['billingAddress'] : null
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

		if (count($items)) {
			foreach ($items as $item) {
				$quantity = $item['quantity'];
				$product = $this->om->getRepository('MaciPageBundle:Shop\Product')
					->findOneById($item['id']);
				if ($product && $product->isAvailable() && $product->checkQuantity($quantity)) {
					$this->addProduct($cart, $quantity, $product);
				}
			}
		}

		if ($values['shippingAddress'] !== null) {
			$address = $values['shippingAddress'];
			if (is_numeric($address)) {
				$address = $this->ac->getAddress($address);
			}
			if ($address) {
				$cart->setShippingAddress($address);
			}
		}

		if ($values['billingAddress'] !== null) {
			$address = $values['billingAddress'];
			if (is_numeric($address)) {
				$address = $this->ac->getAddress($address);
			}
			if ($address) {
				$cart->setBillingAddress($address);
			}
		}

		$cart->refreshAmount();
		return $cart;
	}

	public function refreshSession($order)
	{
		if (!$order->getStatus() == 'session') {
			$this->resetCart();
			return;
		}

		$order_arr = $this->orderToArray($order);
		$this->session->set('order', $order_arr);

		$items = array();
		foreach ($order->getItems() as $item) {
			if (is_object($product = $item->getProduct())) {
				array_push($items, array(
					'id' => $product->getId(),
					'name' => $product->getName(),
					'sale' => $product->getSale(),
					'price' => $product->getAmount(),
					'quantity' => $item->getQuantity()
				));
			}
		}
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

	public function getPaymentChoices()
	{
		$choices = array();
		foreach ($this->getCartShippingPayments() as $name => $value) {
			if(!$value['sandbox'] || ($value['sandbox'] && $this->kernel->getEnvironment() == "dev")) {
				$choices[$this->getPaymentLabel($value)] = $name;
			}
		}
		return $choices;
	}

	public function getPaymentLabel($payment)
	{
		$label = $payment['label'];
		if ($payment['cost']) {
			$label .= ' | ' . number_format($payment['cost'], 2, '.', ',') . ' €';
		}
		return $label;
	}

	public function getCartPaymentLabel()
	{
		if(!$this->cart->getPayment()) {
			return null;
		}
		return $this->getPaymentLabelById($this->cart->getPayment());
	}

	public function getPaymentLabelById($id)
	{
		return array_key_exists($id, $this->configs['payments']) ? $this->getPaymentLabel($this->configs['payments'][$id]) : null;
	}

	public function getCouriersArray()
	{
		return $this->configs['couriers'];
	}

	public function getShippingsArray()
	{
		if ($this->shippings) {
			return $this->shippings;
		}

		$shippings  = array();
		foreach ($this->getCouriersArray() as $name => $courier) {
			if (array_key_exists('countries', $courier)) {
				foreach ($courier['countries'] as $id => $country) {
					$shippings['shipping_'.count($shippings)] = array(
						'country' => $id,
						'courier' => $name,
						'label' => $courier['label'],
						'cost' => (array_key_exists('cost', $country) ? $country['cost'] : $courier['default_cost'])
					);
				};
			}
		}
		$this->shippings = $shippings;

		return $shippings;
	}

	public function getShippingLabel($shipping)
	{
		return $shipping['label'] . ' | ' . Countries::getName($shipping['country']) . ' | ' . number_format($shipping['cost'], 2, '.', ',') . ' €';
	}

	public function getShippingLabelById($id)
	{
		$this->getShippingsArray();
		return array_key_exists($id, $this->shippings) ? $this->getShippingLabel($this->shippings[$id]) : null;
	}

	public function getShippingChoices()
	{
		$choices = array();
		$country = false;
		if($this->cart->getShippingAddress()) {
			$country = $this->cart->getShippingAddress()->getCountry();
		}
		foreach ($this->getShippingsArray() as $key => $value) {
			if ($country) {
				if($country == $value['country']) {
					$choices[$this->getShippingLabel($value)] = $key;
				}
			} else {
				$choices[$this->getShippingLabel($value)] = $key;
			}
		}
		return $choices;
	}

	public function getPaymentItem($id)
	{
		$list = $this->getPaymentsArray();
		if (array_key_exists($id, $list)) {
			return $list[$id];
		}
		return false;
	}

	public function getCartPaymentItem()
	{
		$this->getCurrentCart();
		if($this->cart->getPayment()) {
			return $this->getPaymentItem($this->cart->getPayment());
		}
		return false;
	}

	public function getCartPaymentGateway()
	{
		$item = $this->getCartPaymentItem();
		if ($item) {
			return $item['gateway'];
		}
		return false;
	}

	public function getShippingItem($id)
	{
		$list = $this->getShippingsArray();
		if (array_key_exists($id, $list)) {
			return $list[$id];
		}
		return false;
	}

	public function getCartShippingItem()
	{
		$this->getCurrentCart();
		if($this->cart->getShipping()) {
			return $this->getShippingItem($this->cart->getShipping());
		}
		return false;
	}

	public function getCartShippingLabel()
	{
		$this->getCurrentCart();
		if($this->cart->getShipping()) {
			return $this->getShippingLabelById($this->cart->getShipping());
		}
		return null;
	}

	public function getCartShippingCountry()
	{
		$item = $this->getCartShippingItem();
		if ($item) {
			return $item['country'];
		}
		return false;
	}

	public function getCartShippingCourier()
	{
		$item = $this->getCartShippingItem();
		if ($item) {
			return $item['courier'];
		}
		return false;
	}

	public function getCartShippingPayments()
	{
		$item = $this->getCartShippingItem();
		$courier = $this->getCouriersArray()[$item['courier']];
		if(!array_key_exists('payments', $courier)) {
			return [];
		}
		if(in_array('all', $courier['payments'])) {
			return $this->getPaymentsArray();
		}
		$return = [];
		$paymentNames = array_keys($this->getPaymentsArray());
		foreach ($courier['payments'] as $key => $value) {
			if (in_array($value, $paymentNames)) {
				$return[$value] = $this->getPaymentsArray()[$value];
			}
		}
		return $return;
	}

	public function getAvailableCountries()
	{
		if (!isset($this->available_countries)) {
			$this->available_countries  = array();
			foreach ($this->getCouriersArray() as $key => $value) {
				if (array_key_exists('countries', $value)) {
					$this->available_countries = array_merge($this->available_countries, $value['countries']);
				}
			}
		}
		return $this->available_countries;
	}

	public function getCountryChoices()
	{
		$choices = [];
		foreach ($this->getAvailableCountries() as $key => $value) {
			$choices[Countries::getName($key)] = $key;
		}
		return $choices;
	}
}

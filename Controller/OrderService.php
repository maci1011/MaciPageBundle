<?php

namespace Maci\PageBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Intl\Countries;
use Symfony\Component\HttpFoundation\Request;
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

	public function __construct(ObjectManager $objectManager, AuthorizationCheckerInterface $authorizationChecker, TokenStorageInterface $tokenStorage, Session $session, \App\Kernel $kernel, AddressServiceController $ac, $configs)
	{
		$this->om = $objectManager;
		$this->authorizationChecker = $authorizationChecker;
		$this->tokenStorage = $tokenStorage;
		$this->session = $session;
		$this->kernel = $kernel;
		$this->ac = $ac;
		$this->configs = $configs;
		$this->cart = false;
	}

	public function addToCart($product, $variant, $quantity = 1)
	{
		$cart = $this->getCurrentCart();

		$item = $this->addProduct($cart, $product, $variant, $quantity);

		if (!$item) {
			return false;
		}

		$cart->refreshAmount();

		$this->saveCart($cart);

		return true;
	}

	public function addProduct($order, $product, $variant, $quantity = 1)
	{
		$same_item = false;
		foreach ($order->getItems() as $item) {
			$item_product = $item->getProduct();
			if (!is_object($item_product)) {
				break;
			} else if ($item_product->getId() == $product->getId()) {
				$same_item = $item;
				break;
			}
		}

		if ($same_item) {
			$item = $same_item;
			$item->setQuantity( $item->getQuantity() + $quantity );
		} else {
			$item = new Item;
			$item->setQuantity($quantity);
			$item->setProduct($product);
			$item->setOrder($order);
			if ($variant != false) $item->setVariant($product, $variant);
			if (true === $this->authorizationChecker->isGranted('ROLE_USER')) {
				$this->om->persist($item);
			}
		}

		if (!$item->checkAvailability()) {
			return false;
		}

		$order->addItem($item);

		return $item;
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
			$info = $this->getDefaultSession();
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
			$info = $this->getDefaultSession();
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

	public function resetCart()
	{
		$this->cart = false;
		$this->session->set('order', false);
		$this->session->set('order_items', array());
	}

	public function saveCart($cart = false)
	{
		if ($cart === false) $cart = $this->getCurrentCart();
		if (true === $this->authorizationChecker->isGranted('ROLE_USER') || $cart->getStatus() === 'confirm') {
			if (!$cart->getid() ) {
				$this->om->persist($cart);
			}
			$this->om->flush();
		}
		$this->refreshSession($cart);
	}

	public function getCurrentCart()
	{
		if ($this->cart) {
			return $this->cart;
		}

		if (true === $this->authorizationChecker->isGranted('ROLE_USER')) {

			$cart = $this->om->getRepository('MaciPageBundle:Order\Order')
				->findOneBy(array('user'=>$this->tokenStorage->getToken()->getUser(), 'type'=>'cart', 'status'=>'current'));

			$order_arr = $this->getDefaultSession();

			if (!$cart) {
				if (array_key_exists('status', $order_arr) && $order_arr['status'] !== 'current') {
					$this->resetCart();
				}
				$cart = $this->setCart(new Order);
				$cart->setUser($this->tokenStorage->getToken()->getUser());
				$this->om->persist($cart);
			}

			if ($order_arr['status'] === 'session') {
				$cart = $this->loadCartFromSession($cart);
				$cart->setStatus('current');
				foreach ($cart->getItems() as $item) {
					$this->om->persist($item);
				}
				$this->refreshSession($cart);
			}

			$cart->refreshAmount();
			$this->om->flush();

		} else {

			$cart = $this->setCart(new Order);
			$cart = $this->loadCartFromSession($cart);

		}

		return $this->cart = $cart;
	}

	public function setCart($cart)
	{
		$order_arr = $this->getDefaultSession();
		$cart->setName( $order_arr['name'] );
		$cart->setCode( $order_arr['code'] );
		$cart->setStatus( $order_arr['status'] );
		$cart->setType( $order_arr['type'] );
		$cart->setMail( $order_arr['mail'] );
		$cart->setCheckout( $order_arr['checkout'] );
		$cart->setShipping( $order_arr['shipping'] );
		$cart->setShippingCost( $order_arr['shipping_cost'] );
		$cart->setPayment( $order_arr['payment'] );
		$cart->setPaymentCost( $order_arr['payment_cost'] );
		return $cart;
	}

	public function getDefaultSession()
	{
		$order_arr = $this->session->get('order');
		if (!is_array($order_arr)) {
			if (true === $this->authorizationChecker->isGranted('ROLE_USER')) {
				$status = 'current';
			} else {
				$status = 'session';
			}
			$this->session->set('order', $order_arr = array(
				'name' => 'My Cart',
				'code' => 'CRT-' . rand(10000, 99999) . '-' . 
					date('h') . date('i') . date('s') . date('m') . date('d') . date('Y'),
				'status' => $status,
				'type' => 'cart',
				'mail' => null,
				'checkout' => null,
				'shippingAddress' => null,
				'billingAddress' => null,
				'shipping' => null,
				'shipping_cost' => 0,
				'payment' => null,
				'payment_cost' => 0,
				'amount' => 0
			));
			$this->session->set('order_items', array());
		}
		return $order_arr;
	}

	public function refreshSession($order)
	{
		$info = $this->getDefaultSession();

		$order_arr = array(
			'name' => $order->getName(),
			'code' => $order->getCode(),
			'status' => $order->getStatus(),
			'type' => $order->getType(),
			'mail' => $order->getMail(),
			'amount' => $order->getAmount(),
			'shipping' => $order->getShipping(),
			'shipping_cost' => $order->getShippingCost(),
			'payment' => $order->getPayment(),
			'payment_cost' => $order->getPaymentCost(),
			'checkout' => $order->getCheckout(),
			'shippingAddress' => $info['shippingAddress'],
			'billingAddress' => $info['billingAddress']
		);

		$this->session->set('order', array_merge($info, $order_arr));

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

	public function loadCartFromSession($cart)
	{
		$order_arr = $this->getDefaultSession();
		$items = $this->session->get('order_items', array());
		if (count($items)) {
			foreach ($items as $item) {
				$quantity = $item['quantity'];
				$product = $this->om->getRepository('MaciProductBundle:Product')
					->findOneById($item['id']);
				if ($product && $product->isAvailable() && $product->checkQuantity($quantity)) {
					$this->addProduct($cart, $product, $quantity);
				}
			}
		}

		if ($order_arr['shippingAddress'] !== null) {
			$address = $order_arr['shippingAddress'];
			if (is_numeric($address)) {
				$address = $this->ac->getAddress($address);
			}
			if ($address) {
				$cart->setShippingAddress($address);
			}
		}

		if ($order_arr['billingAddress'] !== null) {
			$address = $order_arr['billingAddress'];
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

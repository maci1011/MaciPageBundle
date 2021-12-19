<?php

namespace Maci\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Payum\Core\Request\GetHumanStatus;
use Payum\Paypal\ExpressCheckout\Nvp\Api;

use Maci\MailerBundle\Entity\Mail;

use Maci\PageBundle\Entity\Order\Item;
use Maci\PageBundle\Entity\Order\Order;
use Maci\PageBundle\Entity\Order\Payment;
use Maci\PageBundle\Entity\Order\PaymentDetails;
use Maci\PageBundle\Form\Order\CartAddProductItemType;
use Maci\PageBundle\Form\Order\CartBillingAddressType;
use Maci\PageBundle\Form\Order\CartBookingType;
use Maci\PageBundle\Form\Order\CartCheckoutType;
use Maci\PageBundle\Form\Order\CartEditItemType;
use Maci\PageBundle\Form\Order\CartPickupType;
use Maci\PageBundle\Form\Order\CartRemoveItemType;
use Maci\PageBundle\Form\Order\CartShippingAddressType;
use Maci\PageBundle\Form\Order\MailType;
use Maci\PageBundle\Form\Order\CheckoutPaymentType;
use Maci\PageBundle\Form\Order\CheckoutShippingType;
use Maci\PageBundle\Form\Order\CheckoutConfirmType;


class OrderController extends AbstractController
{
	public function indexAction()
	{
		$list = $this->getDoctrine()->getManager()
			->getRepository('MaciPageBundle:Order\Order')
			->findBy(['user' => $this->getUser()], ['id' => 'DESC']);

		return $this->render('MaciPageBundle:Order:index.html.twig', array(
			'list' => $list
		));
	}

	public function confirmedAction()
	{
		$list = $this->getDoctrine()->getManager()
			->getRepository('MaciPageBundle:Order\Order')
			->getConfirmed();

		return $this->render('MaciPageBundle:Order:confirmed.html.twig', array(
			'list' => $list
		));
	}

	public function adminShowAction($id)
	{
		$order = $this->getDoctrine()->getManager()
			->getRepository('MaciPageBundle:Order\Order')
			->findOneById($id);

		return $this->render('MaciPageBundle:Order:show.html.twig', array(
			'order' => $order,
			'edit' => false
		));
	}

	public function userShowAction($id)
	{
		$order = $this->getDoctrine()->getManager()
			->getRepository('MaciPageBundle:Order\Order')
			->findOneBy(array('id'=>$id,'user'=>$this->getUser()));

		return $this->render('MaciPageBundle:Order:preview.html.twig', array(
			'order' => $order,
			'edit' => false
		));
	}

	public function cartAction()
	{
		return $this->render('MaciPageBundle:Order:cart.html.twig', array(
			'cart' => $this->get('maci.orders')->getCurrentCart()
		));
	}

	public function showOrderAction($order, $edit = false)
	{
		return $this->render('MaciPageBundle:Order:_show.html.twig', array(
			'order' => $order,
			'edit' => $edit
		));
	}

	public function notfoundAction()
	{
		return $this->render('MaciPageBundle:Order:notfound.html.twig');
	}

	public function addToCartAction(Request $request, $product, $variant = false)
	{
		if (is_numeric($product)) {
			$product = $this->getDoctrine()->getManager()
				->getRepository('MaciPageBundle:Shop\Product')
				->findOneById(intval($product));
			if (!$product) return $this->redirect($this->generateUrl('maci_order_cart', ['error' => 'error.notFound']));
		}

		if ($variant == false) $variant = $request->get('variant', false);

		$form = $this->createForm(CartAddProductItemType::class, null, array(
			'product' => $product,
			'variant' => $variant
		));

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			$item = intval($form['product']->getData());

			if (is_numeric($item)) {
				$item = $this->getDoctrine()->getManager()->getRepository('MaciPageBundle:Shop\Product')
					->findOneById(intval($item));
			}

			if (!is_object($item) || !$item->isAvailable()) {
				return $this->redirect($this->generateUrl('maci_order_cart', ['error' => 'error.notAvailable']));
			}

			$variant = false;
			$quantity = intval($form['quantity']->getData());
			if ($quantity < 1) $quantity = 1;

			if (isset($form['variant'])) {
				$index = $item->findVariant($form['variant']->getData());
				if ($index == -1) return $this->redirect($this->generateUrl('maci_order_cart', ['error' => 'error.notFound']));
				$variant = $product->getVariants()[$index];
				if ($variant['quantity'] == 0) return $this->redirect($this->generateUrl('maci_order_cart', ['error' => 'error.notAvailable']));
				if ($variant['quantity'] < $quantity) $quantity = $variant['quantity'];
			} else if (!$item->checkQuantity($quantity)) {
				return $this->redirect($this->generateUrl('maci_order_cart', ['error' => 'error.notAvailable']));
			}

			if ($this->get('maci.orders')->addToCart($item, $variant, $quantity) ) {
				return $this->redirect($this->generateUrl('maci_order_cart'));
			} else {
				return $this->redirect($this->generateUrl('maci_order_cart', ['error' => 'error.notAdded']));
			}

		}
		// else {
		// 	return $this->redirect($this->generateUrl('maci_product_show', ['path' => 'error' => 'error.formIsNotValid']));
		// }

		return $this->render('MaciPageBundle:Order:_order_cart_add_product.html.twig', array(
			'product' => $product,
			'variant' => $variant,
			'form' => $form->createView()
		));
	}

	public function editCartItemAction(Request $request, $id, $quantity = 1)
	{
		$form = $this->createForm(CartEditItemType::class);
		$form['quantity']->setData(intval($quantity));
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			if ( $this->get('maci.orders')->editItemQuantity(intval($id), intval($form['quantity']->getData()) ) ) {
				return $this->redirect($this->generateUrl('maci_order_cart', array('edited' => true)));
			} else {
				return $this->redirect($this->generateUrl('maci_order_cart', array('error' => true)));
			}
		}

		return $this->render('MaciPageBundle:Order:_order_cart_edit_item.html.twig', array(
			'id' => $id,
			'form' => $form->createView()
		));
	}

	public function removeCartItemAction(Request $request, $id)
	{
		$form = $this->createForm(CartRemoveItemType::class);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			if ( $this->get('maci.orders')->removeItem(intval($id)) ) {
				return $this->redirect($this->generateUrl('maci_order_cart', array('removed' => true)));
			} else {
				return $this->redirect($this->generateUrl('maci_order_cart', array('error' => true)));
			}
		}

		return $this->render('MaciPageBundle:Order:_order_cart_remove_item.html.twig', array(
			'id' => $id,
			'form' => $form->createView()
		));
	}

	public function cartGoCheckoutAction(Request $request, $option)
	{
		if (true === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
			return $this->redirect($this->generateUrl('maci_order_checkout'));
		}

		$edit = $request->get('edit', false);
		$cart = $this->get('maci.orders')->getCurrentCart();

		if ($cart->getMail() !== null && !$edit) {
			return $this->redirect($this->generateUrl('maci_order_checkout'));
		}

		return $this->render('MaciPageBundle:Order:gocheckout.html.twig');
	}

	public function cartCheckoutAction(Request $request)
	{
		$cart = $this->get('maci.orders')->getCurrentCart();

		if ( !$cart ) {
			return $this->redirect($this->generateUrl('maci_order_cart'));
		}

		if ($cart->getStatus() === 'complete' || $cart->getStatus() === 'confirm') {
			return $this->redirect($this->generateUrl('maci_order_invoice', array('id'=>$cart->getId())));
		}

		if ( $this->get('service_container')->getParameter('registration_required') && false === $this->get('security.authorization_checker')->isGranted('ROLE_USER') ) {
			return $this->redirect($this->generateUrl('maci_order_gocheckout'));
		}

		$checkout = array();

		// $type = $cart->getCheckout();
		// $type_array = $cart->getCheckoutArray();

		// if ( !$type || !in_array($type, $type_array) ) {
		//     return $this->redirect($this->generateUrl('maci_order_cart'));
		// }

		$edit = $request->get('checkout');
		$set = false;

		if ($cart->getBillingAddress() && $edit !== 'billingAddress') {
			$checkout['billingAddress'] = 'setted';
		} else {
			if ($set) {
				$checkout['billingAddress'] = 'toset';
			} else {
				$checkout['billingAddress'] = 'set';
				$set = true;
			}
		}

		if ($cart->checkShipment()) {

			if ($cart->getShippingAddress() && $edit !== 'shippingAddress') {
				$checkout['shippingAddress'] = 'setted';
			} else {
				if ($set) {
					$checkout['shippingAddress'] = 'toset';
				} else {
					$checkout['shippingAddress'] = 'set';
					$set = true;
				}
			}

			if ($cart->getShipping() && $edit !== 'shipping') {
				$checkout['shipping'] = 'setted';
			} else {
				if ($set) {
					$checkout['shipping'] = 'toset';
				} else {
					$checkout['shipping'] = 'set';
					$set = true;
				}
			}

		} else {
			$checkout['shippingAddress'] = false;
			$checkout['shipping'] = false;
		}

		if ($cart->getPayment() && $edit !== 'payment') {
			$checkout['payment'] = 'setted';
		} else {
			if ($set) {
				$checkout['payment'] = 'toset';
			} else {
				$checkout['payment'] = 'set';
				$set = true;
			}
		}

		if ($set) {
			$checkout['confirm'] = 'toset';
		} else {
			$checkout['confirm'] = 'set';
			$form = $this->createForm(CheckoutConfirmType::class, $cart);
			$checkout['confirm_form'] = $form->createView();
		}

		$this->get('maci.orders')->setCartLocale( $request->getLocale() );
		$this->get('maci.orders')->refreshCartAmount();

		return $this->render('MaciPageBundle:Order:checkout.html.twig', array(
			'checkout' => $checkout,
			'order' => $cart
		));
	}

	public function cartSetCheckoutAction(Request $request, $checkout = null)
	{
		$cart = $this->get('maci.orders')->getCurrentCart();

		if ($checkout === null || !in_array($checkout, $cart->getCheckoutArray())) {
			$checkout = 'checkout';
		}

		if ($checkout === 'pickup') {
			$form = CartPickupType::class;
		} else if ($checkout === 'booking') {
			$form = CartBookingType::class;
		} else {
			$form = CartCheckoutType::class;
		}

		$form = $this->createForm($form, $cart);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$this->get('maci.orders')->setCartCheckout( $checkout );
			$payment = $form['payment']->getData();
			$payments = $this->get('maci.orders')->getPaymentsArray();
			$this->get('maci.orders')->setCartPayment( $payment, $payments[$payment]['cost'] );
			if ( $form->has('shipping') ) {
				$this->get('maci.orders')->setCartShipping( $form['shipping']->getData() );
			}
			return $this->redirect($this->generateUrl('maci_order_gocheckout', array('setted' => 'checkout')));
		}

		return $this->render('MaciPageBundle:Order:_order_cart_checkout.html.twig', array(
			'checkout' => $checkout,
			'form' => $form->createView()
		));
	}

	public function cartSetMailAction(Request $request)
	{
		$cart = $this->get('maci.orders')->getCurrentCart();
		$form = $this->createForm(MailType::class, $cart);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$this->get('maci.orders')->setCartMail( $form['mail']->getData() );
			return $this->redirect($this->generateUrl('maci_order_gocheckout', array('setted' => 'mail')));
		}

		return $this->render('MaciPageBundle:Order:_order_mail.html.twig', array(
			'form' => $form->createView()
		));
	}

	public function cartSetPaymentAction(Request $request)
	{
		$cart = $this->get('maci.orders')->getCurrentCart();
		$form = $this->createForm(CheckoutPaymentType::class, $cart);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$payments = $this->get('maci.orders')->getPaymentsArray();
			$payment = $form['payment']->getData();
			$this->get('maci.orders')->setCartPayment( $payment, $payments[$payment]['cost'] );
			return $this->redirect($this->generateUrl('maci_order_gocheckout', array('setted' => 'payment')));
		}

		return $this->render('MaciPageBundle:Order:_order_checkout_payment.html.twig', array(
			'form' => $form->createView()
		));
	}

	public function cartSetShippingAction(Request $request)
	{
		$cart = $this->get('maci.orders')->getCurrentCart();
		$form = $this->createForm(CheckoutShippingType::class, $cart);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$this->get('maci.orders')->setCartShipping( $form['shipping']->getData() );
			return $this->redirect($this->generateUrl('maci_order_gocheckout', array('setted' => 'shipping')));
		}

		return $this->render('MaciPageBundle:Order:_order_checkout_shipping.html.twig', array(
			'form' => $form->createView()
		));
	}

	public function cartSetBillingAddressAction(Request $request)
	{
		$cart = $this->get('maci.orders')->getCurrentCart();
		$form = $this->createForm(CartBillingAddressType::class, $cart);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$address = $this->get('maci.addresses')->getAddress($form['billing_address']->getData());
			$this->get('maci.orders')->setCartBillingAddress($address);
			return $this->redirect($this->generateUrl('maci_order_gocheckout', array('setted' => 'billing')));
		}

		return $this->render('MaciPageBundle:Order:_order_billing_address.html.twig', array(
			'form' => $form->createView()
		));
	}

	public function cartSetShippingAddressAction(Request $request)
	{
		$cart = $this->get('maci.orders')->getCurrentCart();
		$form = $this->createForm(CartShippingAddressType::class, $cart);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$address = $this->get('maci.addresses')->getAddress($form['shipping_address']->getData());
			$this->get('maci.orders')->setCartShippingAddress($address);
			return $this->redirect($this->generateUrl('maci_order_gocheckout', array('setted' => 'shipping')));
		}

		return $this->render('MaciPageBundle:Order:_order_shipping_address.html.twig', array(
			'form' => $form->createView()
		));
	}

	public function checkoutConfirmAction(Request $request)
	{
		$cart = $this->get('maci.orders')->getCurrentCart();

		$form = $this->createForm(CheckoutConfirmType::class, $cart);
		$form->handleRequest($request);

		// if ($form->isSubmitted() && $form->isValid()) {
		//     return $this->redirect($this->generateUrl();
		// }

		$gatewayName = $this->get('maci.orders')->getCartPaymentGateway();

		if (!$cart->checkConfirmation() || !$gatewayName) {
			return $this->redirect($this->generateUrl('maci_order_checkout', array('error' => 'error.order_not_valid')));
		}

		$storage = $this->get('payum')->getStorage(Payment::class);
		
		if ($cart->getUser()) {
			$to = $cart->getUser()->getEmail();
			$toint = $cart->getUser()->getUsername();
		} else {
			$to = $cart->getMail();
			$toint = $cart->getBillingAddress()->getName() .' '. $cart->getBillingAddress()->getSurname();
		}

		$notifyToken = false;
		$payment = $storage->create();

		$payment->setNumber(uniqid());
		$payment->setCurrencyCode('EUR');
		$payment->setTotalAmount(intval($cart->getAmount() * 100)); // 1.23 EUR
		$payment->setDescription($cart->getCode());
		$payment->setClientId($toint);
		$payment->setClientEmail($to);

		$payment->setOrder($cart);
		
		$storage->update($payment);
		
		if(substr($gatewayName, 0, 6) === 'paypal') {

			$storageDetails = $this->get('payum')->getStorage(PaymentDetails::class);
			$paymentDetails = $storageDetails->create();

			$paymentDetails->setType('paypalExpress');

			$paymentDetails['PAYMENTREQUEST_0_CURRENCYCODE'] = 'EUR';
			$paymentDetails['PAYMENTREQUEST_0_AMT'] = $cart->getAmount();

			$paymentDetails['NOSHIPPING'] = Api::NOSHIPPING_NOT_DISPLAY_ADDRESS;
			$paymentDetails['REQCONFIRMSHIPPING'] = Api::REQCONFIRMSHIPPING_NOT_REQUIRED;
			$paymentDetails['L_PAYMENTREQUEST_0_ITEMCATEGORY0'] = Api::PAYMENTREQUEST_ITERMCATEGORY_DIGITAL;

			$paymentDetails['L_PAYMENTREQUEST_0_AMT0'] = $cart->getAmount();
			$paymentDetails['L_PAYMENTREQUEST_0_QTY0'] = 1;
			$paymentDetails['L_PAYMENTREQUEST_0_NAME0'] = $cart->getName();
			$paymentDetails['L_PAYMENTREQUEST_0_DESC0'] = $cart->getCode();

			$storageDetails->update($paymentDetails);

			$notifyToken = $this->get('payum')->getTokenFactory()->createNotifyToken($gatewayName, $paymentDetails);

			$captureToken = $this->get('payum')->getTokenFactory()->createCaptureToken(
				$gatewayName, 
				$paymentDetails, 
				'maci_order_payments_after_capture'
			);

			$paymentDetails['PAYMENTREQUEST_0_NOTIFYURL'] = $notifyToken->getTargetUrl();
			$paymentDetails['INVNUM'] = $paymentDetails->getId();

			$storageDetails->update($paymentDetails);

			$paymentDetails->setPayment($payment);

			$payment->setDetails($paymentDetails->getDetails());

			$storage->update($payment);
			
			return $this->redirect($captureToken->getTargetUrl());

		}

		$captureToken = $this->get('payum')->getTokenFactory()->createCaptureToken(
			$gatewayName, 
			$payment, 
			'maci_order_payments_after_capture'
		);
		
		return $this->redirect($captureToken->getTargetUrl());
	}

	public function afterCaptureAction(Request $request)
	{
		$token = $this->get('payum')->getHttpRequestVerifier()->verify($request);
		
		$gatewayName = $token->getGatewayName();
		$gateway = $this->get('payum')->getGateway($gatewayName);
		
		$gateway->execute($status = new GetHumanStatus($token));
		$payment = $status->getFirstModel();
		
		// Now you have order and payment status

		// if 
		// ACK!="Success"
		// then payment error, redirect...
		// or $status->getValue()=="canceled"
		// then redirect...

		// CHECKOUTSTATUS   "PaymentActionCompleted"
		// PAYMENTREQUEST_0_PAYMENTSTATUS   "Pending"
		// PAYMENTREQUEST_0_TRANSACTIONID   "1XX96663P5687610F"

		$params = array(
			'status' => $status->getValue(),
			'payment' => $payment->getDetails()
		);

		return new JsonResponse($params);

		$em = $this->getDoctrine()->getManager();
	
		$cart = $payment->getOrder();
		if (!$cart) {
			return $this->redirect($this->generateUrl('maci_order_cart', array('error' => 'error.order_not_found')));
		}

		$params = array(
			'status' => $status->getValue(),
			'payment' => array(
				'total_amount' => $payment->getTotalAmount(),
				'currency_code' => $payment->getCurrencyCode(),
				'details' => $payment->getDetails(),
			)
		);

		return new JsonResponse($params);

		if($status->getValue() === "failed") {
			return $this->redirect($this->generateUrl('maci_order_checkout', array('error' => 'error.payment_not_valid')));
		}

		$payment_item = $this->get('maci.orders')->getPaymentItem($cart->getPayment());
		$gateway = $payment_item['gateway'];

		if($gateway == 'offline' && $status->getValue() == 'captured') {
			$cart->confirmOrder();
		}

		// Recipient

		$to = $payment->getClientEmail();
		$toint = $payment->getClientId();

		// Create Mail

		$mail = new Mail();

		$mail
			->setName($cart->getCode())
			->setType('notify')
			->setSubject('Order Confirmation')
			->setSender($this->get('service_container')->getParameter('server_email'), $this->get('service_container')->getParameter('server_email_int'))
			->addTo([$to => $toint])
			->setLocale($request->getLocale())
			->setContent($this->renderView('MaciPageBundle:Email:confirmation_email.html.twig', array('mail' => $mail, 'order' => $cart)))
		;

		if ($cart->getUser()) {
			$mail->setUser($cart->getUser());
		}

		$message = $this->get('maci.mailer')->getSwiftMessage($mail);

		$notify = clone $message;

		$mail->end();

		$em->persist($mail);

		$em->flush();

		// Send Mail

		// ---> send message
		if ($this->container->get('kernel')->getEnvironment() == "prod") $this->get('mailer')->send($message);

		$notify->addTo($this->get('service_container')->getParameter('order_email'));

		// ---> send notify
		if ($this->container->get('kernel')->getEnvironment() == "prod") $this->get('mailer')->send($notify);

		return $this->redirect($this->generateUrl('maci_order_checkout_complete', ['token' => $cart->getToken()]));

	}

	public function checkoutCompleteAction(Request $request, $token)
	{
		$order = $em->getDoctrine()->getManager()->getRepository('MaciPageBundle:Order\Order')->findOneByToken($token);

		$page = $em->getRepository('MaciPageBundle:Page')
			->findOneByPath('order-complete');

		if ($page) {
			return $this->render($page->getTemplate(), $order);
		}

		return $this->render('MaciPageBundle:Order:complete.html.twig', $order);
	}

	public function invoiceAction($id)
	{
		$order = $this->getDoctrine()->getManager()
			->getRepository('MaciPageBundle:Order\Order')
			->findOneById($id);

		if (!$order) {
			return $this->redirect($this->generateUrl('maci_order_notfound'));
		}

		if (
			( $order->getUser() && $order->getUser()->getId() !== $this->getUser()->getId() ) &&
			false === $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')
		) {
			return $this->redirect($this->generateUrl('maci_order_homepage', array('error' => 'order.nomap')));
		}

		return $this->render('MaciPageBundle:Order:invoice.html.twig', array(
			'order' => $order
		));
	}

	// public function paypalCompleteAction()
	// {
	//     $om = $this->getDoctrine()->getManager();

	//     $id = $tx = $this->getRequest()->get('cm');

	//     $order = $om->getRepository('MaciPageBundle:Order\Order')
	//         ->findOneById($id);

	//     $tx = $this->getRequest()->get('tx');

	//     if (!$order || !$tx) {
	//         return $this->redirect($this->generateUrl('maci_order_notfound'));
	//     }

	//     $pdt = $this->get('orderly_pay_pal_pdt');
	//     $pdtArray = $pdt->getPdt($tx);

	//     $status = 'unknown';

	//     if (isset($pdtArray['payment_status'])) {
	//         $status = $pdtArray['payment_status'];
	//     } else if ($this->getRequest()->get('st')) {
	//         $status = $this->getRequest()->get('st');
	//     }

	//     $page = $this->getDoctrine()->getManager()
	//         ->getRepository('MaciPageBundle:Page')
	//         ->findOneByPath('order-complete-paypal');

	//     if ($page) {
	//         return $this->redirect($this->generateUrl('maci_page', array('path' => 'order-complete-paypal')));
	//     }

	//     $page = $this->getDoctrine()->getManager()
	//         ->getRepository('MaciPageBundle:Page')
	//         ->findOneByPath('order-complete');

	//     if ($page) {
	//         return $this->redirect($this->generateUrl('maci_page', array('path' => 'order-complete')));
	//     }

	//     return $this->redirect($this->generateUrl('maci_order_checkout_complete'));
	// }

	// public function paypalForm($order)
	// {
	//     $form = $this->createFormBuilder($order);

	//     if ($this->get('service_container')->getParameter('shop_is_live')) {

	//         $form = $form->setAction('https://www.paypal.com/cgi-bin/webscr')
	//             ->add('business', 'hidden', array('mapped' => false, 'data' => $this->get('service_container')->getParameter('MaciPage_paypalform_business')));

	//     } else {

	//         $form = $form->setAction('https://sandbox.paypal.com/cgi-bin/webscr')
	//             ->add('business', 'hidden', array('mapped' => false, 'data' => $this->get('service_container')->getParameter('MaciPage_paypalform_business_fac')));

	//     }

	//     $form = $form->add('cmd', 'hidden', array('mapped' => false, 'data' => '_xclick'))
	//         ->add('lc', 'hidden', array('mapped' => false, 'data' => 'IT'))
	//         ->add('item_name', 'hidden', array('mapped' => false, 'data' => $order->getName()))
	//         ->add('item_number', 'hidden', array('mapped' => false, 'data' => 1))
	//         ->add('custom', 'hidden', array('mapped' => false, 'data' => $order->getId()))
	//         ->add('amount', 'hidden', array('mapped' => false, 'data' => number_format($order->getAmount(), 2, '.', '')))
	//         ->add('currency_code', 'hidden', array('mapped' => false, 'data' => 'EUR'))
	//         ->add('button_subtype', 'hidden', array('mapped' => false, 'data' => 'services'))
	//         ->add('no_note', 'hidden', array('mapped' => false, 'data' => '1'))
	//         ->add('no_shipping', 'hidden', array('mapped' => false, 'data' => '1'))
	//         ->add('rm', 'hidden', array('mapped' => false, 'data' => '1'))
	//         ->add('return', 'hidden', array('mapped' => false, 'data' => $this->generateUrl('maci_order_paypal_complete', array(), true)))
	//         ->add('cancel_return', 'hidden', array('mapped' => false, 'data' => $this->generateUrl('maci_order', array(), true)))
	//         ->add('notify_url', 'hidden', array('mapped' => false, 'data' => $this->generateUrl('maci_paypal_ipn', array(), true)))
	//         ->getForm();

	//     return $this->render('MaciPageBundle:Order:_paypal.html.twig', array(
	//         'form' => $form->createView(),
	//     ));
	// }
}

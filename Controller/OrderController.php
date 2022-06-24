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

		return $this->render('MaciPageBundle:Order:index.html.twig', [
			'list' => $list
		]);
	}

	public function adminShowAction($id)
	{
		$order = $this->getDoctrine()->getManager()
			->getRepository('MaciPageBundle:Order\Order')
			->findOneById($id);

		return $this->render('MaciPageBundle:Order:admin_show.html.twig', [
			'order' => $order,
			'edit' => false
		]);
	}

	public function userShowAction($id)
	{
		$order = $this->getDoctrine()->getManager()
			->getRepository('MaciPageBundle:Order\Order')
			->findOneBy(array('id'=>$id,'user'=>$this->getUser()));

		return $this->render('MaciPageBundle:Order:preview.html.twig', [
			'order' => $order,
			'edit' => false
		]);
	}

	public function cartAction()
	{
		return $this->render('MaciPageBundle:Order:cart.html.twig', [
			'cart' => $this->get('maci.orders')->getCurrentCart()
		]);
	}

	public function showOrderAction($order, $edit = false)
	{
		return $this->render('MaciPageBundle:Order:_show.html.twig', [
			'order' => $order,
			'edit' => $edit
		]);
	}

	public function notfoundAction()
	{
		return $this->render('MaciPageBundle:Order:notfound.html.twig');
	}

	public function addToCartAction(Request $request, $product, $variant = false)
	{
		if (is_numeric($product))
		{
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

			if (is_numeric($item))
				$item = $this->getDoctrine()->getManager()
					->getRepository('MaciPageBundle:Shop\Product')
					->findOneById(intval($item));

			if (!is_object($item) || !$item->isAvailable())
				return $this->redirect($this->generateUrl('maci_order_cart', ['error' => 'error.notAvailable']));

			$variant = false;
			$variants = $product->getVariants();
			$quantity = intval($form['quantity']->getData());
			if ($quantity < 1) $quantity = 1;

			if (count($variants) && isset($form['variant']))
			{
				$index = $item->findVariant($form['variant']->getData());
				if ($index == -1) return $this->redirect($this->generateUrl('maci_order_cart', ['error' => 'error.notFound']));
				$variant = $item->getVariantIndex($index);
				$variantQta = intval($variant['quantity']);
				if ($variantQta == 0) return $this->redirect($this->generateUrl('maci_order_cart', ['error' => 'error.notAvailable']));
				if ($variantQta < $quantity) $quantity = $variantQta;
			}

			if ($this->get('maci.orders')->addToCart($item, $quantity, $variant))
				return $this->redirect($this->generateUrl('maci_order_cart'));
			else
				return $this->redirect($this->generateUrl('maci_order_cart', ['error' => 'error.notAvailable']));

		}
		// else {
		//  return $this->redirect($this->generateUrl('maci_product_show', ['path' => 'error' => 'error.formIsNotValid']));
		// }

		return $this->render('MaciPageBundle:Order:_order_cart_add_product.html.twig', [
			'product' => $product,
			'variant' => $variant,
			'form' => $form->createView()
		]);
	}

	public function editCartItemAction(Request $request, $id, $quantity = 1)
	{
		$form = $this->createForm(CartEditItemType::class);
		$form['quantity']->setData(intval($quantity));
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			if ($this->get('maci.orders')->editItemQuantity(intval($id), intval($form['quantity']->getData())))
				return $this->redirect($this->generateUrl('maci_order_cart', array('edited' => true)));
			else
				return $this->redirect($this->generateUrl('maci_order_cart', array('error' => true)));
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

		if ($form->isSubmitted() && $form->isValid())
		{
			if ($this->get('maci.orders')->removeItem(intval($id)))
				return $this->redirect($this->generateUrl('maci_order_cart', array('removed' => true)));
			else
				return $this->redirect($this->generateUrl('maci_order_cart', array('error' => true)));
		}

		return $this->render('MaciPageBundle:Order:_order_cart_remove_item.html.twig', array(
			'id' => $id,
			'form' => $form->createView()
		));
	}

	public function cartStartCheckoutAction(Request $request, $type)
	{
		if (!is_string($type) || !in_array($type, Order::getCheckoutArray()))
			$type = 'checkout';

		$this->get('maci.orders')->startCartCheckout($type);

		return $this->redirect($this->generateUrl('maci_order_gocheckout'));
	}

	public function cartGoCheckoutAction(Request $request)
	{
		if (true === $this->get('security.authorization_checker')->isGranted('ROLE_USER'))
			return $this->redirect($this->generateUrl('maci_order_checkout'));

		$edit = $request->get('edit', false);
		$cart = $this->get('maci.orders')->getCurrentCart();

		if ($cart->getMail() !== null && !$edit)
			return $this->redirect($this->generateUrl('maci_order_checkout'));

		return $this->render('MaciPageBundle:Order:gocheckout.html.twig');
	}

	public function cartCheckoutAction(Request $request)
	{
		$orders = $this->get('maci.orders');
		$cart = $orders->getCurrentCart();

		if (!$cart)
			return $this->redirect($this->generateUrl('maci_order_cart'));

		if ($cart->getStatus() === 'complete' || $cart->getStatus() === 'confirm')
			return $this->redirect($this->generateUrl('maci_order_invoice', ['id' => $cart->getId()]));

		if ($this->get('service_container')->getParameter('registration_required') && false === $this->get('security.authorization_checker')->isGranted('ROLE_USER'))
			return $this->redirect($this->generateUrl('maci_order_gocheckout'));

		$checkout = $cart->getCheckoutParameters($request->get('checkout'));

		if (!is_array($checkout))
			return $this->redirect($this->generateUrl('maci_order_cart'));

		// $payment = $orders->getCartPaymentItem();
		// if ($payment && $payment['shipping'] && $cart->checkShipment()) {}

		if ($checkout['confirm_form'])
			$checkout['confirm_form'] = $this->createForm(CheckoutConfirmType::class, [], [
				'action' => $this->generateUrl('maci_order_checkout_confirm'),
				'status' => $cart->getStatus(),
				'env' => $this->get('kernel')->getEnvironment()
			])->createView();

		$orders->refreshCartAmount();

		return $this->render('MaciPageBundle:Order:checkout_light.html.twig', $checkout);
	}

	public function cartSetMailAction(Request $request)
	{
		$cart = $this->get('maci.orders')->getCurrentCart();
		$form = $this->createForm(MailType::class, $cart);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$this->get('maci.orders')->setCartMail($form['mail']->getData());
			return $this->redirect($this->generateUrl('maci_order_checkout', ['setted' => 'mail']));
		}

		return $this->render('MaciPageBundle:Order:_order_mail.html.twig', [
			'form' => $form->createView()
		]);
	}

	public function cartSetPaymentAction(Request $request)
	{
		$cart = $this->get('maci.orders')->getCurrentCart();
		$form = $this->createForm(CheckoutPaymentType::class, $cart);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$payments = $this->get('maci.orders')->getPaymentsArray();
			$payment = $form['payment']->getData();
			$this->get('maci.orders')->setCartPayment($payment, $payments[$payment]['cost']);
			return $this->redirect($this->generateUrl('maci_order_checkout', array('setted' => 'payment')));
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

		if ($form->isSubmitted() && $form->isValid())
		{
			$this->get('maci.orders')->setCartShipping($form['shipping']->getData());
			return $this->redirect($this->generateUrl('maci_order_checkout', array('setted' => 'shipping')));
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

		if ($form->isSubmitted() && $form->isValid())
		{
			$address = $this->get('maci.addresses')->getAddress($form['billing_address']->getData());
			$this->get('maci.orders')->setCartBillingAddress($address);
			return $this->redirect($this->generateUrl('maci_order_checkout', ['setted' => 'billing']));
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

		if ($form->isSubmitted() && $form->isValid())
		{
			$address = $this->get('maci.addresses')->getAddress($form['shipping_address']->getData());
			$this->get('maci.orders')->setCartShippingAddress($address);
			return $this->redirect($this->generateUrl('maci_order_gocheckout', ['setted' => 'shipping']));
		}

		return $this->render('MaciPageBundle:Order:_order_shipping_address.html.twig', [
			'form' => $form->createView()
		]);
	}

	public function checkoutConfirmAction(Request $request)
	{
		$cart = $this->get('maci.orders')->getCurrentCart();

		$form = $this->createForm(CheckoutConfirmType::class, [], [
			'env' => $this->get('kernel')->getEnvironment()
		]);

		$form->handleRequest($request);

		if (!$form->isSubmitted() && !$form->isValid())
			return $this->redirect($this->generateUrl('maci_order_gocheckout'));

		$gatewayName = $this->get('maci.orders')->getCartPaymentGateway();

		if (!$cart->checkConfirmation() || !$gatewayName)
			return $this->redirect($this->generateUrl('maci_order_checkout', ['error' => 'error.order_not_valid']));

		$storage = $this->get('payum')->getStorage(Payment::class);

		if ($cart->getUser())
		{
			$to = $cart->getUser()->getEmail();
			$toint = $cart->getUser()->getUsername();
		} else
		{
			$to = $cart->getMail();
			$toint = $cart->getBillingAddress()->getName() . ' ' .
				$cart->getBillingAddress()->getSurname();
		}

		$om = $this->getDoctrine()->getManager();
		$notifyToken = false;
		$payment = $storage->create();

		$payment->setNumber(uniqid());
		$payment->setCurrencyCode('EUR');
		$payment->setTotalAmount(intval($cart->getAmount() * 100)); // 1.23 EUR
		$payment->setDescription($cart->getCode());
		$payment->setClientId($toint);
		$payment->setClientEmail($to);

		$payment->setOrder($cart);

		$this->get('maci.orders')->setCartLocale();

		if ($cart->getStatus() == 'session')
		{
			$cart->setStatus('current');
			$om->persist($cart);
		}

		$om->flush();

		$storage->update($payment);
		
		if(substr($gatewayName, 0, 6) === 'paypal')
			return $this->capturePayPal($cart, $payment);

		$captureToken = $this->get('payum')->getTokenFactory()->createCaptureToken(
			$gatewayName, $payment, 'maci_order_payments_after_capture'
		);

		return $this->redirect($captureToken->getTargetUrl());
	}

	public function capturePayPal($cart, $payment)
	{
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

		$gatewayName = $this->get('maci.orders')->getCartPaymentGateway();

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

	public function afterCaptureAction(Request $request)
	{
		$token = $this->get('payum')->getHttpRequestVerifier()->verify($request);
		$gatewayName = $token->getGatewayName();
		$gateway = $this->get('payum')->getGateway($gatewayName);
		$gateway->execute($status = new GetHumanStatus($token));

		if($status->getValue() === "failed")
		{
			$this->get('session')->getFlashBag()->add('danger', 'error.payment_not_valid');
			return $this->redirect($this->generateUrl('maci_order_checkout'));
		}

		if($status->getValue() === "canceled")
		{
			$this->get('session')->getFlashBag()->add('info', 'error.payment_canceled');
			return $this->redirect($this->generateUrl('maci_order_checkout'));
		}

		// Now you have order and payment status

		// if 
		// ACK!="Success"
		// then payment error, redirect...
		// or $status->getValue()=="canceled"
		// then redirect...

		// CHECKOUTSTATUS   "PaymentActionCompleted"
		// PAYMENTREQUEST_0_PAYMENTSTATUS   "Pending"
		// PAYMENTREQUEST_0_TRANSACTIONID   "1XX96663P5687610F"

		$payment = $status->getFirstModel();
		$cart = $payment->getOrder();

		if (!$cart) {
			$this->get('session')->getFlashBag()->add('danger', 'error.order_not_found');
			return $this->redirect($this->generateUrl('maci_product'));
		}

		$payment_item = $this->get('maci.orders')->getPaymentItem($cart->getPayment());

		$params = [
			'status' => $status->getValue(),
			'id' => $cart->getPayment(),
			'label' => $payment_item['label'],
			'gateway' => $payment_item['gateway'],
			'sandbox' => $payment_item['sandbox'],
			'payment' => [
				'total_amount' => $payment->getTotalAmount(),
				'currency_code' => $payment->getCurrencyCode(),
				'details' => $payment->getDetails(),
			]
		];

		// return new JsonResponse($params);

		if($payment_item['gateway'] == 'offline')
			$params['payment']['details']['paid'] = false;

		if($status->getValue() == 'captured')
			$cart->confirmOrder($params);

		$this->get('maci.orders')->resetCart();

		$this->sendNotify($cart, [$payment->getClientEmail() => $payment->getClientId()]);

		return $this->redirect($this->generateUrl('maci_order_checkout_complete', ['token' => $cart->getToken()]));
	}

	public function sendNotify($cart, $recipients = false, $template = false)
	{
		$mail = new Mail();
		$mail
			->setName($cart->getCode())
			->setType('notify')
			->setSubject('Order Confirmation')
			->setSender($this->get('service_container')->getParameter('server_email'), $this->get('service_container')->getParameter('server_email_int'))
			->addRecipients($recipients ? $recipients : $cart->getRecipient())
			->setLocale($cart->getLocale())
			->setContent($this->renderView($template ? $template : '@MaciPage/Email/order_confirmed.html.twig', ['mail' => $mail, 'order' => $cart]))
		;

		if ($cart->getUser())
		{
			if (!$cart->getMail()) $cart->setMail($cart->getUser()->getEmail());
			$cart->setDescription('Order Placed by ' . $cart->getUser()->getUsername() . '.');
			$mail->setUser($cart->getUser());
		}

		// $message = $this->get('maci.mailer')->getSwiftMessage($mail);
		$message = $mail->getSwiftMessage($mail);
		$notify = clone $message;
		// $mail->end();

		$om = $this->getDoctrine()->getManager();
		$om->persist($mail);
		$om->flush();

		// Send Mail

		// ---> send message
		if ($this->container->get('kernel')->getEnvironment() == "prod")
			$this->get('mailer')->send($message);

		$notify->addTo($this->get('service_container')->getParameter('order_email'), $this->get('service_container')->getParameter('order_email_int'));

		// ---> send notify
		if ($this->container->get('kernel')->getEnvironment() == "prod")
			$this->get('mailer')->send($notify);
	}

	public function checkoutCompleteAction(Request $request, $token)
	{
		$em = $this->getDoctrine()->getManager();
		$order = $em->getRepository('MaciPageBundle:Order\Order')->findOneByToken($token);
		$page = $em->getRepository('MaciPageBundle:Page\Page')
			->findOneByPath('order-complete');

		if ($page)
			return $this->render($page->getTemplate(), $order);

		return $this->render('@MaciPage/Order/complete.html.twig', [
			'order' => $order
		]);
	}

	public function invoiceAction($id)
	{
		$order = $this->getDoctrine()->getManager()
			->getRepository('MaciPageBundle:Order\Order')
			->findOneById($id);

		if (!$order)
			return $this->redirect($this->generateUrl('maci_order_notfound'));

		if (
			( $order->getUser() && $order->getUser()->getId() !== $this->getUser()->getId() ) &&
			false === $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')
		) return $this->redirect($this->generateUrl('maci_order_homepage', array('error' => 'order.nomap')));

		return $this->render('MaciPageBundle:Order:invoice.html.twig', [
			'order' => $order
		]);
	}

	public function confirmedAction()
	{
		$admin = $this->container->get(\Maci\AdminBundle\Controller\AdminController::class);
		if (!$admin->checkAuth()) {
			return new JsonResponse(['success' => false, 'error' => 'Not Authorized.'], 200);
		}

		$list = $this->getDoctrine()->getManager()
			->getRepository('MaciPageBundle:Order\Order')
			->getConfirmed();

		return $this->render('MaciPageBundle:Order:confirmed.html.twig', [
			'list' => $list
		]);
	}

	public function orderManagerAction(Request $request, $id)
	{
		// --- Check Request

		if (!$request->isXmlHttpRequest())
			return $this->redirect($this->generateUrl('homepage'));

		if ($request->getMethod() !== 'POST')
			return new JsonResponse(['success' => false, 'error' => 'Bad Request.'], 200);

		// --- Check Auth

		$admin = $this->container->get(\Maci\AdminBundle\Controller\AdminController::class);
		if (!$admin->checkAuth())
			return new JsonResponse(['success' => false, 'error' => 'Not Authorized.'], 200);

		$order = $this->getDoctrine()->getManager()
			->getRepository('MaciPageBundle:Order\Order')
			->findOneById($id);

		if (!$order)
			return new JsonResponse(['success' => false, 'error' => 'Order Not Found.'], 200);

		$cmd = $request->get('cmd');

		if (!$cmd)
			return new JsonResponse(['success' => false, 'error' => 'Command Missing.'], 200);

		if ($cmd == 'completeOrder')
			return new JsonResponse($this->completeOrder($order), 200);

		return new JsonResponse(['success' => false, 'error' => 'Nothing Done.'], 200);
	}

	public function completeOrder($order)
	{
		$order->completeOrder();

		$om = $this->getDoctrine()->getManager();
		$om->flush();

		return ['success' => true, 'msg' => 'completeOrder'];
	}

	public function sendShippedNotify($order)
	{
		$this->sendNotify($order, false, '@MaciPage/Email/order_shipped.html.twig');

		return ['success' => true, 'msg' => 'completeOrder'];
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

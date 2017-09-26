<?php

namespace App\Controllers;

use Slim\Views\Twig as View;
use Slim\Router;


class CheckOutController extends Controller
{


    public function index($request, $response)
    {
      return $this->view->render($response, 'checkout/index.twig');
    }

    public function checkout($request, $response)
    {
      //die('here');
        $enteredAmount = $request->getParam('amount');
        $amount = $enteredAmount * 100;
       //Stripe::setApiKey(getenv('secret_key'));
        return $this->view->render($response, 'checkout/checkout.twig', $arg = [
          'publishable_key' => getenv('publishable_key'),
          'action'          =>  'checkout/charge',
          'amount'          =>  $amount
        ]);
    }

    public function charge($request, $response)
    {

        //One time payment
        \Stripe\Stripe::setApiKey(getenv('secret_key'));
        $token  = $request->getParam('stripeToken');
        $email  = $request->getParam('stripeEmail');

        $stripeinfo = \Stripe\Token::retrieve($token);
        $email = $stripeinfo->email;


        $charge = \Stripe\Charge::create(array(
          'customer' => $customer->id,
          'amount'   => 500,
          'currency' => 'usd',
          'description' => 'Service Market Place charge',
          'receipt_email' => $email,
          'source'  => $token
        ));

        echo '<h1>Successfully charged $50.00!</h1>';
    }

    public function chargeCustomer($request, $response)
    {
        // this for charging recurring sustomer

        \Stripe\Stripe::setApiKey(getenv('secret_key'));
        $token  = $request->getParam('stripeToken');
        $email  = $request->getParam('stripeEmail');

        $stripeinfo = \Stripe\Token::retrieve($token);
        $email = $stripeinfo->email;

        $customer = \Stripe\Customer::create(array(
          'email' => $email,
          'source'  => $token,
          'description' => 'Service Market Place charge'
        ));

        $charge = \Stripe\Charge::create(array(
          'customer' => $customer->id,
          'amount'   => 500,
          'currency' => 'usd'
        ));

        echo '<h1>Successfully charged $50.00!</h1>';
    }
}

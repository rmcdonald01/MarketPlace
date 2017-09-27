<?php

namespace App\Controllers;

use Slim\Views\Twig as View;
use Slim\Router;
use Respect\Validation\Validator as v;


class CheckOutController extends Controller
{

    protected $amount;

    public function index($request, $response)
    {
      return $this->view->render($response, 'checkout/index.twig');
    }

    public function checkout($request, $response)
    {

        $validation = $this->validator->validate($request, [
            'amount' => v::notEmpty()->numeric()->positive()->minPayment(),
        ]);

        if ($validation->failed()) {
          
            return $response->withRedirect($this->router->pathFor('checkout.index'));
        }
$enteredAmount = $request->getParam('amount');
        $this->amount = $enteredAmount * 100;
        $_SESSION['AMOUNT'] = $this->amount;
        return $this->view->render($response, 'checkout/checkout.twig', $arg = [
          'publishable_key' => getenv('publishable_key'),
          'action'          =>  'checkout/charge',
          'amount'          =>  $this->amount
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
          'amount'   => $_SESSION['AMOUNT'],
          'currency' => 'usd',
          'description' => 'Service Market Place charge',
          'receipt_email' => $email,
          'source'  => $token
        ));

        //$charge->paid
        if ($charge->status == 'succeeded') {
          $this->flash->addMessage('dissmissable-success', 'Charge of $'. $_SESSION['AMOUNT'] / 100 .' was successful made!');
          return $response->withRedirect($this->router->pathFor('home'));
      }else {
        $this->flash->addMessage('error', '<h1>Charged of $'. $_SESSION['AMOUNT'] / 100 .' failed!</h1>');
        return $response->withRedirect($this->router->pathFor('home'));
      }


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
          'amount'   => $this->$amount,
          'currency' => 'usd'
        ));

        echo '<h1>Successfully charged'.$this->$amount.'!</h1>';
    }
}

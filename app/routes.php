<?php

use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;


$app->get('/', 'HomeController:index')->setName('home');

$app->post('/internal-login', 'AuthController:postInternalLogin')->setName('internal-login');

//----------------------------------------- Checking Out ---------------------------------
$app->get('/checkout/index', 'CheckOutController:index')->setName('checkout.index');
$app->post('/checkout', 'CheckOutController:checkout')->setName('checkout');
$app->post('/checkout/charge', 'CheckOutController:charge')->setName('checkout.charge');


$app->group('', function () {
    //$this->get('/auth/signup', 'AuthController:getSignUp')->setName('auth.signup');

    $this->get('/auth/signin', 'AuthController:getSignIn')->setName('auth.signin');
    $this->post('/auth/signin', 'AuthController:postSignIn');

    //$this->get('/checkout', 'CheckOutController:index')->setName('checkout.index');

})->add(new GuestMiddleware($container));

  $app->get('/dcx/list', 'DcxAPIController:getDcxExams')->setName('dcx.exams');

$app->group('', function () {

    $this->get('/auth/signup', 'AuthController:getSignUp')->setName('auth.signup');
    $this->post('/auth/signup', 'AuthController:postSignUp')->setName('auth.signup');

    $this->get('/auth/signout', 'AuthController:getSignOut')->setName('auth.signout');

    $this->get('/auth/password/change', 'PasswordController:getChangePassword')->setName('auth.password.change');
    $this->post('/auth/password/change', 'PasswordController:postChangePassword');



    // Pulling a single call count
   //$this->post('/sinlge-list-call-count','SingleListSetList:pullListCallCount')->setName('fetch-call-count-in-list');



})->add(new AuthMiddleware($container));

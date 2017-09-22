<?php

namespace App\Controllers\Auth;

use App\Models\User;
use App\Controllers\Controller;
use Respect\Validation\Validator as v;

class AuthController extends Controller
{
    public function getSignOut($request, $response)
    {
        $this->auth->logout();

        return $response->withRedirect($this->router->pathFor('home'));
    }

    public function getSignIn($request, $response)
    {
        return $this->view->render($response, 'auth/signin.twig');
    }

    public function postSignIn($request, $response)
    {
        $auth = $this->auth->attempt(
            $request->getParam('username'),
            $request->getParam('password')
        );

        if (!$auth) {
            $this->flash->addMessage('error', 'Could not sign you in with those details.');
            return $response->withRedirect($this->router->pathFor('auth.signin'));
        }

        return $response->withRedirect($this->router->pathFor('home'));
    }

    public function getSignUp($request, $response)
    {
        return $this->view->render($response, 'auth/signup.twig');
    }

    public function postSignUp($request, $response)
    {
        $validation = $this->validator->validate($request, [
            'fname' => v::notEmpty()->alpha(),
            'lname' => v::notEmpty()->alpha(),
            'email' => v::noWhitespace()->notEmpty()->email()->emailAvailable(),
            'username' => v::notEmpty(),
            'usertype' => v::notEmpty(),
            'password' => v::noWhitespace()->notEmpty(),
        ]);

        if ($validation->failed()) {
            return $response->withRedirect($this->router->pathFor('auth.signup'));
        }

        $user = User::create([
            'fname' => $request->getParam('fname'),
            'lname' => $request->getParam('lname'),
            'email' => $request->getParam('email'),
            'username' => $request->getParam('username'),
            'usertype' => $request->getParam('usertype'),
            'password' => password_hash($request->getParam('password'), PASSWORD_DEFAULT),
        ]);

        $this->flash->addMessage('info', 'User '. $request->getParam('username') .' created!');

        //$this->auth->attempt($user->email, $request->getParam('password'));

        return $response->withRedirect($this->router->pathFor('home'));
    }

    public function postInternalLogin($request, $response)
    {
      $username = $request->getParam('username');

      $auth = $this->auth->attempt_internal_login(
          $username
      );

      if (!$auth) {
          $this->flash->addMessage('error', 'Could not sign you in with those details.');
          return $response->withRedirect($this->router->pathFor('auth.signin'));
      }

      return $response->withRedirect($this->router->pathFor('home'));
    }
}

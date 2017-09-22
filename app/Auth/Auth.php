<?php

namespace App\Auth;

use App\Models\User;
use App\Models\Employee as Employee;
use App\Helpers\InternalLoginEncryption;

class Auth
{
    public function user()
    {
        if ($_SESSION['entry_type']) {

          return User::where('username', '=', $_SESSION['user'])->select('fname', 'lname', 'usertype', 'username')->first();

        }
        //For integration with Employee table
        //return Employee::where('username', '=', $_SESSION['user'])->select('fname', 'lname', 'usertype')->first();
        return User::find($_SESSION['user']);
    }

    public function check()
    {
        return isset($_SESSION['user']);
    }

    public function isAdmin()
    {
      //For integration with Employee table
      // return Employee::where('username', '=', $_SESSION['user'])
      //                 ->where('usertype', '=', 'Admin')
      //                 ->select('fname', 'lname', 'usertype')->first();
      return User::where('username', '=', $_SESSION['user'])
                      ->where('usertype', '=', 'Admin')
                      ->select('fname', 'lname', 'usertype')->first();
    }

    public function isSupperAdmin()
    {
      return User::where('username', '=', $_SESSION['user'])
                      ->where('usertype', '=', 'superAdmin')
                      ->select('fname', 'lname', 'usertype')->first();
    }


    public function attempt_internal_login($user)
    {

        $user = InternalLoginEncryption::Decrypt($user);

        $user = Employee::where('username', '=', trim($user))->first();


        if (!$user) {
            return false;
        }

        if ($user) {
            $_SESSION['user'] = $user->username;
            return true;
        }

        return false;
    }

    public function attempt($username, $password)
    {

        $user = User::where('username', $username)->first();

        if (!$user) {
            return false;
        }

        if (password_verify($password, $user->password)) {
            $_SESSION['user'] = $user->username;
            $_SESSION['entry_type'] = 'external';
            return true;
        }

        return false;
    }

    public function logout()
    {
        unset($_SESSION['user']);
    }
}

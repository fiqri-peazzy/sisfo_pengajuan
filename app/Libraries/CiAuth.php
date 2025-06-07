<?php

namespace App\Libraries;

use App\Models\User;
use PhpParser\Node\Expr\New_;

class CiAuth
{
    public static function setCiAuth($result)
    {
        $session = session();
        $array = ['logged_in' => true];
        $userData = $result;
        $session->set('userData', $userData);
        $session->set($array);
    }

    public static function id()
    {
        $session = session();
        if ($session->has('logged_in')) {
            if ($session->has('userData')) {
                return $session->get('userData')['id'];
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public static function check()
    {
        $session = session();
        return $session->has('logged_in');
    }

    public static function forget()
    {
        $session = session();
        $session->remove('logged_in');
        $session->remove('userData');
    }

    public static function user()
    {
        $session = session();
        if ($session->has('logged_in')) {
            if ($session->has('userData')) {
                // return $session->get('userData');
                $user = new User;
                return $user->asObject()->where('id', CiAuth::id())->first();
            } else {
                return null;
            }
        } else {
            return null;
        }
    }
}
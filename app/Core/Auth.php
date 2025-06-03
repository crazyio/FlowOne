<?php
namespace App\Core;

// Basic Auth class structure
class Auth {
    public static function check() {
        // return Session::has('user_id');
    }

    public static function id() {
        // return Session::get('user_id');
    }

    public static function user() {
        // if (self::check()) {
        //    // Fetch user from DB based on Session::get('user_id')
        //    // return UserModel::find(self::id());
        // }
        // return null;
    }

    public static function attempt($email, $password) {
        // $user = UserModel::findByEmail($email);
        // if ($user && password_verify($password, $user->password)) {
        //     Session::set('user_id', $user->id);
        //     return true;
        // }
        // return false;
    }

    public static function logout() {
        // Session::destroy();
    }
}
?>

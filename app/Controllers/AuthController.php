<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 01.02.2019
 * Time: 19:06
 */

namespace App\Controllers;


use App\Models\Admin;
use Core\Exceptions\DatabaseException;
use Core\Http\Request;
use Core\Support\Log;

class AuthController
{
    public function index()
    {
        if(_session()->has('auth') and _session()->get('ua') == $_SERVER['HTTP_USER_AGENT'] and _session()->get('ip') == $_SERVER['REMOTE_ADDR']){
            _redirect()->to(APP_URL . '/admin');
        } else {
            $data = _session()->getFlash('form_data');
            return _view('pages/login.twig', ['form_data' => $data]);
        }
    }

    public function login(Request $request)
    {
        $login = $request->get('login');
        $password = $request->get('password');

        $admin = Admin::getByParam('login', $login);

        if(!$admin){
            $data = [
                'login' => $login,
                'login_error' => 'Користувач із введеним логіном не зареєстрований в системі!'
            ];
            _session()->flash('error', 'Спроба авторизації невдала!');
            _session()->flash('form_data', $data);

            return _redirect()->to(APP_URL . "/login");

        } else if(!password_verify($password, $admin->password)){
            $data = [
                'login' => $login,
                'password_error' => 'Не вірно введений пароль!'
            ];
            _session()->flash('error', 'Спроба авторизації невдала!');
            _session()->flash('form_data', $data);

            return _redirect()->to(APP_URL . "/login");
        }

        _session()->set('admin_login', $login);
        _session()->set('auth', true);
        _session()->set('ua', $_SERVER['HTTP_USER_AGENT']);
        _session()->set('ip', $_SERVER['REMOTE_ADDR']);

        return _redirect()->to(APP_URL . '/admin');
    }

    public function logout()
    {
        if(_session()->has('auth')){
            _session()->remove(['admin_login', 'auth', 'ua', 'ip']);
            return _redirect()->to("/");
        }
    }


    public function forgot()
    {
        if(_session()->has('auth') and _session()->get('ua') == $_SERVER['HTTP_USER_AGENT'] and _session()->get('ip') == $_SERVER['REMOTE_ADDR']){
            _redirect()->to(APP_URL . '/admin');
        } else {
            $data = _session()->getFlash('form_data');
            return _view('pages/forgot.twig', ['form_data' => $data]);
        }
    }

}
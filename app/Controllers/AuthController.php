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
use Core\Support\ForgotPassword;
use Core\Support\Mailer;

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
        if($request->has('login') and $request->has('password')){
            $login = htmlspecialchars(trim($request->get('login')));
            $password = htmlspecialchars(trim($request->get('password')));

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


    public function forgotCheckEmail(Request $request)
    {
        if($request->has('email')){
            $email = htmlspecialchars(trim($request->get('email')));

            $admin = Admin::getByParam('email', $email);

            if(!$admin){
                $data = [
                    'email' => $email,
                    'email_error' => 'Користувач із введеною email адресою не зареєстрований в системі!'
                ];
                _session()->flash('error', 'Спроба невдала!');
                _session()->flash('form_data', $data);

                return _redirect()->to(APP_URL . "/forgot");
            } else {
                $forgot = new ForgotPassword();
                $token = $forgot->getToken();

                if($admin->update(['token' => $token])){
                    $message = forgot_mail_body($admin->email, $token);

                    $mail = new Mailer();
                    $mail->setRecipient($admin->email);
                    $mail->setSubject("Відновелння паролю для досутупу до ресурсу");
                    $mail->setMessage($message);

                    try{
                        $mail->send();

                        _session()->flash('success', 'Подальші інструкції для відновлення паролю відправлені на Вашу email адресу!');
                        return _redirect()->to(APP_URL . "/forgot");
                    } catch(MailException $e){
                        _log()->add($e->getError());
                        _session()->flash('error', 'Упс... Сталася помилка. Спробуйте повторити операцію ще раз.');
                        return _redirect()->to(APP_URL . "/forgot");
                    }
                } else {
                    _session()->flash('error', 'Упс... Сталася помилка. Спробуйте повторити операцію ще раз.');
                    return _redirect()->to(APP_URL . "/forgot");
                }
            }
        }
    }


    public function resetPassword(Request $request)
    {
        if(_session()->has('auth') and _session()->get('ua') == $_SERVER['HTTP_USER_AGENT'] and _session()->get('ip') == $_SERVER['REMOTE_ADDR']){
            _redirect()->to(APP_URL . '/admin');
        } else {
            if($request->has('email') and $request->has('token')){
                $email = htmlspecialchars(trim($request->get('email')));
                $token = htmlspecialchars(trim($request->get('token')));

                $admin = Admin::getByParam('email', $email);

                if(!$admin){
                    $data = [
                        'email' => $email,
                        'email_error' => 'Користувач із переданою email адресою не зареєстрований в системі!'
                    ];
                    _session()->flash('error', 'Спроба авторизації невдала!');
                    _session()->flash('form_data', $data);

                    return _redirect()->to(APP_URL . "/forgot");
                } else if($admin->token != $token) {
                    $data = [
                        'email' => $email,
                        'email_error' => 'Переданий токен не вірний!'
                    ];
                    _session()->flash('error', 'Спроба авторизації невдала!');
                    _session()->flash('form_data', $data);

                    return _redirect()->to(APP_URL . "/forgot");
                }

                $data = _session()->getFlash('form_data');
                return _view('pages/reset.twig', ['form_data' => $data, 'token' => $token]);
            } else if(_session()->has('form_data')) {
                $data = _session()->getFlash('form_data');
                return _view('pages/reset.twig', ['form_data' => $data]);
            }

            _session()->flash('error', 'Спроба відновлення паролю невдала! Недостатньо інформації');
            return _redirect()->to(APP_URL . "/forgot");
        }
    }




    public function updatePassword(Request $request)
    {
        if($request->has('password') and $request->has('password_confirm') and $request->has('token')) {

            $token = htmlspecialchars(trim($request->get('token')));

            $admin = Admin::getByParam('token', $token);

            if(!$admin){
                _session()->flash('error', 'Спроба відновлення паролю невдала! Недостатньо інформації');
                return _redirect()->to(APP_URL . "/forgot");
            }

            /*валідуємо новий пароль*/
            $data = $request->validate([
                "password" => "required|max:15|min:8|special+number|latin",
            ]);

            /*перевіряємо чи співпадають значення полів password та password_confirm*/
            if($request->has('password_confirm') and $request->get('password_confirm') !== $data['data']['password']){
                $data['errors']['password_confirm'] = 'Паролі не співпадають.';
            } else if(!$request->has('password_confirm')){
                $data['errors']['password_confirm'] = 'Поле обов\'язкове до заповнення.';
            }

            /*Якщо помилки валідації - повертаємо користувача назад*/
            if(isset($data['errors'])){
                _session()->flash('error', 'Форма містить помилки!');
                $data['data']['token'] = $token;
                _session()->flash('form_data', $data);
                return _redirect()->to(APP_URL . "/reset");
            }

            /*зберігаємо інформацію для бази даних*/
            $dataForStoring = $data['data'];
            $dataForStoring['token'] = null;

            /*хешуємо пароль*/
            $dataForStoring['password'] = $admin->hashPassword($dataForStoring['password']);

            /*оновлюємо інфо в БД*/
            try {
                $admin->update($dataForStoring);
                _session()->flash('success', 'Пароль змінено!');
                return _redirect()->to(APP_URL . "/login");
            } catch (DatabaseException $e){
                _log()->add($e->getError());
                _session()->flash('error', 'Помилка збереження даних! Спробуйте ще раз...');
                return _redirect()->to(APP_URL . "/reset");
            }

        }
    }
}
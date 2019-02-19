<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 21.01.2019
 * Time: 21:35
 */

namespace App\Controllers;


use App\Models\Admin;
use Core\Files\Avatar;
use Core\Http\Request;

class AdminController
{
    public function index()
    {
        return _view('pages/admin/index.twig');
    }



    public function showProfile()
    {
        $admin = Admin::getByParam('login', _session()->get('admin_login'));
        return _view('pages/admin/profile.show.twig', ['admin' => $admin]);
    }



    public function editProfile()
    {
        $admin = Admin::getByParam('login', _session()->get('admin_login'));

        /* у випадку провалу валідації, дані з помилками записуються в сесію, і викликаєтсья редірект на сторінку редагування новини.
         * Тому, необхідно отримувати дані про помилки із сесії, перед тим як повертати представлення даної сторінки.
         */
        $data = _session()->getFlash('form_data');

        return _view('pages/admin/profile.edit.twig', ['admin' => $admin, 'form_data' => $data]);
    }


    public function editPassword()
    {
        $admin = Admin::getByParam('login', _session()->get('admin_login'));

        /* у випадку провалу валідації, дані з помилками записуються в сесію, і викликаєтсья редірект на сторінку редагування новини.
         * Тому, необхідно отримувати дані про помилки із сесії, перед тим як повертати представлення даної сторінки.
         */
        $data = _session()->getFlash('form_data');

        return _view('pages/admin/password.edit.twig', ['admin' => $admin, 'form_data' => $data]);
    }


    public function updateProfile(Request $request, $id)
    {
        $admin = Admin::getByParam('id', $id);

        if(!$admin){
            _session()->flash('error', 'Упс! Сталася помилка передачі даних. Спробуйте, будь ласка, ще раз');
            return _redirect()->to("admin/profile/profile_edit");
        }

        /*Різні правила валідації для випадків, коли аватар передано та коли - ні*/
        if($request->hasFile('photo')){
            $data = $request->validate([
                "photo" => "required|maxsize:5120|image|proportions:500*500",
                "login" => "required|max:15|min:3",
                "email" => "required|max:255|min:6|email"
            ]);
        } else {
            $data = $request->validate([
                "login" => "required|max:15|min:3",
                "email" => "required|max:255|min:6|email"
            ]);
        }

        /*Якщо помилки валідації - повертаємо користувача назад*/
        if(isset($data['errors'])){
            _session()->flash('error', 'Форма містить помилки!');
            _session()->flash('form_data', $data);
            return _redirect()->to(APP_URL . "/admin/profile/profile_edit");
        }

        /*зберігаємо інформацію для бази даних*/
        $dataForStoring = $data['data'];

        /*Перевіряємо чи передане нове головне фото новини, якщо воно передане і якщо новина має не дефолтне фото - видаляємо його*/
        if($request->hasFile('photo')){
            if($admin->photo){
                /*видаляємо попереднє фото новини, якщо воно було не дефолтне*/
                if($admin->deletePhoto()){
                    /*зберігаємо нове фото новини, у випадку успішного видалення попереднього фото*/
                    $photo = new Avatar($data['data']['photo']);

                    if(!$photo->save()){
                        _session()->flash('error', 'Помилка збереження нового аватару! Спробуйте будь ласка ще раз...');
                        return _redirect()->to(APP_URL . "/admin/profile/profile_edit");
                    }
                    /*замість масиву із інформацією про файл зберігаємо в БД src зображення*/
                    $dataForStoring['photo'] = $photo->getSrc();
                } else {
                    _session()->flash('error', 'Помилка видалення попереднього аватару! Спробуйте будь ласка ще раз...');
                    return _redirect()->to(APP_URL . "/admin/profile/profile_edit");
                }
            } else {
                $photo = new Avatar($data['data']['photo']);

                if(!$photo->save()){
                    _session()->flash('error', 'Помилка збереження нового аватару! Спробуйте будь ласка ще раз...');
                    return _redirect()->to(APP_URL . "/admin/profile/profile_edit");
                }
                /*замість масиву із інформацією про файл зберігаємо в БД src зображення*/
                $dataForStoring['photo'] = $photo->getSrc();
            }
        }

        /*Видаляємо всі зайві файли із тимчасової папки*/
        cleanDirectory("images/profile/temporary");

        /*оновлюємо інфо в БД*/
        try {
            $admin->update($dataForStoring);

            /*у випадку якщо оновлено логін - перезаписуємо даний параметер в сесії*/
            if(_session()->get('admin_login') !== $dataForStoring['login']){
                _session()->set('admin_login', $dataForStoring['login']);
            }

            _session()->flash('success', 'Профіль відредаговано!');
            return _redirect()->to(APP_URL . "/admin/profile");
        } catch (DatabaseException $e){
            _log()->add($e->getError());
            _session()->flash('error', 'Помилка збереження даних! Спробуйте ще раз...');
            return _redirect()->to(APP_URL . "/admin/profile/profile_edit");
        }

    }


    public function checkCurrentPassword(Request $request)
    {
        if($request->has('password_change') and $request->get('password_change') === 'initiated' and $request->has('pass')){
            $admin = Admin::getByParam('login', _session()->get('admin_login'));
            $requestPass = $request->get('pass');

            if(password_verify($requestPass, $admin->password)){
                echo 'Пароль введено вірно';
                return;
            }

            echo "Не вірно введений пароль";
        }
    }


    public function updatePassword(Request $request, $id)
    {
        $admin = Admin::getByParam('id', $id);

        if(!$admin){
            _session()->flash('error', 'Упс! Сталася помилка передачі даних. Спробуйте, будь ласка, ще раз');
            return _redirect()->to("admin/profile/password_edit");
        }

        /*перевіряємо чи співпадає діючий пароль, який прийшов із форми із тим, дібчим в БД*/
        if($request->has('old_password') and $request->get('old_password') != password_verify($request->get('old_password'), $admin->password)){
            _session()->flash('error', 'Передано не вірний діючий пароль!');
            return _redirect()->to("admin/profile/password_edit");
        }

        /*валідуємо новий пароль*/
        $data = $request->validate([
            "password" => "required|max:15|min:8|special+number",
        ]);

        /*перевіряємо чи співпадають значення полів password та password_confirm*/
        if($request->has('password_confirm') and $request->get('password_confirm') !== $data['data']['password']){
            $data['errors']['password_confirm'] = 'Паролі не співпадають.';
            $data['data']['password_confirm'] = $request->get('password_confirm');
        } else if(!$request->has('password_confirm')){
            $data['errors']['password_confirm'] = 'Поле обов\'язкове до заповнення.';
        }

        /*Якщо помилки валідації - повертаємо користувача назад*/
        if(isset($data['errors'])){
            _session()->flash('error', 'Форма містить помилки!');
            _session()->flash('form_data', $data);
            return _redirect()->to(APP_URL . "/admin/profile/password_edit");
        }

        /*зберігаємо інформацію для бази даних*/
        $dataForStoring = $data['data'];

        /*хешуємо пароль*/
        $dataForStoring['password'] = $admin->hashPassword($dataForStoring['password']);

        /*оновлюємо інфо в БД*/
        try {
            $admin->update($dataForStoring);

            _session()->flash('success', 'Пароль успішно змінено!');
            return _redirect()->to(APP_URL . "/admin/profile");
        } catch (DatabaseException $e){
            _log()->add($e->getError());
            _session()->flash('error', 'Помилка збереження даних! Спробуйте ще раз...');
            return _redirect()->to(APP_URL . "/admin/profile/password_edit");
        }

    }

}
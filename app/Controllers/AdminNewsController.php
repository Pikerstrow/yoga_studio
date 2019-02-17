<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 21.01.2019
 * Time: 21:35
 */

namespace App\Controllers;

use App\Models\News;
use Core\Exceptions\DatabaseException;
use Core\Files\File;
use Core\Files\Image;
use Core\Http\Request;
use Core\Support\Slug;

class AdminNewsController
{
    use Slug;

    public function index(Request $request)
    {
        if($request->has('page')){
            if(ctype_digit($request->get('page'))){
                $page = $request->get('page');
            } else {
                return _redirect()->to(APP_URL . "/404");
            }
        } else {
            $page = 1;
        }
        $data = News::paginate("admin/news", 3, $page);
        return _view('pages/admin/news.view.twig', ['news' => $data['objects'], 'links' => $data['links']]);
    }


    public function create()
    {
        /* у випадку провалу валідації, дані з помилками записуються в сесію, і викликаєтсья редірект на сторінку створення новини.
         * Тому, необхідно отримувати дані із сесії, перед тим як повертати представлення даної сторінки.
         */
        $data = _session()->getFlash('form_data');
        return _view('pages/admin/news.add.twig', ['form_data' => $data]);
    }


    public function destroy(Request $request)
    {
        if($request->has('post_id')){
            if(ctype_digit($request->get('post_id'))){
                $post = News::getByParam('id', $request->get('post_id'));
                if(!$post){
                    _session()->flash('error', 'Упс! Сталася помилка передачі даних. Спробуйте, будь ласка, ще раз');
                    return _redirect()>to("admin/news");
                }
                if($post->delete()){
                    /*Видаляємл головне фото новини та зображення із тексту новини*/
                    $post->deletePhoto();
                    $post->deleteBodyImages();

                    _session()->flash('success', 'Новину видалено!');
                    return _redirect()->to(APP_URL . "/admin/news");
                } else {
                    _session()->flash('error', 'Помилка видалення даних! Спробуйте ще раз...');
                    return _redirect()->to(APP_URL . "/admin/news");
                }
            } else {
                _session()->flash('error', 'Передане значення недопустиме!');
                return _redirect()->to(APP_URL . "/admin/news");
            }
        } else {
            _session()->flash('error', 'Новину не передано!');
            return _redirect()->to(APP_URL . "/admin/news");
        }
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            "photo" => "required|maxsize:5120|image|proportions:1100*1100",
            "title" => "required|max:120|min:3",
            "body" => "required|max:15000|min:10"
        ]);

        if(isset($data['errors'])){
            _session()->flash('error', 'Форма містить помилки!');
            _session()->flash('form_data', $data);
            return _redirect()->to(APP_URL . "/admin/news/create");
        }

        /*зберігаємо файл головного фото*/
        $photo = new Image($data['data']['photo']);

        if(!$photo->save()){
            _session()->flash('error', 'Помилка збереження файлу! Спробуйте будь ласка ще раз...');
            return _redirect()->to(APP_URL . "/admin/news/create");
        }

        $dataForStoring = $data['data'];

        /*перезаписуємо значення "photo" із масиву на url файлу*/
        $dataForStoring['photo'] = $photo->getSrc();

        /*операції над зображенням в тілі новини. Див. метод*/
        $this->arrangeBodyImages($dataForStoring['body'], "temporary");
        /*Видаляємо всі зайві файли із тимчасової папки*/
        cleanDirectory("images/news/temporary");

        /*додаємо slug з яким буде формуватися ЧПУ*/
        $dataForStoring['slug'] = $this->slug($dataForStoring['title']);

        /*Зберігаємо новину в БД*/
        try {
            News::create($dataForStoring);
            _session()->flash('success', 'Новина опублікована!');
            return _redirect()->to(APP_URL . "/admin/news/create");
        } catch (DatabaseException $e){
            _log()->add($e->getError());
            _session()->flash('error', 'Помилка збереження даних! Спробуйте ще раз...');
            return _redirect()->to(APP_URL . "/admin/news/create");
        }
    }



    public function edit(Request $request, $id)
    {
        $post = News::getByParam('id', $id);

        if(!$post){
            _session()->flash('error', 'Упс! Сталася помилка передачі даних. Спробуйте, будь ласка, ще раз');
            return _redirect()>to("admin/news");
        }

        /* у випадку провалу валідації, дані з помилками записуються в сесію, і викликаєтсья редірект на сторінку редагування новини.
         * Тому, необхідно отримувати дані про помилки із сесії, перед тим як повертати представлення даної сторінки.
         */
        $data = _session()->getFlash('form_data');

        /*При першому завантажені форми редагування, в сесії помилки відсутні. Тому здійснюємо маніпуляції з файлами*/
        if(empty($data)){
            $this->arrangeBodyImages($post->body, "main");
        }

        return _view('pages/admin/news.edit.twig', ['post' => $post, 'form_data' => $data]);
    }



    public function update(Request $request, $id)
    {
        $post = News::getByParam('id', $id);

        if(!$post){
            _session()->flash('error', 'Упс! Сталася помилка передачі даних. Спробуйте, будь ласка, ще раз');
            return _redirect()>to("admin/news/edit/" . $id);
        }

        /*Різні правила валідації для випадків, коли нове фото передано та коли - ні*/
        if($request->hasFile('photo')){
            $data = $request->validate([
                "photo" => "required|maxsize:5120|image|proportions:1100*1100",
                "title" => "required|max:120|min:3",
                "body" => "required|max:15000|min:10"
            ]);
        } else {
            $data = $request->validate([
                "title" => "required|max:120|min:3",
                "body" => "required|max:15000|min:10"
            ]);
        }

        /*Якщо помилки валідації - повертаємо користувача назад*/
        if(isset($data['errors'])){
            _session()->flash('error', 'Форма містить помилки!');
            _session()->flash('form_data', $data);
            return _redirect()->to(APP_URL . "/admin/news/edit/" . $id);
        }

        /*зберігаємо інформацію для бази даних*/
        $dataForStoring = $data['data'];

        /*операції над зображенням в тілі новини. Див. метод*/
        $this->arrangeBodyImages($dataForStoring["body"], "temporary");

        /*Видаляємо всі зайві файли із тимчасової папки*/
        cleanDirectory("images/news/temporary");

        /*Перевіряємо чи передане нове головне фото новини, якщо воно передане і якщо новина має не дефолтне фото - видаляємо його*/
        if($request->hasFile('photo')){
            if($post->photo){
                /*видаляємо попереднє фото новини, якщо воно було не дефолтне*/
                if($post->deletePhoto()){
                    /*зберігаємо нове фото новини, у випадку успішного видалення попереднього фото*/
                    $photo = new Image($data['data']['photo']);

                    if(!$photo->save()){
                        _session()->flash('error', 'Помилка збереження нового фото! Спробуйте будь ласка ще раз...');
                        return _redirect()->to(APP_URL . "/admin/news/edit/" . $id);
                    }
                    /*замість масиву із інформацією про файл зберігаємо в БД src зображення*/
                    $dataForStoring['photo'] = $photo->getSrc();
                } else {
                    _session()->flash('error', 'Помилка видалення попереднього головного фото новини! Спробуйте будь ласка ще раз...');
                    return _redirect()->to(APP_URL . "/admin/news/edit/" . $id);
                }
            }
        }

        /*робимо slug із title для можливості створення ЧПУ*/
        $dataForStoring['slug'] = $this->slug($dataForStoring['title']);

        /*оновлюємо інфо в БД*/
        try {
            $post->update($dataForStoring);
            _session()->flash('success', 'Новину відредаговано!');
            return _redirect()->to(APP_URL . "/admin/news");
        } catch (DatabaseException $e){
            _log()->add($e->getError());
            _session()->flash('error', 'Помилка збереження даних! Спробуйте ще раз...');
            return _redirect()->to(APP_URL . "/admin/news/edit/" . $id);
        }
    }


    private function arrangeBodyImages(&$needle, $from)
    {
        /*Переміщуємо всі зображення, які були добавлені в текст новини через CKEditor із тимчасової папки в постійну або навпаки (при редагуванні),
          замінюючи їх src. Дозволить видаляти непотрібні зображення, які ніде не використовуюються
        */
        $newsBodyImages = getImagesFileNamesFromSrc($needle);

        if (!empty($newsBodyImages)) {
            switch ($from) {
                case "main":
                    foreach ($newsBodyImages as $value) {
                        if (file_exists($oldName = News::NEWS_IMAGES_MAIN_STORAGE . $value)) {
                            File::move($oldName, News::NEWS_IMAGES_TEMPORARY_STORAGE . $value);
                        }
                    }
                    $needle = preg_replace_callback("#<img src=\"(.*?)\">#", function($matches){
                        return str_replace("main", "temporary", $matches[0]);
                    }, $needle);
                    break;
                case "temporary":
                    foreach ($newsBodyImages as $value) {
                        if (file_exists($oldName = News::NEWS_IMAGES_TEMPORARY_STORAGE . $value)) {
                            File::move($oldName, News::NEWS_IMAGES_MAIN_STORAGE . $value);
                        }
                    }
                    $needle = preg_replace_callback("#<img src=\"(.*?)\">#", function($matches){
                        return str_replace("temporary", "main", $matches[0]);
                    }, $needle);
                    break;
            }
        }

    }

}
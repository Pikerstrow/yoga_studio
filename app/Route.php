<?php

namespace App;

use App\Controllers\ErrorController;
use Core\Exceptions\FilesException;
use Core\Files\File;
use Core\Http\Request;
use Core\Support\Log;

class Route {

    const CONTROLLER_NAMESPACE = __NAMESPACE__ . "\\Controllers\\";

    protected $routes = array();
    protected $request;


    public function __construct()
    {
        $file = APP_ROOT . "/app/routes.json";

        /*Отримуємо перелік всіх досутпних маршрутів*/
        try {
            $this->routes = File::getJsonData($file);
        } catch(FilesException $e){
            _log()->add($e->getError());
        }

        $this->request = new Request();
    }


    /*Повертає назву маршруту в залежності від того чи є передані GET параметри*/
    public function getRoute() :string
    {
        return $this->hasParams() ? substr(explode('/?', $this->request->getPath())[0], 1) : substr($this->request->getPath(), 1);
    }


    public function hasParams() :bool
    {
        return (bool) strpos($this->request->getPath(), '/?');
    }


    public function getAvailableRoutes()
    {
        return $this->routes;
    }


    public function start()
    {
        $routes = $this->getAvailableRoutes();
        $route  = $this->getRoute();
        $identifier = null;

        foreach($routes as $name => $properties){

            /*Перевірка для статичних маррутів*/
            if($route === $name){

                /*Перевіряємо вимогу стосовно авторизації до маршруту*/
                $this->checkAuthorization($properties);

                $controllerName = self::CONTROLLER_NAMESPACE . ucfirst($properties['controller']);
                $controller = new $controllerName;
                $method = $properties['method'];

                return $controller->$method($this->request);

            } else {
                /*Перевірка для динамічних маршрутів*/
                if($properties['identifier']){
                    /*перевіряємо маршрут із зареєстрованих маршрутів на наявність динамічного параметру, який реєструєця у файлі із маршрутами
                        в фігурних дужках. Наприклад: news/{slug}. Зберігаємо ідентифіктор в окрему зміну для подальшої перевірки URI
                    */
                    if(preg_match("#{(.*)}#", $name, $matches)){
                        $identifierProperty = $matches[0];
                        /*
                        * забираємо з переданого URI останню частину адреси. Наприклад URI admin_resources/news/5, отримаємо - admin_resources/news
                        */
                        $routeSubstr = mb_substr($route, 0, strrpos($route, '/')+1);
                        /*звіряємо переданий URI конкатинований із ідентифікатором із маршруту із зареєстрованими маршрутами.
                          У випадку співпадіння - викликаємо необхідний метод контролера
                        */
                        if($name == $routeSubstr . $identifierProperty){
                            $identifier = mb_substr($route, strrpos($route, '/')+1);

                            /*Перевіряємо вимогу стосовно авторизації до маршруту*/
                            $this->checkAuthorization($properties);

                            $controllerName = self::CONTROLLER_NAMESPACE . ucfirst($properties['controller']);
                            $controller = new $controllerName;
                            $method = $properties['method'];

                            return $controller->$method($this->request, $identifier);
                        }
                    } else {
                        return ErrorController::notFound();
                    }
                }
            }
        }
        return ErrorController::notFound();
    }


    protected function checkAuthorization($properties)
    {
        if($properties['auth'] and !_session()->has('auth')) {
            return _redirect()->to(APP_URL . "/login"); // сторінка із формаю для авторизації
        }
    }
}
<?php

use Core\Support\Log;
use Core\Support\Config;
use Core\Http\Request;
use Core\Support\Redirect;
use Core\Support\Session;
use Core\Twig\TwigFunctions;

$session = new Session();


define('DS', DIRECTORY_SEPARATOR);
define('APP_ROOT', dirname($_SERVER['DOCUMENT_ROOT']));
define('APP_URL', 'http://anahata.test');


function _log(){
    $log = get_class(new Log());
    return new $log;
}

function _redirect(){
    $redirect = get_class(new Redirect());
    return new $redirect;
}

function _config(){
    $config = get_class(new Config());
    return new $config;
}

function _request(){
    $request = get_class(new Request());
    return new $request;
}

function _session(){
    $session = get_class(new Session());
    return new $session;
}


function _view($name, $data = [] ){
    $loader = new \Twig_Loader_Filesystem(dirname(__DIR__) . '/app/Views');
    $twig = new \Twig_Environment($loader, [
        'debug' => true,
    ]);
    $twig->addExtension(new Twig_Extension_Debug());
    $twig->addExtension(new TwigFunctions());

    $twig->addGlobal("session", $_SESSION);

    echo $twig->render($name, $data);
}


function checkValidatedDataForErrors($data)
{
    foreach($data['errors'] as $error){
        if(isset($error) and !empty($error)){
            return true;
        }
    }
    return false;
}

function cleanDirectory($directory){
    array_map('unlink', glob(APP_ROOT . DS . "public" . DS . $directory . DS . "*"));
}

function getImagesFileNamesFromSrc($needle){
    preg_match_all("#<img src=\"(.*?)\">#", $needle, $matches);

    if(!empty($matches[1])){
        foreach($matches[1] as $value){
            $fileNames[] = mb_substr($value, strrpos($value, "/")+1);
        }
        return $fileNames;
    }
    return [];
}
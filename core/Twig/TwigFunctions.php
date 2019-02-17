<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 02.02.2019
 * Time: 21:04
 */

namespace Core\Twig;

use App\Models\Admin;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigFunctions extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('flash', [$this, 'flash']),
            new TwigFunction('showInfoBlock', [$this, 'showInfoBlock'], ['is_safe' => array('html')]),
            new TwigFunction('url', [$this, 'url'], ['is_safe' => array('html')]),
            new TwigFunction('adminGet', [$this, 'adminGet'], ['is_safe' => array('html')]),
            new TwigFunction('postBodyPreview', [$this, 'postBodyPreview'], ['is_safe' => array('html')]),
        ];
    }


    public function flash($data)
    {
        if(_session()->has($data)){
            return _session()->getFlash($data);
        }
    }


    public function getInfoBlock($message, $type = 'success')
    {
        switch ($type) {
            case 'success':
                $classname = 'alert-success';
                $strong = '<i class="fas fa-check-circle fa-lg"></i> ' . 'Дія успішна!';
                break;
            case 'error':
                $classname = 'alert-danger';
                $strong = '<i class="fas fa-exclamation-circle fa-lg"></i> ' . 'Помилка!';
                break;
        }

        $infoblock = "<div class=\"alert {$classname} alert-dismissible fade show\" role=\"alert\">";
        $infoblock .= "<strong>$strong</strong> $message";
        $infoblock .= "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">";
        $infoblock .= "<span aria-hidden=\"true\">&times;</span>";
        $infoblock .= "</button></div>";

        return $infoblock;
    }


    public function url($path = "", $identifier = "", $get = array())
    {
        $url = APP_URL . "/" . $path;

        $counter = 1;

        if([] === $get and $identifier === "" ){
            return $url;
        } else if ([] === $get and $identifier) {
            return $url .= "/" . $identifier;
        } else if ([] != $get and $identifier) {
            $url .= "/" . $identifier . "/?";

            foreach ($get as $key => $value) {
                if($counter == count($get)) {
                    $url .= $key . "=" . $value;
                    break;
                } else {
                    $url .= $key . "=" . $value . "&";
                }
                $counter++;
            }
            return $url;
        } else {
            $url .= "/?";

            foreach ($get as $key => $value) {
                if($counter == count($get)) {
                    $url .= $key . "=" . $value;
                    break;
                } else {
                    $url .= $key . "=" . $value . "&";
                }
                $counter++;
            }
            return $url;
        }
    }


    public function showInfoBlock()
    {
        $block = null;

        if(_session()->has('error')){
            $block = $this->getInfoBlock(_session()->getFlash('error'), 'error');
        } else if(_session()->has('success')) {
            $block = $this->getInfoBlock(_session()->getFlash('success'));
        }

        return $block;
    }

    public function adminGet(string $property)
    {
        if(_session()->has('auth')){
            $admin = Admin::getByParam('login', _session()->get('admin_login'));

            if(property_exists($admin, $property)){
                return $admin->$property;
            }
        }
        return null;
    }

    public function postBodyPreview($post)
    {
        preg_match("#\<p\>(.*)\<\/p\>#U", $post, $matches);

        if(!empty($matches)){
            $firstParagraph = $matches[1];
            $preview = preg_replace("#<img(.*?)\>#", " ", $firstParagraph);
            if(mb_strlen($preview) > 150){
                return str_replace(mb_substr($preview, 150), " ... ", $preview);
            }
            return $preview;
        }
        return mb_strlen($post) > 150 ? str_replace(mb_substr($post, 150), " ... ", $post) : $post;
    }
}
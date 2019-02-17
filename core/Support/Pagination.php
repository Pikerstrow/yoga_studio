<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 08.02.2019
 * Time: 18:46
 */

namespace Core\Support;


class Pagination
{
    public $currentPage;
    public $perPage;
    public $totalItems;
    public $totalPages;
    public $onEachSides;

    public function __construct($onEachSides = 1)
    {
        $this->onEachSides = $onEachSides;
    }

    public function next()
    {
        return $this->currentPage + 1;
    }


    public function previous()
    {
        return $this->currentPage - 1;
    }

    public function has_previous()
    {
        return $this->previous() >= 1 ? true : false;
    }

    public function has_next()
    {
        return $this->next() <= $this->totalPages ? true : false;
    }


    public function offset()
    {
        return ($this->currentPage - 1) * $this->perPage;
    }

    private function createPaginationLinks($uri)
    {
        $addDots   = true;

        if($this->totalPages <= 1){
            $htmlLinks = '';
        } else {

            $htmlLinks = "<ul class='pagination'>";

            if ($this->currentPage == 1) {
                $htmlLinks .= "<li class='page-item disabled'><a class='page-link' href='#'> &laquo; </a></li>";
            } else {
                $page = $this->currentPage - 1;
                $htmlLinks .= "<li class='page-item'><a class='page-link' href='" . APP_URL . "/" . $uri . "/?page={$page}' tabindex='-1'>&laquo;</a></li>";
            }

            for ($i = 1; $i <= $this->totalPages; $i++) {
                if ($i == $this->currentPage) {
                    $htmlLinks .= "<li class='page-item active'><a class='page-link' href='" . APP_URL . "/" . $uri . "/?page={$i}'>{$i}<span class='sr-only'>(current)</span></a></li>";
                    $addDots = true;
                } else if ($i == 1 ||
                           $i == 2 ||
                           $i == $this->totalPages ||
                           $i == ($this->totalPages - 1) ||
                           ($i >= $this->currentPage - $this->onEachSides && $i <= $this->currentPage + $this->onEachSides)) {
                    $htmlLinks .= "<li class='page-item'><a class='page-link' href='" . APP_URL . "/" . $uri . "/?page={$i}'>{$i}<span class='sr-only'>(current)</span></a></li>";
                    $addDots = true;
                } else {
                    if ($addDots) {
                        $htmlLinks .= "<li class='page-item disabled'> <a class='page-link' href='#'>... </a></li>";
                        $addDots = false;
                    }
                }
            }

            if ($this->currentPage == $this->totalPages) {
                $htmlLinks .= "<li class='page-item disabled'><a class='page-link' href='#'> &raquo; </a></li>";
            } else {
                $page = $this->currentPage + 1;
                $htmlLinks .= "<li class='page-item'><a class='page-link' href='" . APP_URL . "/" . $uri . "/?page={$page}'>&raquo;</a></li>";
            }

            $htmlLinks .= "</ul>";

        }
        return isset($htmlLinks) ? $htmlLinks : '';
    }


    public function getLinks($uri)
    {
        return $this->createPaginationLinks($uri);
    }

}
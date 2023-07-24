<?php

namespace Controllers\Common\Errors;

use System\Base\Controller as BaseController;

class PageNotFoundController extends BaseController{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->title = "Ошибка 404 - Страница не найдена";
        $this->content = $this->view->render("Common/Errors/404.twig");
    }
}
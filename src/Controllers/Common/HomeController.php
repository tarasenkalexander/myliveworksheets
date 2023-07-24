<?php
namespace Controllers\Common;

use System\Base\Controller as BaseController;

class HomeController extends BaseController
{
    public function index() {
        $this->title = "Home page";
        $this->content = $this->view->render('Common/home.twig');
    }
}

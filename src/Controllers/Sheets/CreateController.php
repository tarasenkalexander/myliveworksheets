<?php
namespace Controllers\Sheets;

use System\Base\Controller as BaseController;

class CreateController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->checkAccess();
    }

    public function create()
    {
        $this->title   = 'Создать упражнение';
        $this->content = $this->view->render('Sheets/create.twig');
    }
}
/**
 * get - отобразить
 * post - сохранить
 * 
 * if(get){
 *       открываю щит для прохождения: создаю pageContent на основе v_sheet_test
 *      if(залогинен && автор щита)
 *      {
 *          добавляю кнопку перехода в режим редактирования
 *      }
 * }
 * else if(post)
 * {
 *      Значит я сохраняю задание: извлекаю из _POST необходимую информацию, обращаюсь в model
 *      
 *      
 * }
 * 
 * 
 */
<?php
class BackEndController extends BaseController
{

  // лейаут
  public $layout='//layouts/column2';
        
  // меню
  public $menu = array();
  
  // крошки
  public $breadcrumbs = array();
  
  /**
  Инициализация данных
  */
  public function init(){
    
  }
  
  
  
  public function actionIndex()
  {
    echo 'all good';
  }
  
  /*
      Фильтры
  */
  public function filters()
  {
      return array(
          'accessControl',
      );
  }
    
    
  /*
      Права доступа
  */
   public function accessRules() {
     return array();
    /* 
    return array(
        // даем доступ только админам
        array(
            'allow',
            //'roles' => array('admin','moderator'),
            'roles' => array('*'),
        ),
        array(
            'deny',
            'users' => array('*'),
        ),
    );*/
  }
}
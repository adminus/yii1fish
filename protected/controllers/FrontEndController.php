<?php
class FrontEndController extends BaseController
{
  // лейаут
  //public $layout = 'layouts';      
  // меню
  public $menu = array();
  
  // крошки
  public $breadcrumbs = array();
  
  public function init() {
    parent::init();
  }
    
}
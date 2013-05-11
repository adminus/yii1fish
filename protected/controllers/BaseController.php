<?php
class BaseController extends CController
{
  public static $root = null;// центральная директория приложения
  public static $is_quest = null;//по умолчанию гость
  public static $is_test = null;//проверяем тестовый сервер, али нет
  public static $is_frontend = null;//проверяем какую часть запустили
  public static $cache = null;//переменная для отключения кеша в системе при отладке
  
  //Служебные переменные шаблона
  public $pageDescription = '';
  public $pageKeywords = '';
    
  /*Глобальная переменная пользователя
  обязательна к использыванию , что бы сократить кол-во
  мест загрузки информации по пользователю
  */
  public static $user  = null;
  
  /*TODO наличие бана под вопросом
  #есть ли у игрока бан
  //0 - нет
  //1 - временный и действует
  //2 - постоянный
  public static $have_ban = 0;
  */
  
  
  /**
  Инициализация данных
  */
  public function init(){

  }
  
  /**
  set-get метод для проверки части приложения
  */
  public static function is_frontdend(){
    if(self::$is_frontend==null){
      self::$is_frontend = true;
      if(preg_match('/^\/admin/i', $_SERVER['REQUEST_URI'])){
        self::$is_frontend = false;  
      }
    }
    return self::$is_frontend;
  }
  
  /**
  set-get метод для проверки гостя
  */
  public static function is_quest(){
    if(self::$is_quest==null){
      self::$is_quest = Yii::app()->user->isGuest;
    }
    return self::$is_quest;
  }
  
  /**
  set-get метод для проверки тестового сервера
  */
  public static function is_test(){
    if(self::$is_test==null){
      global $test_server_variable;
      self::$is_test = $test_server_variable;
    }
    return self::$is_test;
  }
  
  /**
  set-get метод для корневой директории приложения
  */
  public static function getRoot(){
    if(!self::$root){
      $path = pathinfo(Yii::app()->basePath, PATHINFO_DIRNAME);
      self::$root = $path.'/';
    }
    return self::$root;
  }
  
  /**
  set-get метод для установки загрузки без кеша.  
  !!!WARNING!!! работает только на тестовом сервере
  */
  public static function no_cache(){
    if(self::$cache==null){
      if(self::is_test()){
        self::$cache = empty($_REQUEST['nocache']);
      }else{
        self::$cache = true;
      }
    }
    return !self::$cache;
  }

  /**
  Метод устанавливает предупреждения и напоминания 
  для пользователя
  */
  public static function setFlash($type,$message){
      $type = empty($type) ? 'default' : $type;
      $type_array = array('default','ok','error','info');
      if(!in_array($type,$type_array,true)){
        $type = 'default';
      }
      $type = $type=='ok' ? 'success' : $type;
      Yii::app()->user->setFlash($type, $message);
    }
    
  #########################SYSTEM METHODS#########################
  
  /**
  Возвращает уникальное имя для файлв в указанной
  директории
  */
  public static function uni_name($dir = '', $pref = '') {    
    $nextMove = true;
    do {
      $file = md5(mt_rand());
      if (count(glob($dir . "/" . $file . '.*')) <= 1) {
        $nextMove = false;
      }
    } while ($nextMove);
    return $file;
  }
  
  /**
  Возвращает расширение файла
  */
  public static function getExt($file){      
    return pathinfo($file, PATHINFO_EXTENSION);
  }
  
  
  /**
  Возвращает дату человеческим представлением
  */
  public static function returnWhen($date, $unixtimestamp = false) {

    if (!$date) {//забыли передать дату
      return "не известно";
    }

    //вычисляем разницу в зависимости от способо передачи
    $secs  = $unixtimestamp ? ceil(time() - (int) $date) : ceil(time() - strtotime($date));

    if ($secs == 0) {//да, да - прямо сейчас это произошло
      return "сейчас";
    }

    if ($secs < 0) {//по каким то причниам разница отрицательная
      return "не известно";
    }
    //проверяем по нарастающей в каком из периодов наша разница
    $periods = array(1, 60, 3600, 86400, 604800, 2592000, 31104000);
    $i = 0;
    foreach ($periods as $period) {
      if ($secs > $period) {
        $i++;
      } else {
        break;
      }
    }
    $i--;
    
    //языковое обозначение
    $word = array(
      0 => array('секунда', 'секунды', 'секунд'),
      1 => array('минуту', 'минуты', 'минут'),
      2 => array('час', 'часа', 'часов'),
      3 => array('день', 'дня', 'дней'),
      4 => array('неделя', 'недели', 'недель'),
      5 => array('месяц', 'месяца', 'месяцев'),
      6 => array('год', 'года', 'лет'),
    );
    //определяем сколько целых периодов прошло
    $value = round($secs / $periods[$i]);

    //магия с вычислением текущего слова под цифру
    $z_index = 0;
    $n = abs($value) % 100;
    $n1 = $n % 10;
    if ($n > 10 && $n < 20) {
      $z_index = 2;
    } else if ($n1 > 1 && $n1 < 5) {
      $z_index = 1;
    } else if ($n1 == 1) {
      $z_index = 0;
    } else {
      $z_index = 2;
    }

    if ($i == 3 && $secs <= (3600 * 24 * 2)) {//то есть еще не прошло 2 дня
      return "вчера";
    } else {
      return $value . " " . $word[$i][$z_index] . " назад";
    }
  }
      
  public static function number_format($number,$for_javascript = false){
    return number_format($number, 0, $for_javascript ? '.' : ',', '`');
  }
      
  public static function date_format($time){
    return date('d F Y H:i',$time);          
  }
      
  /**
  * 
  * @param string $date normal date
  */
  public static function russian_time($date){
    $replace = array(
      "January" => "января",
      "February" => "февраля",
      "March" => "марта",
      "April" => "апреля",
      "May" => "мая",
      "June" => "июня",
      "July" => "июля",
      "August" => "августа",
      "September" => "сентября",
      "October" => "октября",
      "November" => "ноября",
      "December" => "декабря",	

      "Sunday" => "воскресенье",
      "Monday" => "понедельник",
      "Tuesday" => "вторник",
      "Wednesday" => "среда",
      "Thursday" => "четверг",
      "Friday" => "пятница",
      "Saturday" => "суббота",

      "Sun" => "воскресенье",
      "Mon" => "понедельник",
      "Tue" => "вторник",
      "Wed" => "среда",
      "Thu" => "четверг",
      "Fri" => "пятница",
      "Sat" => "суббота",
    );
      
    return str_replace(array_keys($replace), array_values($replace), $date);
  }
  
  #########################/SYSTEM METHODS#########################
  
  #########################TEMPLATE METHODS#########################
 
  /**
  *
  * Добавляет значение title страницы перед существующим 
  *
  * $value mixed массив или строка
  * $start boolean при значение true затирает все предыдущие значения
  */
  public function beforeTitle($value,$start = false){
    if(gettype($value)=='array'){
      $array = array();            
      
      settype($value, 'array');
      foreach($value as $val){
        $array[] = $val;
      }
      $array[] = $start? Yii::app()->name : $this->pageTitle;
      $this->pageTitle = implode(Yii::app()->params['title_delimiter'],$array);
    }else{
      $this->pageTitle = $value.Yii::app()->params['title_delimiter'].($start? Yii::app()->name : $this->pageTitle);
    }
  }
  
  /**
  *
  * Добавляет значение title страницы после существующего
  *
  * $value mixed массив или строка
  * $start boolean при значение true затирает все предыдущие значения
  */
  public function afterTitle($value,$start = false){
    if(gettype($value)=='array'){
      $array = array();            
      $array[] = $start? Yii::app()->name : $this->pageTitle;
      settype($value, 'array');
      foreach($value as $val){
        $array[] = $val;
      }      
      $this->pageTitle = implode(Yii::app()->params['title_delimiter'],$array);
    }else{
      $this->pageTitle = ($start? Yii::app()->name : $this->pageTitle).Yii::app()->params['title_delimiter'].$value;
    }
  }  
  #########################/TEMPLATE METHODS#########################
}
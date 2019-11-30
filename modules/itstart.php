<?php
include "share/myHtmlHalper.php";
//Базовые параметры для нормальной работы сайта
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_http_input('UTF-8');
mb_regex_encoding('UTF-8'); 


class PageBuilder{
				//Папки с контентом
				private $contentFolder = "content/Home";
				//Папка с ресурсами
    private $sources;
				//Страница по умолчанию
				private $content = "index.php" ;
				//Заголовок  по умолчанию
				private $title = "Главная";
				//Скрипты по умолчанию
				private $scripts=["jquery-3.3.1.min.js","main.js","jquery-ui.js"];
				//Стили по умолчанию
    private $styles = ["style.css","jquery-ui.css"];
				//layout по умолчанию
				private $layout = "share/layout.php";
				//Отображение местоположения
				private $viewPatch=[];
				//Сообщения
				private $messages = [];
				private $htmlForms;
				public $showPatch = true;
				public function PageBuilder(){
								$rootname = $_SERVER['SERVER_NAME'];
        $scheme = $_SERVER['REQUEST_SCHEME'];
        $this->sources = "$scheme://$rootname/source";
								$this->htmlForms = new myHtmlHalper();
				}
				public function addScript($script){
								array_push($this->scripts,$script);
				}
				public function addStyle($Style){
								array_push($this->styles,$Style);
				}
				public function setLayout($layout){
								$this->layout = $layout;
				}
				public function setTitle($title){
								$this->title = $title;
				}
				public function setContent($content){
								$this -> content = $content;
				}
				public function setContentFolder($folder){
								$this->contentFolder = $folder;
				}
				public function build(){
								include_once	$this->layout;
				}
				public function setPatch($array){
								$this->viewPatch = $array;
				}
				public function addPart($title,$href){
								array_push($this->viewPatch,['title'=>$title,'href'=>$href]);
				}
				public function addMessage(string $type,string $message){
								$this->messages[$type] = $message;
				}
				public function Message(string $type):string{
								if(isset($this->messages[$type])){
												return $this->messages[$type];
								}else{
												return "";
								}
								
				}
}



class itstart{
    private $title;
    private $content;
    private $patch;
    private $styles;
    private $scripts;
    private $sources;
				private $pages;


				public function itstart(){
								$this->init();
				}
    public function setTitle($t){
        $this->title = $t;
    }
    public function setContent($c){
        $this->content = $c;
        $this->content();
    }
    public function setPatch($p){
								
        $this->patch = $p;
    }

    public function showPage(){
        if(is_null($this->patch)){ 



								}
												
								else{
												/*$fndPage = false;
												$ptc = explode("/", $this->patch);
												$ptc=array_reverse($ptc);
												$category = array_pop($ptc);
												foreach	($this->pages	as	$key	=>	$page)	{
																if(!strcasecmp("$category.php",$page)){
																				$fndPage=true;
																				break;
																}
												}
												if($fndPage){
																include_once	"modules/pages/$category.php";
												}else{
																//redi
												}*/
								}
    }
    private function init(){
        $rootname = $_SERVER['SERVER_NAME'];
        $scheme = $_SERVER['REQUEST_SCHEME'];
        $this->sources = "$scheme://$rootname/source";
        $this->scripts=["jquery-3.3.1.min.js","main.js","jquery-ui.js","datepicker.js"];
        $this->styles = ["style.css","jquery-ui.css","datepicker.css"];
								$this->pages = [];
								$regexp="/[A-z].php/i";
								foreach	(scandir("modules/pages/",1) as $page){
												if(preg_match($regexp,	$page)) array_push	($this->pages,$page);
								}
								
    }
    private function showIndex(){
								array_push($this->scripts,"slider.js");
        $this->setTitle("Добро пожаловать");
        $this->setContent("home.php");
        
    }
    private function content(){
        include_once "modules/page.php";
        //include_once "pages/$this->content";
    }
    
}


function getAdr($adr){
    $arr = explode("/", $adr);
    $arr=array_reverse($arr);
    var_dump($arr);
    $category = array_pop($arr);
    switch ($category){
        case "articles":break;
        case "groups":break;
        case "users":break;
        case "forums":break;
        default: return false;
    }
    return true;
    //echo $category;
}
function showArticles($comand){
    $content = "articles.php";
    $title = "Все статьи";
    return true;
}
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


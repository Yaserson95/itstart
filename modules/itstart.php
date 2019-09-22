<?php
//Базовые параметры для нормальной работы сайта
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_http_input('UTF-8');
mb_regex_encoding('UTF-8'); 

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
        if(is_null($this->patch)) $this->showIndex();
								else{
												$fndPage = false;
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
												}
								}
    }
    private function init(){
        $rootname = $_SERVER['SERVER_NAME'];
        $scheme = $_SERVER['REQUEST_SCHEME'];
        $this->sources = "$scheme://$rootname/source";
        $this->scripts=["jquery-3.3.1.min.js","main.js","jquery-ui.js"];
        $this->styles = ["style.css","jquery-ui.css"];
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
        default: return false; break;
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


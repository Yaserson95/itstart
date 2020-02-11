<?php
include "share/myHtmlHalper.php";
//Базовые параметры для нормальной работы сайта
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_http_input('UTF-8');
mb_regex_encoding('UTF-8'); 
date_default_timezone_set('Europe/Moscow');

//===========================
function imageFormarter($imgPatch, int $nwidth, int $nheight){
				//Открыть картинку и проверить размеры
				$imgSize = getimagesize($imgPatch);
				//Если не открылось возвращаем пустую строку
				if(empty($imgSize)) return "";
				//Снимаем размеры
				$width = $imgSize[0];
				$height = $imgSize[1];
				//Является ли картинка и маска вертикальными
				$imgVertical = $height>$width;
				$temp = currentUserData()."/temp/temp.png";
				//Если файл не png - создаем временный png файл
				if(exif_imagetype($imgPatch)!=IMAGETYPE_PNG){						
								imagepng(
												imagecreatefromstring(
																file_get_contents($imgPatch)
												), 
												$temp
								);
								$image = imagecreatefrompng($temp);
								//unlink($temp);
				}
				else{
								$image = imagecreatefrompng($imgPatch);
				}
				
				$k=1;
				$x=0;
				$y=0;
				if($imgVertical){
								$k = $nwidth/$width;
								$y = (int)(($height*$k-$nheight)/2);
				}
				else{
								$k = $nheight/$height;
								$x = (int)(($width*$k - $nwidth)/2);
				}
				$image = imagescale($image,	(int)($k*$width),(int)($k*$height));
				$image = imagecrop($image, ["x"=>$x,"y"=>$y,"width" => $nwidth,"height"=>$nheight]);
				if(file_exists($temp)) unlink($temp);
				return $image;
}
function checkImgSize(string $imgPatch,int $width,int $height):bool{
				if(!file_exists($imgPatch)) return false;
				$imgSize = getimagesize($imgPatch);
				if(empty($imgSize)) return false;
				return (($imgSize[0]==$width)&&($imgSize[1]==$height));
}
function copyFile(string $patch,string $newPatch){
				$oldInf = pathinfo($patch);
				$baseName = $oldInf["basename"];
				return newFileName("$newPatch/$baseName");
}

function newFileName(string $patch):string{
				$info = pathinfo($patch);
				$filename = $info['filename'];
				$dir = $info['dirname'];
				$npatch = $patch;
				$ext = $info['extension'];
				if(file_exists($patch)){
								$i=0;
								while(file_exists($dir."/".$filename."_$i.$ext")){
												$i++;
								}
								$npatch = $dir."/".$filename."_$i.$ext";
				}
				return $npatch;
}

function urlFileExist(string $url){
				$Headers = @get_headers($url);
				// проверяем ли ответ от сервера с кодом 200 - ОК
				//if(preg_match("|200|", $Headers[0])) { // - немного дольше :)
				return strripos($Headers[0],"200");			
}

function downloadFile ($URL, $PATH) {
    $ReadFile = fopen ($URL, "rb");
    if ($ReadFile) {
        $WriteFile = fopen ($PATH, "wb");
        if ($WriteFile){
            while(!feof($ReadFile)) {
                fwrite($WriteFile, fread($ReadFile, 4096 ));
            }
            fclose($WriteFile);
        }
        fclose($ReadFile);
    }
}

function downloadImage($url,$patch){
				if(urlFileExist($url)===false){
								return false;
				}
				if(($imginfo = getimagesize($url))!==false){
								$filename = pathinfo($url)["filename"];
								$ext = ltrim($imginfo["mime"],"image/");
								$filename =	strtolower("$patch/$filename.$ext");
								$saved = newFileName($filename);
								if (copy($url, $saved)){
												return $saved;
								}
				}
							return false;
}
function dateDiff(string $date1, string $date2,int $limit=2):string{
				$d1=new DateTime($date1);
				$d2=new DateTime($date2);
				$diff=$d2->diff($d1);
				$i=0;
				$timeDif = "";
				foreach	($diff as $key => $value){
								if($value==0)	continue;
								if($i==$limit)break;
								switch	($key){
												case "y":
																$timeDif.="$value год. ";
																break;
												case "m":
																$timeDif.="$value мес. ";
																break;
												case "d":
																$timeDif.="$value дн. ";
																break;
												case "h":
																$timeDif.="$value час ";
																break;
												case "i":
																$timeDif.="$value мин. ";
																break;
												case "s":
																$timeDif.="$value сек. ";
																break;
								}
								$i++;
				}
				return $timeDif;
}

function RequireData(array $data,string $columns):bool{
				$flag = false;
				$arr = explode(",",$columns);
				foreach	($arr	as	$key	=>	$value)	{
								if(isset($data[$value])){
												$flag=true;
								}
								else{
												$flag=false;
												break;
								}
				}
				return $flag;
}
function getNumberType(string $part):int{
				$page = strtolower($part);
				switch($page){
								case "comment":	return 0;
								case "article": return 1;
								case "discussion": return 2;
								default	: return 10;
				}
}

class getItem{
				public static function Contacts(array $contacts){
								$posts=[
												"-1"=>"Создатель",
												"1"=>"Модератор",
												"2"=>"Администратор"
								];
								$res = "";
								foreach	($contacts AS $value){
												$src = getUsersUrl($value["Nickname"])."/photo.png";
												
												if(!file_exists($_SERVER["DOCUMENT_ROOT"].$src)){
																$src="/source/img/user_default.png";
												}
												$res.="<a class='item user' href='/Users/".$value["Nickname"]."'>"
												."<img src='$src'>"
												."<h2>".$value["Nickname"]." (".$posts[$value["Post"]].")</h2>"
												."<p>".$value["Firstname"]." ".$value["Surname"]."</p>"
												. "</a>";
								}
								return $res;
				}
				public static function Users(array $users){
								$res = "";
								foreach	($users AS $value){
												$src = getUsersUrl($value["Nickname"])."/photo.png";
												if(!file_exists($_SERVER["DOCUMENT_ROOT"].$src)){
																$src="/source/img/user_default.png";
												}
												$res.="<a class='item user' href='/Users/".$value["Nickname"]."'>"
												."<img src='$src'>"
												."<h2>".$value["Nickname"]."</h2>"
												."<p>".$value["Firstname"]." ".$value["Surname"]."</p>"
												. "</a>";
								}
								return $res;
				}
}

//==========================================
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
				//Добавить в голову сайта
				private $head = [];
				//Диалоги
				private $dialogs = [
								"loginform"=>[
												"title"=>"Войти",
												"autorize"=>false
								]
				];
				public $countPages = 0;
				public $pageNumber = 0;
				private $data = [];
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
				public function addHead($head){
								array_push($this->head,$head);
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
								$this->messages[strtolower($type)] = $message;
				}
				public function Message(string $type):string{
								$type=strtolower($type);
								if(isset($this->messages[$type])){
												return "<p class = '$type'>".$this->messages[$type]."</p>";
								}else{
												return "";
								}
								
				}
				public function addData($key,$data){
								$key = strtolower($key);
								$this->data[$key] = $data;
				}
				public function Data($key){
								$key = strtolower($key);
								return $this->data[$key];
				}
				public function Tags(string $tags):array{
								return explode(";",$tags);
				}
				public function addDialog(string $name,array $prop):bool{
								if(isset($this->dialogs[$name])) return false;
								$this->dialogs[$name] = $prop;
								return true;
				}
				public function removeDialog(string $name):bool{
								if(!isset($this->dialogs[$name])) return false;
								unset($this->dialogs[$name]);
								return true;
				}
				public function setPageNavigator(int $number,int $count){
								$this->pageNumber = $number;
								$this->countPages = $count;
				}
				public function pageNavigator(string $part):string{
								if($this->countPages==0) return "";
								$vpages = 10;
								$result = "<div class='pagesnavigator'>Страницы: ";
								$part = trim($part,	"/");
								if($this->pageNumber>0){
												$result .= "<a href = '/$part/".($this->pageNumber-1)."'><<</a>";
								}
								if($this->countPages<=$vpages){
												$N = 0;
												$M = $this->countPages;
								}
								else{
												$n = (int)($this->pageNumber/$vpages);
												$N = $n*$vpages;
												$M = $vpages*($n+1);
												if($M>$this->countPages)$M = $this->countPages;
								}
								$result .= "<span>[";
								for($i=$N;$i<$M;$i++){
												$st="";
												if($i==$this->pageNumber) $st="style = 'font-weight:bold;'";
												$result .= "<a href='/$part/$i' $st>".($i+1)."</a>";
								}
								$result .= "]</span>";
								if($this->pageNumber<$this->countPages-1){
												$result .= "<a href = '/$part/".($this->pageNumber+1)."'>>></a>";
								}
								$result.= "</div>";
								return $result;
				}
								
}
function getRootUrl(){
				return $_SERVER["REQUEST_SCHEME"]."://".$_SERVER["SERVER_NAME"];
}
<?php
include_once	'modules/autorize.php';
include_once	'modules/structures/Users.php';
include_once	'modules/forms/DisplaySettings.php';
class ProfileController{
				private $db;
				private $user;
				private $Users;
				private $pageName = "Index";
				private $pageTitle = "Профиль";
				public const partName = "Profile";
				public const partTitle = "Профиль";
				private $newPage;
				public function ProfileController(){
								if(!IsAutorized())header("Location: /");
								$this->db = new Itstart_db();
								$this->Users = new Users($this->db);
								$this->user = $this->Users->findByNick($_SESSION["Nickname"]);
								$this->newPage = new PageBuilder();
								$this->newPage->setContentFolder("content/".ProfileController::partName);
								$this->newPage->addPart("Главная",	"/");
								$this->newPage->addPart(ProfileController::partTitle,	ProfileController::partName);
				}
				public function Index(array $post = null){
								$this->newPage->addHead("<script type='text/javascript' src='/source/ckfinder/ckfinder.js'></script>");
								$this->newPage->addScript("UserProfile.js");
								//jquery.tablesorter.js jquery.tablesorter.widgets.js
								$this->newPage->addScript("jquery.tablesorter.js");
								$this->newPage->addScript("jquery.tablesorter.widgets.js");
				}
				public function Manage(array $post = null){
								if(!empty($post)){
//												echo var_dump($post);
//												exit();
												$this->user->setMode(User::Update);
												$this->user->Set($post);
												$this->user->setValue("UserId",	$_SESSION["UserId"]);
												$this->user->setValue("Nickname",	$_SESSION["Nickname"]);
												if($this->user->isValid()){
																$this->Users->Edit($this->user);
																$this->newPage->addMessage("message","Данные были обновлены!");
												}
												else {
																$this->newPage->addMessage("Error","Ошибка: неправильно введены данные!");
												}
								}
								$this->newPage->addScript("datepicker.js");
								$this->newPage->addScript("userEdit.js");
								$this->newPage->addStyle("datepicker.css");
								$this->pageName = "manage";
								$this->pageTitle = "Настройки";
								$this->newPage->addData("user",	$this->user);
								unset($this->user);
				}
				public function Logout(array $post = null){
								$_SESSION = array(); 
								session_destroy();
								header("Location: /");
				}
				public function Display(array $post = null){
								$display = new DisplaySettings();
								if(!empty($post)){
												$display->Set($post);
												if($display->isValid()){
																if(setDisplaySettings($display->Get(),$_SESSION["Nickname"])==0){
																				$this->newPage->addMessage("info","Настройки сохранены");
																}
																else{
																				$this->newPage->addMessage("error","Неизвестная ошибка");
																}
												}	
												else	{
																$this->newPage->addMessage("Error","Ошибка: неправильно введены данные!");
												}
								}
								else{
												$display->Set(getDisplaySettings($_SESSION["Nickname"]));
								}
								$this->newPage->addData("display",$display);
								unset($display);
								$this->pageName = "display";
								$this->pageTitle = "Настройка отображения";
				}
				public function Password(array $post = null){
								include_once	'modules/share/UserEditPassword.php';
								$this->pageName = "password";
								$this->pageTitle = "Изменить пароль";
				}
				public function Nickname(array $post = null){
								include_once	'modules/share/UserEditNick.php';
								$this->pageName = "nickname";
								$this->pageTitle = "Изменить ник";
				}
				public function View(array $post = null){
								$this->pageName = "view";
								$this->pageTitle = "Моя станица";
								$this->user->setMode(User::Wiew);
								$photo = $this->user->getPhoto();
								$this->newPage->addScript("UserPage.js");
								$this->newPage->addData("User",	$this->user);
								$this->newPage->addHead("<script type='text/javascript' src='/source/ckfinder/ckfinder.js'></script>");
								$this->newPage->addData("photo",	$photo);
								unset($this->user);
				}
				public function Data(array $post = null):int{
								if(empty($post)){
												return 1;
								}
								if(!RequireData($post,	"Cat,Part")){
												return 2;
								}
								$parts = [
												"my"=>[
																"articles"=>"UserId",
																"discussions"=>"UserId",
																"groups"=>"OwnerId"
												],
												"like"=>[
																"articles"=>"ArtId",
																"discussions"=>"DiscId",
												],
												"dislike"=>[
																"articles"=>"ArtId",
																"discussions"=>"DiscId",
												],
												"hidden"=>[
																"articles"=>"ArtId",
																"discussions"=>"DiscId",
												]
								];
								
								
								$cat = strtolower($post["Cat"]);
								$part = strtolower($post["Part"]);
								//echo "sd";
								if(!isset($parts[$cat][$part])){
												return 3;
								}
								$key = $parts[$cat][$part];
								unset($parts);
								unset($post);
								$where = [];
								switch ($cat){
												case "my":
																$where=[$key=>$_SESSION["UserId"]];
																break;
												case "like":
																$k = "issetMark($key,".getNumberType(rtrim($part,'s')).",".$_SESSION["UserId"].",0)";
																$where=[$k=>1];
																break;
												case "dislike":
																$k = "issetMark($key,".getNumberType(rtrim($part,'s')).",".$_SESSION["UserId"].",1)";
																$where=[$k=>1];
																break;
												case "hidden":
																$k = "issetMark($key,".getNumberType(rtrim($part,'s')).",".$_SESSION["UserId"].",2)";
																$where=[$k=>1];
																break;
												default:
																exit();
																
								}
								echo $this->FindData($part,$where);
								return 0;
				}
				public function FindData(string $part, array $where){
								$elem = null;
								$data = null;
								switch($part){
												case "articles":
																include_once	'modules/structures/Articles.php';
																$articles = new Articles($this->db);
																$elem = new Article();
																$elem->setMode(Article::TableShow);
																$data = $articles->Find(null,$where);
																break;
												case "discussions":
																return "";
																break;
												case "groups":
																return "";
																break;
								}
								$str = "<table class='elemList' cellpadding = '0px' cellspacing = '0px'>";
								$columns = $elem->getViewColumns();
								//Заголовок таблицы
								$str.="<thead><tr>";
								foreach	($columns	as	$value)	{
												$str.="<th>".$elem->getProperty($value,	"alias")."</th>";
								}
								$str.="</tr></thead>";
								//Тело таблицы
								$str .="<tbody>";
								foreach	($data	as	$value)	{
												$str .= "<tr>";
												$elem->setData($value);
												foreach	($columns AS $column){
																$str .="<td>".$elem->getField($column)."</td>";
												}
												$str .= "</tr>";
								}
								$str .= "</tbody></table>";
								return $str;
				}
				public function UploadImage(array $post = null):string{
								if(!isset($post["url"])){
												return json_encode(["Type"=>"error","Text"=>"Не указан URL!"]);
								}
								$uploadPatch = strtolower(getUsersData($_SESSION["Nickname"]))."\images";
								$imgPatch = downloadImage($post['url'],	$uploadPatch);
								if($imgPatch===false){
												return json_encode(["Type"=>"error","Text"=>"Изображение не найдено!"]);
								}
								$info  = pathinfo($imgPatch);
								$baseName = $info["basename"];
								$imgPatch = strtolower(getUsersUrl($_SESSION["Nickname"]))."/images/".$baseName;
								return json_encode(["Type"=>"info","Text"=>$imgPatch]);
				}
				public function set(&$patch){
								$this->Set_Patch($patch);
	
								$this->newPage->setTitle($this->pageTitle);
								$this->newPage->setContent("$this->pageName.php");
								$this->newPage->build();
								$this->newPage->showPatch=true;
								
				}
				public function Set_Patch(array &$patch){
								if(empty($patch)){
												$this->Index();
												
												return 0;
								}
								$page = strtolower(array_pop($patch));
								$count = 0;
								switch	($page){
												case "index":
																$this->Index();
																return 0;
												case "view":
																$this->View();
																break;
												case "manage":
																$this->Manage($_POST);
																break;				
												case "logout":
																$this->Logout($_POST);
																break;
												case "password":
																$this->Password($_POST);
																break;
												case "nickname":
																$this->Nickname($_POST);
																break;
												case "data":{
																if($this->Data($_POST)!=0){
																				header("Location: /".ProfileController::partName);
																}
																exit();
												}
												case "display":
																$this->Display($_POST);
																break;
												case "uploadimage":
																echo $this->UploadImage($_POST);
																exit();
																break;
												case "setphoto":
																echo $this->SetPhoto($_POST);
																exit();
																break;
												default	:
																header("Location: /".ProfileController::partName);
																break;
								}
								if(count($patch)>$count){
												header("Location: /".ProfileController::partName."/".$page);
								}
								$this->newPage->addPart($this->pageTitle,	$this->pageName);
								return $count;
				}
				public function SetPhoto(array $post){
								if(!IsAutorized()) return "";
								if(!isset($post["Image"])){
												return json_encode(["Type"=>"error","Value"=>1]);
								}
								//source/IMG/defaultUser.png
								$pacth = str_replace(getRootUrl(),	"",	$post["Image"]);
								if(strtolower($pacth)=="/source/img/defaultuser.png"){
												return json_encode(["Type"=>"error","Value"=>2]);
								}
								if(strtolower($pacth)==	strtolower(getUsersUrl($_SESSION["Nickname"]))."/photo.png"){
												return json_encode(["Type"=>"error","Value"=>3]);
								}
								if(!file_exists($_SERVER["DOCUMENT_ROOT"].$pacth)){
												return json_encode(["Type"=>"error","Value"=>4]);
								}
								$image = imageFormarter($_SERVER["DOCUMENT_ROOT"].$pacth,160,160);
								imagepng($image,	getUsersData($_SESSION["Nickname"])."/photo.png");
								return json_encode(["Type"=>"info","Value"=>	getUsersUrl($_SESSION["Nickname"])."/photo.png"]);
				}
				
}

if(isset($patch)){
				$profile = new ProfileController();
				$profile->Set($patch);
}

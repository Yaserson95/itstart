<?php
include "modules/Structures/Users.php";
include "modules/Forms/Login.php";
include "modules/Autorize.php";
if(IsAutorized())header("Location: /");;
$db = new Itstart_db();
$partName = "Account";
$newPage = new PageBuilder();
$newPage->setContentFolder("content/$partName");
$pageName = "";
$pageTitle="";
$newPage->addPart("Главная",	"/$partName");

if(isset($patch)){
				if(empty($patch)){
								header("Location: /$partName/Index");
				}else{
								$page =	strtolower(array_pop($patch));
								switch($page){
												case "register":{
																include_once	'modules/share/UserRegister.php';
																break;
												}
												case "index":{
																$pageName = "Index";
																$pageTitle = "Авторизация";
																break;
												}
												case "login":{
																if(empty($_POST)) header("Location: /$partName");
																				$login = new Login($_POST);
																				if($login->isValid()){
																								$data = $login->getData();
																								$arr_login = $db->Login($data["Nickname"],	$data["UserPswrd"]);
																								if(!empty($arr_login)){
																												
																												$_SESSION = $arr_login;
																												echo "0";
																								}else{
																												echo "1";
																								}
																								unset($arr_login);
																				}
																				
																exit();
												}
												default	:{
																header("Location: /$partName");
																break;
												}
								}
				$newPage->setTitle($pageTitle);
								$newPage->setContent("$pageName.php");
								$newPage->addPart($pageTitle,	$pageName);
								$newPage->showPatch=true;
				}
				//Ограничение по уровню
				if(count($patch)>0){
								header("Location: /$partName/$page");
				}
				$newPage->build();
}
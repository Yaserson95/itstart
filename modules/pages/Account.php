<?php
include "modules/Structures/Users.php";

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
																if(!empty($_POST)){
																				$db = new Itstart_db();
																				$Users = new Users($db);
																				$user = new User();
																				$user->setColumnProperty("Priority",	"null",	true);
																				$user->setColumnProperty("UserId",	"null",	true);
																				$user->Set($_POST);
																				if(isset($_POST["Repassword"])){
																										$Users->Register($user,$_POST["Repassword"]);
//																								if(empty($mess)){
//																												$pageName = "index";
//																												$pageTitle = "Авторизация";
//																												$newPage->addMessage("Welcome","Регистрация прошла успешно! Теперь вы можете войти в свой аккаунт!");
//																												break;
//																								}
//																								$newPage->addMessage("Error",	$mess);
																								
																				}
																				else $newPage->addMessage("Error",	"Введите подтверждение пароля");
																}
																$newPage->addScript("register.js");
																$newPage->addScript("datepicker.js");
																$newPage->addStyle("datepicker.css");
																$pageName = "Register";
																$pageTitle = "Регистрация";
																					
															//	}
																break;
												}
												case "index":{
																$pageName = "Index";
																$pageTitle = "Авторизация";
																break;
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
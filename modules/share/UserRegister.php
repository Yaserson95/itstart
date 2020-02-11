<?php
$pageName = "Register";
$pageTitle = "Регистрация";
$User = new User();
$User->setMode(User::Register);
if(!empty($_POST)){
				$User->Set($_POST);
				if($User->isValid()){
								$Users	=	new	Users($db);
								$mess = $Users->Register($User);
								if	(empty($mess))	{
												$pageName	=	"index";
												$pageTitle	=	"Авторизация";
												$newPage->addMessage("Welcome",	"Регистрация прошла успешно! Теперь вы можете войти в свой аккаунт!");
								}
								else $newPage->addMessage("Error",	$mess);
								
				}else $newPage->addMessage("Error",	"Не правильно введены данные!");
}
$newPage->addData("user",$User);
unset($User);
//if	(!empty($_POST))	{
//				$Users	=	new	Users($db);
//				$user	=	new	User();
//				$user->setColumnProperty("Priority",	"null",	true);
//				$user->setColumnProperty("UserId",	"null",	true);
//				$user->Set($_POST);
//				if	(isset($_POST["Repassword"]))	{
//								$mess	=	$Users->Register($user,	$_POST["Repassword"]);
//								if	(empty($mess))	{
//												$pageName	=	"index";
//												$pageTitle	=	"Авторизация";
//												$newPage->addMessage("Welcome",	"Регистрация прошла успешно! Теперь вы можете войти в свой аккаунт!");
//
//								}
//								$newPage->addMessage("Error",	$mess);
//				}	else
//								$newPage->addMessage("Error",	"Введите подтверждение пароля");
//}else{}
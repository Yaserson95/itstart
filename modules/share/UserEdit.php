<?php
$Users = new Users($db);
$user = new User($_POST);
$user->setMode(User::Update);
$user->setValue("UserId",	$_SESSION["UserId"]);
$user->setValue("Nickname",	$_SESSION["Nickname"]);
if($user->isValid()){
				$Users->Edit($user);
				$newPage->addMessage("message","Данные были обновлены!");
}
else {
				$newPage->addMessage("Error","Ошибка: неправильно введены данные!");
}
$newPage->addData("user",	$user);
unset($user);
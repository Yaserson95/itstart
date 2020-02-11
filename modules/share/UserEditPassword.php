<?php
include_once	'modules/forms/security.php';
$password = new Password();
$password->setValue("UserId",	$_SESSION["UserId"]);
if(!empty($post)){
				$password->Set($post);
				if($password->isValid()){
								$data = $password->getData(true);
								$key = (int)$nick->getValue("UserId");
								$res = $this->db->Users_ChangePassword($key,$data["UserPswrd"],$data["NewPassword"]);
								if($res!=0){
												$this->newPage->addMessage("error","Текущий пароль введён неправильно!");
								}
								else{
												$this->newPage->addMessage("message","Ваш пароль изменён успешно!");
								}
								unset($data);								
				}
				else{
								$this->newPage->addMessage("error","Неправильные данные");
				}
}
else{
				$password->setValue("Nickname",	$_SESSION["Nickname"]);
}
$this->newPage->addData("password",$password);
unset($password);
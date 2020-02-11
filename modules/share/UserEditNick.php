<?php
include_once	'modules/forms/security.php';
$nick = new Nick();
$nick->setValue("UserId",	$_SESSION["UserId"]);
if(!empty($post)){
				$nick->Set($post);
				if($nick->isValid()){
								$data = $nick->getData();
								$key = (int)$nick->getValue("UserId");
								if($data["Nickname"]!==$_SESSION["Nickname"]){
												$res = $this->db->Users_ChangeNick($_SESSION["Nickname"],$data["Nickname"],$data["UserPswrd"]);
												if($res!=0){
																echo $res;
																$err = "Неизвестная ошибка";
																switch	($res){
																				case -1:
																								$err = "Ошибка БД";
																								break;
																				case 1:
																								$err = "Неправильный пароль";
																								break;
																				case 2:
																								$err = "Пользователь с ником '".$data["Nickname"]."' уже существует!";
																								break;
																}
																$this->newPage->addMessage("error",$err);
												}
												else{
																$this->newPage->addMessage("message","Ваш ник/логин изменён успешно!");
																$old = getUsersData()."/".$_SESSION["Nickname"];
																$new = getUsersData()."/".$data["Nickname"];
																if(!rename($old,$new)){
																				$res = $this->db->Users_ChangeNick($key,$data["Nickname"],$data["UserPswrd"]);
																				$this->newPage->addMessage("error","Ошибка: Не возможно переименовать папку пользователя!");
																}
																else{
																					$_SESSION["Nickname"] = $data["Nickname"];
																}
															
												}
								}
								unset($data);					
				}
				else{
								$this->newPage->addMessage("error","Неправильные данные");
				}
}
else{
				$nick->setValue("Nickname",	$_SESSION["Nickname"]);
}
$this->newPage->addData("nick",$nick);
unset($nick);
				
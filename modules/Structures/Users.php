<?php
include "DbTable.php";
include "modules/Forms/User.php";
class Users extends DbTable{
				protected $tableName="Users";
				public function Add(User $user){
								$valid = $user->isValid();
								if($valid){
												$result = $this->addRow($user->Get());
												return $result;
								}
								return $valid;
				}
				public function findByNick(string $nickname):	User{
								$user = new User();
								$userArr = $this->Find(null,['Nickname'=>$nickname]);
								//echo var_dump($userArr);
								if(!empty($userArr)){
												$user->Set($userArr);
								}
								return $user;
				}
				public function Register(User $user,string $repassword):string{
								if(!$user->isValid()){
												return "Введены неправильные данные!";
								}
								$repassword = md5($repassword);//Зашифровать пароль
								$repassword = strrev($repassword);//Перевернуть для надёжности
								if($user->getValue("UserPswrd")!=$repassword){
												return "Пароль должен совпадать с его подтверждением!";
								}
								if(!$this->findByNick($user->getValue("Nickname"))->isEmpty()){
												return "Пользователь с таким ником уже существует!";
								}
								if(!$this->Add($user)) return "Не удалось зарегистрировать пользователя";
								return "";
				}
				
}
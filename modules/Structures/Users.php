<?php
include_once "DbTable.php";
include_once  "modules/Forms/User.php";
class Users extends DbTable{
				protected $tableName="Users";
				public function Add(User $user){
								$valid = $user->isValid();
								if($valid){
												$result = $this->Insert($user->Get());
												return $result;
								}
								return $valid;
				}
				public function GetUsers($values = null, $where = null):	array{
								$users_arr = parent::Find($values,$where);
								$users = [];
								foreach	($users_arr	as	$user)	{
												$u = new User();
												$u ->setData($user);
												array_push($users,$u);
								}
								return $users;
				}

				public function findByNick(string $nickname):	User{
								$userArr = $this->Find(null,['Nickname'=>$nickname]);
								$User = new User();
								if(!empty($userArr)){
												$User->setData($userArr[0]);
								}
								return $User;
				}
				public function findById(int $UserId){
								$userArr = $this->Find(null,['UserId'=>$UserId]);
								//echo var_dump($userArr);
								if(!empty($userArr)){
												$User = new User();
												$User->setData($userArr[0]);
												return $User;
								}
								return null;
				}
				
				public function Register(User &$user):string{
								if(!$user->isValid()){
												return "Введены неправильные данные!";
								}
								if($user->getMode()!=User::Register){
												return "Введены неправильные данные!";
								}
								if(!$this->findByNick($user->getValue("Nickname"))->isEmpty()){
												return "Пользователь с таким ником уже существует!";
								}
								$data = $user->getData();
								if(!$this->Insert($data)) return "Не удалось зарегистрировать пользователя";
								return "";
				}
				public function Edit(User $user){							
								return $this->Update($user->getData(),$user->getKeys());
				}
				
}
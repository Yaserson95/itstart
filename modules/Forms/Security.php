<?php
include_once	'Form.php';
include_once	'modules/Autorize.php';
abstract class Security extends Form{
				protected $ignore = ["UserId"];
				protected $columns =[
								"UserId" => [
												"alias"=> "Ид",
												"type"=> "key",
												"null"=> false,
												"input"=> "hidden"
								],
								"UserPswrd"=>[
												"alias" => "Пароль",
												"type" => "password",
												"null"=> false,
												"input" => "password",
								]
				];
				public function Security(array $data = null){
								if(!IsAutorized()) exit();
								parent::Form($data);
				}

}
class Nick extends Security{
				private $mata=[
								"Nickname" =>[
												"alias" => "Ник/логин",
												"type" => "string",
												"null" => false,
												"preg" => "/^([A-z0-9_.]*)$/ui",
												"input" => "text"
								]
				];
				public function Nick(array $data =null){
								$this->columns = array_merge($this->mata,$this->columns);
								parent::Security($data);
				}
}


class Password extends Security{
				private $mata=[
								"NewPassword"=>[
												"alias" => "Новый пароль",
												"type" => "password",
												"null"=> false,
												"input" => "password",
												"errmess"=>[10=>"Пароль должен совпадать с его поддтверждением!"]
								],
								"RePassword"=>[
												"alias" => "Повторите пароль",
												"type" => "password",
												"null"=> false,
												"input" => "password",
												"errmess"=>[10=>"Пароль должен совпадать с его поддтверждением!"]
								]
				];
				public function Password(){
								parent::Security();
								$this->columns["UserPswrd"]["alias"] = "Текущий пароль";
								$this->columns = array_merge($this->columns,$this->mata);
				}
				public function isValid():	bool	{
								parent::isValid();
								if($this->row["RePassword"]!=$this->row["NewPassword"]){
												$this->errors["RePassword"] = 10;
												$this->errors["NewPassword"] = 10;
								}
								return empty($this->errors);
				}
}
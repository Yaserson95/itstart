<?php

include_once	'Form.php';
class	User	extends	Form	{
				protected	$columns	=	[];
				public const Register = 2;
				public const Wiew = 7;
				public	function	User(array	$user	=	null)	{
								$this->setMetaData(__CLASS__);
								parent::Form($user);		
				}
				public function setMode(int	$mode)	{
								parent::setMode($mode);
								switch($mode){
												case User::Update: $this->ignore=["UserPswrd","UserId","Priority","Nickname"];
																break;
												case User::Register:{ 
																$this->ignore=["UserId","Priority","Gender","City","About","Birth"];
																$this->columns["Repass"] = $this->columns["UserPswrd"];
																$this->columns["Repass"]["alias"] = "Повторите пароль";
																break;
												}
												case User::Wiew: $this->ignore=["UserPswrd"]; 
																break;
												default: break;
								}
				}
				public function isValid():	bool	{
								parent::isValid();
								if($this->mode==User::Register){
												if($this->row["Repass"]!=$this->row["UserPswrd"]){
																$this->errors["Repass"] = 10;
																$this->errors["UserPswrd"] = 10;
												}
								}
								return empty($this->errors);
				}
				public function getData(bool $igNull = false):	array	{
								if(isset($this->row["Repass"])){
												unset($this->row["Repass"]);
								}
								return parent::getData($igNull);
				}
				public function getPhoto():string{
								if($this->isEmpty()) return "";
								$photo  = "/source/IMG/defaultUser.png";
								if(file_exists(getUsersData($this->getValue("Nickname"))."/photo.png")){
												$photo = getUsersUrl($this->getValue("Nickname")."/photo.png");
												
								}
								return $photo;
				}
}

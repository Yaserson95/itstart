<?php
include	'Form.php';
class User extends Form{
				protected $columns = [
								"About"=>[
												"type"=>"string",
												"null"=>true,
												"preg"=>"/^([A-zА-я \".?! ]*)$/ui",
												"input"=>"textarea"
								],
								"Birth"=>[
												"type"=>"string",
												"null"=>true,
												"preg"=>"/^([0-9]{2}.[0-9]{2}.[0-9]{4})$/ui",
												"input"=>"text"
								],
								"City"=>[
												"type"=>"string",
												"null"=>true,
												"preg"=>"/^([A-zА-я]*)$/ui",
												"input"=>"text"
								],
								"Email"=>[
												"type"=>"string",
												"null"=>false,
												"preg"=>"/^([A-z0-9_.]*)@([A-z.]*)$/ui",
												"input"=>"text"
								],
								"Firstname"=>[
												"type"=>"string",
												"null"=>false,
												"preg"=>"/^([A-zА-я]*)$/ui",
												"input"=>"text"
								],
								"Gender"=>[
												"type"=>"bool",
												"null"=>true,
												"input"=>"radio"
								],
								"Nickname"=>[
												"type"=>"string",
												"null"=>false,
												"preg"=>"/^([A-z0-9_.]*)$/ui",
												"input"=>"text"
								],
								"Priority"=>[
												"type"=>"int",
												"null"=>false,
												"input"=>"text"
								],
								"Surname"=>[
												"type"=>"string",
												"null"=>false,
												"preg"=>"/^([A-zА-я]*)$/ui",
												"input"=>"text"
								],
								"UserId"=>[
												"type"=>"int",
												"null"=>false,
												"input"=>"text"
								],
								"UserPswrd"=>[
												"type"=>"string",
												"null"=>false,
												"input"=>"password",
												"preg"=>'/(?=[-_a-zA-Z0-9]*?[A-Z])(?=[-_a-zA-Z0-9]*?[a-z])(?=[-_a-zA-Z0-9]*?[0-9])[-_a-zA-Z0-9]{6,}/'
								]
				];
				
				public function User(array $user=null){
								if($user!=null) $this->Set($user);
				}
				public function setValue(string $key,$value):int{
								switch ($key){
												case "UserPswrd":{
																if(empty($value)) return 1;
																if(!preg_match($this->columns[$key]["preg"],$value)) return 4;
																$pass = md5($value);
																$pass = strrev($pass);
																$this->row[$key] = $pass;
																return 0;
												}
												case "Gender":{
																if($value=="Male"){
																				$this->row["Gender"]=true;
																}else{
																				$this->row["Gender"]=false;
																}
																return 0;
												}
												default	:{
																return Form::setValue($key,$value);
												}
												
								}
				}
				public function getHtmlField(string $key){
								$this->row["UserPswrd"]="";
								if($key=="Gender"){
												$male  = new InputHelper("radio",$key,"Male");
												$female  = new InputHelper("radio",$key,"Female");
																if($this->row["Gender"]){
																				$male->addAtribute("checked",	"checked");
																}else{
																				
																				$female->addAtribute("checked",	"checked");
																}
																echo "<p>".$male->build()
																				."<label for ='male'>Мужской</label></p>";
																echo "<p>".$female->build()
																				."<label for='female'>Женский</label></p>";
								}else{
												Form::getHtmlField($key);
								}
				}

}
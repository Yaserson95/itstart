<?php
abstract class Form{
				protected $row = [];
				protected $errors = [];
				protected $columns = [];
				private function error(int $n):string{
								switch	($n){
												case 1: return "Отсутствует значение!";
												case 2: return "Поле не должно быть пустым!";
												case 3: return "Поле не должно быть пустым!";
												case 4: return "Данные не соответствуют правилам!";
												default	: return "";
								}
				}
				public function Set(array $insert){
								foreach	($this->columns	as	$key	=>	$meta)	{
												$err=0;
												if(isset($insert[$key])){
																$err = $this->setValue($key,$insert[$key]);
												}	else	{
																if(!$meta["null"])$err=1;
												}
												if($err!=0){
																$this->errors[$key] = $err;
												}
								}
				}
				public function Get(){
								return $this->row;
				}
				public function getValue(string $key){
								if(isset($this->row[$key])){
												return $this->row[$key];
								}
								else{
												return "";
								}
				}
				public function getHtmlField(string $key){
								if(isset($this->columns[$key])){
												$value = $this->columns[$key];
												$input  = new InputHelper($value["input"],$key,null);
												if(!$value["null"])$input->addAtribute("required",	"required");
												if(isset($this->row[$key])) $input->setValue	($this->row[$key]);
												echo $input->build();
												if(isset($this->errors[$key])){
																if($value["null"]||$this->errors[$key]==4){
																				echo "<p class='error'>".$this->error($this->errors[$key])."</p>";
																}
												}
								}
				}
				public function isValid():bool{
								return empty($this->errors);
				}
				public function setColumnProperty(string $column,string $property, $value){
								$this->columns[$column][$property] = $value;
				}
				public function setValue(string $key,$value):int{
								if(isset ($this->columns[$key])){
												$meta = $this->columns[$key];
												if($value==null||empty($value)){
																if(!$meta["null"]){
																				return 2;
																}
																return 0;
												}
												if(isset($meta["preg"])){
																if(!preg_match($meta["preg"],	$value)) return 4;
												}
												settype($value,	$meta["type"]);
												$this->row[$key] = $value;
												return 0;
								}else return 1;
				}
				public function isEmpty():bool{
								return empty($this->row);
				}
				
}
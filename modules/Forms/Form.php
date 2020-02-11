<?php
abstract class Form{
				public const Standart = 0;
				public const Update = 1;
				public const Create = 3;
				public const Delete = 4;
				protected $mode=0;
				//Данные формы
				protected $row = [];
				//Список ошибок в данных по каждому столбцу
				protected $errors = [];
				//Список допустимых столбцов
				protected $columns = [];
				//Игнорируемые столбцы
				protected	$ignore=[];
				protected $keys = [];
				private $types_preg=[
								"password"=>"/(?=[-_a-zA-Z0-9]*?[A-Z])(?=[-_a-zA-Z0-9]*?[a-z])(?=[-_a-zA-Z0-9]*?[0-9])[-_a-zA-Z0-9]{6,}/u",
								"email"=>"/^([A-z0-9_.]*)@([A-z.]*)$/ui",
								"date"=>"/^([0-9]{2}.[0-9]{2}.[0-9]{4})$/ui",
								"datetime"=>"/^([0-9]{2}.[0-9]{2}.[0-9]{4} [0-9]{2}:[0-9]{2}:[0-9]{2})$/ui"
				];
				private function error(int $n,$current=null):string{
								switch	($n){
												case 1: return "Отсутствует значение!";
												case 2: return "Поле не должно быть пустым!";
												case 3: return "Данные не соответствуют правилам!";
												case 4: return "Данные не из списка!";
												case 5: return "Некорректно введён адрес электронной почты!";
												case 6: return "Пароль должен быть не менее 6 символов и содержать в себе хотя бы 1 большой и 1 маленький латинский символ и 1 цифру.";
												case 7: return "Не правильно введена дата!";
												case 8: return "Не указаны значения для списка!";
												case 9: return "Указаного значения нет в списке!";
												case 10: return "В строке больше чем ".$current["length"]." cимволов!";
												case 20: return "Значение должно быть целым числом";
												default	: return "Неизвестная ошибка: $n";
								}
				}
				protected function type_set($value,string $key,string $type){
								settype($value,	$type);
								$this->row[$key] = $value;
								
				}
				protected function setMetaData(string $formname){
								$jsonPatch = __DIR__."\\Metadata\\$formname.json";
								if(file_exists($jsonPatch)){
												$json =  file_get_contents($jsonPatch);
												$meta = (array)json_decode($json);
												foreach	($meta	as	$key	=>	$value)	{
																$meta[$key] = (array)$value;
												}
												$this->columns=$meta;
								}
				}
				
				public function Form(array $row = null){
								if($row!=null)	$this->Set($row);
				}
				//Получение данных от пользователя
				public function Set(array $insert){
								foreach	($insert	as	$key	=>	$value)	{
												$this->setValue($key,	$value);
								}
				}
				//Получение данных из БД
				public function setData(array $data){
								foreach	($this->columns	as	$key	=>	$column)	{
												if(!isset($data[$key]))	continue;
												
												switch ($column["type"]){
																case "password": 
																				$this->row[$key] = ""; 
																				break;
																case "date": 
																				$this->row[$key] = date("d.m.Y",strtotime($data[$key])); 
																				break;
																case "datetime":
																				$this->row[$key] = date("d.m.Y H:i:s",strtotime($data[$key])); 
																				break;
																case "int": 
																				$this->setValue($key,(int)$data[$key]);
																				break;
																case "bool":{
																				$this->setValue($key,(int)$data[$key]);
																				break;
																}
																default : 
																				$this->setValue($key, $data[$key]); 
																				break;
												}
								}
				}
				//Выдача данных для пользователя
				public function Get():array{
								return $this->row;
				}
				//Выдача подготовленных для БД данных
				public function getData(bool $igNull = false):array{
								$data = [];
								foreach	($this->row	as	$key	=>	$value)	{
												if(in_array($key,	$this->ignore)){
																continue;
												}
												$format = $this->columns[$key];
												if(!$igNull&&empty($value)&&!is_numeric($value)){
																$data[$key] =null;
																continue;
												}
												switch($format["type"]){
																//Делаем все даты в американском формате
																case "date":{
																				$data[$key] = date("Y-m-d",strtotime((string)$value));
																				break;
																}
																case "datetime":{
																				$data[$key] = date("Y-m-d H:i:s",strtotime((string)$value));
																				break;
																}
																//Шифруем пароль и переворачиваем для надёжности
																case "password":{
																				$data[$key] = md5($value);
																				$data[$key] = strrev($data[$key]);
																				break;
																}
																case "key": break;
																case "html": $data[$key] = $value;
																				break;
																default	:{
																				$data[$key] = htmlspecialchars($value);
																				break;
																}
												}
								}
								return $data;
				}
				public function getViewColumns():array{
								if(!empty($this->ignore)){
												return array_diff($this->getColumns(),$this->ignore);
								}else return $this->getColumns(); 
				}
				public function getValue(string $key){
								if(isset($this->keys[$key])){
												return $this->keys[$key];
								}
								elseif(isset($this->row[$key])){
												return $this->row[$key];
								}
								else{
												return "";
								}
				}
				public function getColumns():array{
								return array_keys($this->columns);
				}
				public function getProperty(string $colName,string $property){
								$value="";
								if(isset($this->columns[$colName][$property])){
												$value = $this->columns[$colName][$property];
								}
								return $value;
				}
				//Автоматическое отображение поля
				public function getHtmlField(string $key):string{
								//Если нет такого поля возвращаем пустую строку
								if(!isset($this->columns[$key])) return "";
								$pr = &$this->columns[$key];
								$type = $pr["type"];
								$input = $pr["input"];
								$field = new InputHelper($input,$key,$this->getValue($key));
								$required = false;
								$atributes = [];
								$inner = "";
								if(isset($pr["null"])){
												$required = !$pr["null"];
								}
								if(isset($pr["atributes"])){
												$atributes = (array)$pr["atributes"];
								}
								if(isset($pr["id"])){
												$atributes["id"] = $pr["id"];
								}
								if(isset($pr["inner"])){
												$inner = "<p>".$pr["inner"]."</p>";
								}
								if($input==="select"){
												if(isset($pr["options"])){
																$field->setData((array)$pr["options"]);
												}else return "";
								}
								if($required){
												$atributes["required"] = "required";
								}
								$field->setAtributes($atributes);
									
								//Если есть ошибки:
								if(isset($this->errors[$key])){
												$field->addAtribute("style",	"border: 1px solid red");
								}
								return "<div class='input_field'>".$field->build().$this->getError($key,	$this->columns[$key]).$inner."</div>";
				}
				public function getField(string $key):string{
								//Если нет поля - вернуть пустую строку
								//if(!isset($this->row[$key]))return "";
								//Проверка типов
								$value = $this->getValue($key);
								$meta = $this->columns[$key];
								switch($meta["type"]){
												case "select":{
																$options = (array)$meta["options"];
																if(isset($meta["options"])){
																				return $options[$value];
																}else{
																				return $value;
																}
												}
								default	: return $value;
								}
				}
				public function printHtmlField(string $key){
								echo $this->getHtmlField($key);
				}
				
				//Проверка полей на корректность
				public function isValid():bool{
								foreach	($this->columns	as	$key	=>	$column)	{
												//Если поле игнорируется из за выбранного режима
												if(!empty($this->ignore)){
																if(in_array($key,	$this->ignore)){
																				continue;
																}
												}
												//Если поле отсутствует...
												$value = null;
												if(isset($this->row[$key])){
																$value = $this->row[$key];
												}
												elseif(isset($this->keys[$key])){
																$value = $this->keys[$key];
												}
												else{
																//Но оно должно быть
																if(!$column["null"])	$this->errors[$key] = 1;
																continue;
												}
												//Если поле есть, и оно пустое и не является числом...
												if(empty($value)&&!is_numeric($value)){
																//И нужное
																if(!$column["null"])	$this->errors[$key] = 2;
																continue;
												}
												if(isset($column["length"])){	
																if(mb_strlen($this->row[$key])>(int)$column["length"]){
																				$this->errors[$key] = 10;
																				continue;
																}
												}
												
												//Проверка для типов
												$Type = strtolower($column["type"]);
												switch($Type){
																//Проверка корректности мыла
																case "email":{
																				$preg=preg_match($this->types_preg["email"],	$value);
																				if(!$preg) $this->errors[$key] = 5;
																				break;
																}
																//Проверка пароля
																case "password":{
																				$preg=preg_match($this->types_preg["password"],	$value);
																				if(!$preg) $this->errors[$key] = 6;
																				break;
																}
																//Проверка даты
																case "date":{
																				$preg=preg_match($this->types_preg["date"],	$value);
																				if(!$preg) $this->errors[$key] = 7;
																				break;
																}
																//Проверка для списка выбора
																case "select":{
																				//echo var_dump($column["options"]);
																				if(isset($column["preg"])){
																				//Проверка наличия регулярки для поля и её соблюдение
																								if(!preg_match($column["preg"],$value)){
																												$this->errors[$key] = 3;
																												break;
																								}
																								//Если нет значений списка
																								if(!isset($column["options"])){
																												$this->errors[$key] = 8;
																												break;
																								}
																								//Если ввели данные, которых нет в списке
																								if(!isset($column["options"][$value])){
																												$this->errors[$key] = 9;
																												break;
																								}
																				}
																				break;
																}
																case "string":{
																				//Проверка регулярок для строк
																				if(isset($column["preg"])){
																								if(!preg_match($column["preg"],$value)){
																												$this->errors[$key] = 3;
																								}
																				}
																				break;
																}
																case "int":{
																				if(!is_int($value)) $this->errors[$key] = 20;
																				break;
																}
												default	: break;
												}
								}
								//echo var_dump($this->errors);
								return empty($this->errors);
				}
				public function setColumnProperty(string $column,string $property, $value){
								$this->columns[$column][$property] = $value;
				}
				public function getKeys(){
								return $this->keys;
				}
				public function setValue(string $key,$value):bool{
								if(isset($this->columns[$key])){
												//$types = ["password","select","date","datetime","email","html","image"];
												switch	($this->columns[$key]["type"]){
																case "key": 
																				$this->keys[$key] = $value; 
																				break;
																case "password": $this->type_set($value,	$key,	"string"); break;
																case "select": $this->type_set($value,	$key,	"string"); break;
																case "date": $this->type_set($value,	$key,	"string"); break;
																case "datetime": $this->type_set($value,	$key,	"string"); break;
																case "email": $this->type_set($value,	$key,	"string"); break;
																case "html": $this->type_set($value,	$key,	"string"); break;
																case "image": $this->type_set(strtolower($value),	$key,	"string"); break;
																default : $this->type_set($value,	$key,	$this->columns[$key]["type"]); break;
												}
												return true;
								}
								return false;
				}
				public function setMode(int $mode){
								$this->mode = $mode;
				}
				public function getMode():int{
								return $this->mode;
				}
				public function isEmpty():bool{
								return empty($this->row);
				}
				public function getError(string $key,&$current=null):string{
								if(isset($this->errors[$key])){
												$err = $this->error($this->errors[$key],$current);
												if(isset($this->columns[$key]["errmess"])){
																$errmess = (array)$this->columns[$key]["errmess"];
																if(isset($errmess[$this->errors[$key]])){
																				$err = $errmess[$this->errors[$key]];
																}
												}
												return "<span class = 'tooltiptext'>$err</span>";
								}
								else return "";
				}
				public function getErrors(){
								foreach	($this->errors as $key=>$value){
												echo $this->columns[$key]['alias'].":";
												echo $this->error($value)."<br/>";
								}
				}
				public function getColumnsNames():array{
								$cols = [];
								foreach	($this->columns AS $key=>$val){
												$cols[$key]=$val["alias"];
								}
								return $cols;
				}
}
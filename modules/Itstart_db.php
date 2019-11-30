<?php
class	Itstart_db	{
				//Имя БД
				private $db_name = "itstart";
				//Хост БД
				private $db_host = "localhost";
				//Имя пользователя БД
				private $db_user = "Itstart_user";
				//Пароль для пользователя БД
				private $db_password = "admin_hello";
				private $db;
				public function Itstart_db(){
								//Подключение к БД
								$this->db = mysqli_connect($this->db_host,$this->db_user,$this->db_password,$this->db_name);
								$this->db->set_charset("utf-8");
				}
				public function isConnect(){
								return !$this->db->connect_errno;
				}
				public function Disconnect(){
								if($this->isConnect()){
												$this->$db->close();
								}
				}
				public function getDb(){
								return $this->db;
				}
}


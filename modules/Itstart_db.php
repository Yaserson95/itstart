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
								$this->db->set_charset("utf8");
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
				public function Users_ChangeNick(string $Oldnick, string $Newnick, string $UserPass):int{
								$query = "call Users_ChangeNick('$Oldnick','$Newnick','$UserPass')";
								//echo $query;
								$result = $this->db->query($query);
								if($result&&$result->num_rows>0){
												return (int)$result->fetch_array()[0];
								}
								return -1;
				}
				public function Users_ChangePassword(int $id, string $UserPass,string $newpass):int{
								$result = $this->db->query("call Users_ChangePassword('$id','$UserPass','$newpass')");
								return (int)$result->fetch_all ()[0][0];
				}
				public function Login(string $Nickname,string $UserPswd):array{
								$result = $this->db->query("call Login('$Nickname','$UserPswd')");
								if($result){
												if($result->num_rows!==0){ 
																return $result->fetch_assoc();
												}
								}	
								return [];
				}
				public function ChildComments(int $ObjectId, int $type):int{
								$query = "SELECT ChildComments($ObjectId,$type)";
								$nChild = -1;
								$res = $this->db->query($query);
								if($res){
												$nChild = (int)$res->fetch_array()[0];
								}
								return $nChild;
				}
}


<?php
include "modules/Itstart_db.php";
abstract class DbTable{
				private $db;
				protected $tableName="";
				public function DbTable(Itstart_db $base){
							if($base->isConnect()){
												$this->db = $base->getDb();
							}
				}
				public function setDb(mysqli &$db){
								$this->db = $db;
				}
				protected function addRow(array $row):bool{
								$keys = array_keys($row);
								$values = array_values($row);
								$query = "INSERT INTO $this->tableName(".	implode(",",	$keys).')VALUES';
								$query.="('".implode("','",	$values)."')";
								return $this->db->query($query);
				}
				public function Find(array $values = null, array $where = null){
								$cols = "*";
								if(!is_null($values)) $cols = implode(", ",$values);
								$query = "SELECT $cols FROM $this->tableName";
								if(!is_null($where)){
												$and = false;
												$query.=" WHERE \n";
												foreach	($where	as	$key	=>	$value)	{
																if($and)$query.=" AND ";
																$query.="$key = '$value'";
																$and = true;
												}
								}
								$finded = [];
								$result = $this->db->query($query);
								if($result->num_rows>0)
								{
												$finded=	$result->fetch_assoc();
								}
								return $finded;
				}
				public function Edit(array $set, array $where = null){
								$query = "UPDATE $this->tableName SET ";
								$f = false;
								foreach	($set	as	$key	=>	$value)	{
												if($f) $query.=", ";
												$query.="$key = '$value'";
												$f=true;
								}
								if(!is_null($where)){
												$and = false;
												$query.=" WHERE \n";
												foreach	($where	as	$key	=>	$value)	{
																if($and)$query.=" AND ";
																$query.="$key = '$value'";
																$and = true;
												}
								}
								return $this->db->query($query);
				}
				public function Remove(array $where){
								$query = "DELETE FROM $this->tableName";
								$and = false;
								$query.=" WHERE \n";
								foreach	($where	as	$key	=>	$value)	{
												if($and)$query.=" AND ";
												$query.="$key = '$value'";
												$and = true;
								}
								return $this->db->query($query);
				}
				protected function setTable($tableName){
								$this->tableName = $tableName;
				}
				
}
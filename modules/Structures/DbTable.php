<?php
include "modules/Itstart_db.php";
abstract class DbTable{
				protected $db;
				protected $tableName="";
				protected $orderby = "";
				protected $likes = "";
				protected $limit = "";
				public function DbTable(Itstart_db $base){
							if($base->isConnect()){
												$this->db = $base->getDb();
							}
							if(empty($this->tableName))
												$this->tableName = __CLASS__;
				}
				public function Count(array $where=null):int{
								$query = "SELECT COUNT(0) FROM $this->tableName ".$this->query_where($where);
								$result = $this->db->query($query);
								$n=0;
								if($result&&$result->num_rows>0){
												$n = (int)$result->fetch_array()[0];
								}
								return $n;
				}
				public function Status():array{
								$query = "SHOW TABLE STATUS LIKE '$this->tableName' ";
								if(($result = $this->db->query($query))){
												return $result->fetch_assoc();
								}
								return [];
				}
				public function addLike(string $column,string $find,bool $req=false){
								if(!empty($this->likes)){
												$this->likes.=" OR ";
								}
								$str = "";
								if($req){
												$str.="%";
								}
								$str.="$find%";
								$this->likes.="$column LIKE('$str')";
				}
				public function SearchBy(array $columns, string $find, bool $req=false){
								foreach	($columns as $value){
												$this->addLike($value,	$find, $req);
								}
				}
				public function removeLikes(){
								$this->likes="";
				}
				public function getStatus(string $stat):	string{
								$array = $this->Status();
								if(isset($array[$stat])){
												return $array[$stat];
								}
								return "";
				}

				protected function query_set(array $set):string{
								$f = false;
								$query = "";
								foreach	($set	as	$key	=>	$value)	{
												if($f) {
																$query.=",\n";
												}
												$f=true;
												//Если значение пустое вставляем null
												if(is_numeric($value)){
																$query.="$key = $value";
																continue;
												}
												if(empty($value)){
																$query.="$key = null";
												}else{
																$query.="$key = '$value'";
												}
												
								}
								return $query;
				}
				protected function query_where($where=null):string{
								if(empty($where)){
												if(empty($this->likes)){
																return "";
												}
												else{
																return "WHERE $this->likes";
												}
								}
								$query = "WHERE ";
								if(is_array($where)){
												$and = false;
												foreach	($where	as	$key	=>	$value)	{
																if($and)$query.=" AND ";
																$query.="$key = '$value'";
																$and = true;
												}
								}
								elseif(is_string($where)){
												$query.=$where;
								}
								else{
												return "";
								}
								if(!empty($this->likes)){
												$query.=" AND ($this->likes)";
								}
								return $query;
				}
				public function setDb(mysqli &$db){
								$this->db = $db;
				}
				public function Insert(array $row){
								$keys = array_keys($row);
								$values = array_values($row);
								$query = "INSERT INTO $this->tableName (".	implode(",",	$keys).') VALUES';
								$query.="('".implode("','",	$values)."')";
								return $this->db->query($query);
				}
				public  function OrderBy(array $orders){
								$fl = false;
								$this->orderby = "ORDER BY";
								foreach	($orders AS $column=>$order){
												if($fl) $this->orderby .= ",";
												else{
																$fl = true;
												}
												$this->orderby .= " $column ";
												if($order) $this->orderby .= "ASC";
												else $this->orderby .= "DESC";
								}
				}
				private function columnsLst($columns=null):string{
								if(empty($columns)) return "*";
								if(is_array($columns)){
												return implode(", ",$columns);
								}
								elseif(is_string($columns)){
												return $columns;
								}
								return "*";
				}

				public function Limit(int $up,int $down=0){
								$this->limit = "LIMIT $down,$up";
				}
				public function Find($values = null, $where = null):array{
								if($this->db->connect_errno) return [];
								$query = "SELECT ".$this->columnsLst($values)." FROM $this->tableName ";
								$query.=$this->query_where($where);
								if(!empty($this->orderby)){
												$query .=" $this->orderby";
								}
								if(!empty($this->limit)){
												$query .=" $this->limit";
								}
								$result = $this->db->query($query);
								//echo $query;
								if($result){
												return $result->fetch_all(MYSQLI_ASSOC);
								}

								return [];
				}
				public function Update(array $set, array $where){
								$query = "UPDATE $this->tableName"
								." SET ".$this->query_set($set)." "
								.$this->query_where($where);
								//echo $query;
								return $this->db->query($query);
				}
				public function Remove($where){
								$query = "DELETE FROM $this->tableName ";
								if(is_array($where)){
												$query.=$this->query_where($where);
								}
								else{
												$query.="WHERE $where";
								}
								return $this->db->query($query);
				}
				protected function setTable($tableName){
								$this->tableName = $tableName;
				}
}


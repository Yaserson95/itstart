<?php
include_once	'DbTable.php';
include_once	'Marks.php';
include_once	'Modules/Forms/Comment.php';
class	Comments extends DbTable{
				protected $tableName = "Comments";
				private $type = "";
				private $key = "";
				private $marks;
				public function Comments(Itstart_db $base){
								$this->marks = new Marks($base);
								parent::DbTable($base);
				}
				public function setType(int $type){
								switch	($type){
												case 0:
																$this->type = "Comments";
																$this->key = "ComId";
																break;
												case 1:
																$this->type = "Articles";
																$this->key = "ArtId";
																break;
												case 2:
																$this->type = "Discussion";
																$this->key = "DiscId";
																break;
												default:
																$this->type = "";
																break;
								}
				}
				public function getById($commentId){
								$res = $this->Find(null,["ComId"=>$commentId])[0];
								
								if(!empty($res)){
												$comm = new Comment();
												$comm->setData($res);
												return $comm;
								}
								return null;
				}

				public function issetParent($parId):int{
								$query = "SELECT COUNT($this->key) FROM $this->type WHERE $this->key = '$parId';";
								$result = $this->db->query($query);
								$count = 0;
								if($result&&$result->num_rows>0){
												$count = (int)$result->fetch_array()[0];
								}
								return $count;
				}
				public function add(Comment $coment):int{
								$this->setType((int)$coment->getValue("TypePar"));
								if($this->issetParent($coment->getValue("Parent"))==0){
												return 2;
								}
								if(!$this->Insert($coment->getData())){
												return 3;
								}
								return 0;
				}
				public function getComments(int $TypePar, int $Parrent):array{
								$table = $this->tableName;
								$this->tableName = "ViewComments";
								$result = $this->Find(null,["TypePar"=>$TypePar,"Parent"=>$Parrent]);
								$this->tableName = $table;
								return $result;
				}
				private function getBrunch($parent,$type):string{
								$str = "";
								$arr=$this->Find(["ComId"],["Parent"=>$parent,"TypePar"=>$type]);
								foreach	($arr	as	$value)	{
												$str.=$this->getBrunch($value["ComId"],	0);
								}
								if($type==0)$str.= "$parent,";
								return $str;
				}
				public function Remove($where)	{
								$this->marks->Remove(["TypePar"=>0,"Parent"=>$where["ComId"]]);
								return parent::Remove($where);
				}

				public function removeBrunch($parent,$type){
								$deleted = trim($this->getBrunch($parent,$type),',');
								$this->marks->Remove("TypePar=0 AND Parent IN($deleted)");
								$deleted =  "ComId IN($deleted)";
								return $this->Remove($deleted);
				}
}

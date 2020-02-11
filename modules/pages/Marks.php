<?php
include_once	'modules/structures/Marks.php';
class MarksController{
				private $db;
				private $marks;
				public function MarksController(){
								$this->db = new Itstart_db();
								$this->marks = new Marks($this->db);
				}
				private function getusermarks(array $mark):array{
								return $this->marks->Find(null,$mark);
				}
				private function getinfomarks(int $Parent,int $TypePar):string{
								$result = $this->marks->getInfo($Parent,$TypePar);
								$info = ["Request"=>"info", "Comments"=>$this->db->ChildComments($Parent,$TypePar)];
								if(!empty($result)){
												$info = array_merge($info,	$result[0]);
								}
								else{ 
												$info = array_merge($info,["Like"=>0,"Dislike"=>0,"Rating"=>0]);
								}
								return json_encode($info);
				}
				public function Mark($post){
								if(!IsAutorized())return json_encode	(["Request"=>"error","Text"=>"No autorize"]);
								if(!RequireData($post,	"Type,Parent,Mark")){
											return json_encode	(["Request"=>"error","Text"=>"Require data does not exists!"]);
								}
								$post["TypePar"] = getNumberType($post["Type"]);
								unset($post["Type"]);
								$post["UserId"] = $_SESSION["UserId"];
								$result = $this->marks->Find(null,$post);
								if(!empty($result)){
												$this->marks->Remove($post);
								}
								else{
												$res=[];
												$temp = $post;
												switch((int)$post["Mark"]){
																case 0:{
																				$temp["Mark"] = 1;
																				$res = $this->marks->Find(null,$temp);
																				break;
																}
																case 1:{
																				$temp["Mark"] = 0;
																				$res = $this->marks->Find(null,$temp);
																				break;
																}
																default:
																				//$this->marks->Insert($post);
																				break;
												}
												if(!empty($res)){
																$this->marks->Update($post,$temp);
												}
												else{
																$this->marks->Insert($post);
												}
								}
								return $this->getinfomarks($post["Parent"],	$post["TypePar"]);
								//return 0;
								//echo var_dump($post);
				}
				public function UserMarks($post){
								if(!IsAutorized()) return json_encode(["Autorize"=>false]);
								if(!RequireData($post,	"Type,Parent")){
												return 5;
								}
								$type = getNumberType($post["Type"]);
								$marks = $this->getusermarks(["UserId"=>$_SESSION["UserId"],"Parent"=>$post["Parent"],"TypePar"=>$type]);
								$arr = ["Autorize"=>true,"Marks"=>$marks];
								return json_encode($arr);
				}
				public function Info($post){
								if(!RequireData($post,	"Type,Parent")){
												return 5;
								}
								$parType = getNumberType($post["Type"]);
								return $this->getinfomarks($post["Parent"],	$parType);
				}
				public function Set(&$patch){
								$com = strtolower(array_pop($patch));
								switch($com){
												case "mark":
																echo $this->Mark($_POST);
																break;
												case "info":
																echo $this->Info($_POST);
																break;
												case "usermarks":
																echo $this->UserMarks($_POST);
																break;
												default	:
																echo json_encode(["Request"=>"error","Text"=>"Вы нетуда попали"]);
																break;
								}
				}
}
if(isset($patch)){
				$marks = new MarksController();
				$marks->Set($patch);
}
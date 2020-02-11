<?php
include_once "Modules/Structures/Comments.php";
include_once	'Modules/autorize.php';
class commentsController{
				private $db;
				private $comments;
/*
public function get(int CommentId)
public function getAll(int objectId, int type)
public function Remove(int CommentId)
public function Create(int objectId, int type, Comment comment)
public function Edit(int CommentId,Comment comment)

*/
				public function commentsController(array &$patch){
								$this->db = new Itstart_db();
								$this->comments = new Comments($this->db);
								$this->Set($patch);
				}
				public function Set(array &$patch){
								$page = strtolower(array_pop($patch));
								switch($page){
												case "get":{
																if(RequireData($_POST,"CommentId")){
																				$this->Get($_POST["CommentId"]);
																}
																break;
												}
												case "getall":{
																//echo var_dump($_POST);
																if(RequireData($_POST,"ObjectId,Part")){
																				$this->getAll($_POST["ObjectId"],	getNumberType($_POST["Part"]));
																}
																else echo json_encode	(["Type"=>"error","Text"=>"Неверные данные"]);
																break;
												}
												case "remove":{
																if(RequireData($_POST,"CommentId")){
																				echo $this->Remove($_POST["CommentId"]);
																}else echo 1;
																break;
												}
												case "create":{
																if(!RequireData($_POST,"Parent,Part")){
																				echo 1;
																				break;
																}
																$comment = new Comment($_POST);
																$comment->setMode(Form::Create);
																$comment->setValue("TypePar",	getNumberType($_POST["Part"]));
																$comment->setValue("UserId",(int)$_SESSION["UserId"]);
																//echo var_dump($comment->Get());
																if(!$comment->isValid()){
																				echo 2;
																				break;
																}
																echo $this->Create($comment);
																break;
												}
												case "edit":{
																if(!RequireData($_POST,"ComId,TextCom")){
																				echo 1;
																				break;
																}
																echo $this->Edit($_POST["ComId"],$_POST["TextCom"]);
																break;
												}
								}
				}
				public function getAll(int $objectId, int $type){
								//$limit = getLimit("Comments");
								$this->comments->OrderBy(getOrderBy("Comments"));
								$result = $this->comments->getComments($type,$objectId);
								foreach	($result as $key => $value){
												$result[$key]['Text'] = htmlspecialchars_decode($result[$key]['Text']);
												$result[$key]['Time'] =  dateDiff($value["DatePubl"],date("Y-m-d H:i:s",time()));
												//Изменять можно только те комментарии на которые никто не ответил
												if(IsAutorized()){
																if(RoleAbove(Role::Moderator)){
																				$result[$key]['Editing']=true;
																}
																else{
																				$result[$key]['Editing']=(($value["UserId"]==$_SESSION["UserId"])&&($value["Nchild"]==0)&&!isRole(Role::Blocked));
																}
												}
												else $result[$key]['Editing'] = false;
												unset($result[$key]["DatePubl"]);
								}
								$returned = ["Type"=>"comments","Data"=>$result,"Autorize"=>	IsAutorized()];
								echo json_encode($returned);
				}
				public function Remove(int $CommentId):int{
								if(!RoleAbove(Role::Moderator)){
												if($this->db->ChildComments($CommentId,0)!=0){
																return 3;
												}
												//Чтобы могли удалять только свои комментарии
												$where = ["ComId"=>$CommentId,"UserId"=>$_SESSION["UserId"]];
												if(!$this->comments->Remove($where)){
																return 2;
												}
								}
								else{ 
												if(!$this->comments->removeBrunch($CommentId,0)){
																return 7;
												}
								}
								return 0;
				}
				public function Get(int $CommentId){

				}
				public function Create(Comment $comment){
								return $this->comments->add($comment);
				}
				public function Edit(int $comId,string $textCom):int{
								//echo $comId;
								$comment = $this->comments->getById($comId);
								if($comment==null) return 5;
								//Для модератора и выше следующие 2 пункта должны игнорироваться
								if(!RoleAbove(Role::Moderator)){
												//Чтобы один пользователь не мог изменить комментарий другого
												if($comment->getValue("UserId")!=$_SESSION["UserId"]){
																return 3;
												}
												//Чтобы не могли изменить комменты, на которые уже ответили
												if($this->db->ChildComments($comId,	0)>0){
																return 6;
												}
								}
								$comment->setValue("Textcom",$textCom);
								$comment->setValue("DatePubl",date("Y-m-d H:i:s",time()));
								if(!$comment->isValid()){ 
												return 4;
								}
								$res = $this->comments->update($comment->getData(),$comment->getKeys());
								if(!$res) return 2;
								return 0;
				}
}

if(!isset($patch)) header("Location: /Home");
//Если путь пустой
if(empty($patch)){
				header("Location: /Home");
}
$comment = new commentsController($patch);
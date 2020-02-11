<?php
include_once	'modules/structures/Groups.php';
include_once	'modules/structures/Users.php';
include_once	'modules/structures/Discussions.php';
include_once	'modules/structures/Participations.php';
include_once "modules/share/myHtmlHalper.php";
class GroupsController{
				private $pageName = "Index";
				private $partName = "Groups";
				private $pageTitle= "Сообщества";
				private $newPage;
				private $db;
				private $groups;
				private $limit=0;
				public function GroupsController(){
								$this->db = new Itstart_db();
								$this->groups = new Groups($this->db);
								$this->newPage = new PageBuilder();
								$this->newPage->setContentFolder("content/$this->partName");
								$this->newPage->addPart("Главная",	"/");
								$this->newPage->setTitle($this->pageTitle);
								$this->limit = getLimit("Groups");
				}
				public function initCount(int $count){
								
				}
				public function groupTools(int $Groupid,array &$patch){
								$command = strtolower(array_pop($patch));
								if(!empty($patch)){
												$patchTr = "/$this->partName/Group_$Groupid/$command";
												echo $patchTr;
												header("Location: $patchTr");
												//exit();
								}
								$Group = $this->groups->getById($Groupid);
								$this->newPage->addPart($Group->getValue("Title"),"Group_$Groupid");
								switch	($command){
												case "enter":
																$this->Enter($Group);
																header("Location: /$this->partName/Group_$Groupid");
																break;
												case "edit":
																if($this->Edit($Group,$_POST)!=0){
																				header("Location: /$this->partName/Group_$Groupid");
																}
																break;
												case "users":
																$this->Users($Group);
																break;
												case "editpart":
																echo $this->EditPart($_POST,	$Group);
																exit();
												case "info":
																$this->Info($Group);
																exit();
												case "deluser":
																echo $this->DelUser($_POST,	$Group);
																exit();
												default:
																header("Location: /$this->partName/Group_$Groupid");
								}
								return 0;
				}
				public function Users(Group $Group){
								$this->pageName = "Users";
								
								//$this->newPage->setContent("$this->pageName.php");
								$this->newPage->addData("group",$Group);
								$this->pageTitle = "Управление пользователями";
								$this->newPage->addScript("UsersEdit.js");
								$this->newPage->addPart($this->pageTitle,$this->partName);
				}
				public function set_patch(array &$patch):int{
								$this->newPage->addPart($this->pageTitle,	$this->partName);
								if(empty($patch)){
												$this->Index();
												return 0;
								}
								$page = strtolower(array_pop($patch));
								if(preg_match("/^group_([0-9]*)$/",	$page)){
												$groupId = (int)ltrim($page,"group_");
												if(!empty($patch)){
																$this->groupTools($groupId,$patch);
												}
												else{
																$this->Detales($groupId);
												}
												return 0;
								}
								switch($page){
												case "index":
																$this->Index();
																return 0;
												case "create":
																$this->Create($_POST);
																break;
												case "info":
																echo $this->groups_info($_POST);
																exit();
												default:
																header("Location: /$this->partName");
																break;
								}
								return 0;
				}
				public function set(array &$patch){
								$length = $this->set_patch($patch);
								
								if(count($patch)>$length){
												header("Location: /$this->partName/$this->pageName");
								}
								$this->newPage->setContent("$this->pageName.php");
								$this->newPage->setTitle($this->pageTitle);
								$this->newPage->build();
				}
				public function Index(array $post=null){
								$result = $this->groups->Find();
								$this->newPage->addData("Groups",$result);
								$this->newPage->addData("Themes",$this->groups->getThemes());
								//echo utf8_вусщву("привет");
								unset($result);
				}
				public function Edit(Group &$Group, array $post=null):int{
								//if(isRole(Role::Blocked)) header("Location: /$this->partName");
								if(!IsAutorized()){
												return -1;
								}
								if($Group==null){
												return 2;
								}
								$GroupId = $Group->getValue("GroupId");
								if(!RoleAbove(Role::Moderator)){
												if($Group->getValue("OwnerId")!=$_SESSION["UserId"]){
																$part = new Participations($this->db);
																$role = $part->Role((int)$_SESSION["UserId"],	$GroupId);
																unset($part);
																if($role!=2){
																				return 1;
																}
												}
								}
								$this->pageName="Create";
								$this->pageTitle="Редактирование";
								//$this->groups
								//$Group->setMode(Form::Create);
								if(!empty($post)){
												$Group->Set($post);
												if($Group->isValid()){
																//$this->groups->Create($Group);
																if($this->groups->Edit($Group)==0){
																				header("Location: /$this->partName/Group_$GroupId");
																}
																else{
																				$this->newPage->addMessage('error','Ошибка при изменении данных');
																}
												}
												else{
																$this->newPage->addMessage('error','Необходимые поля должны быть заполнены правильно!');
												}
								}
								$this->newPage->addData("Group",$Group);
								$this->newPage->addHead("<script type='text/javascript' src='/source/ckfinder/ckfinder.js'></script>");
								$this->newPage->addScript("CreateGroup.js");
								$this->newPage->addScript("ckeditor/ckeditor.js");
								$this->newPage->addData("target","/Groups/Group_$GroupId/Edit");
								
								$this->newPage->addPart($this->pageTitle,"Edit");
								unset($Group);
								return 0;
				}
				public function Enter(Group &$Group){
								if(!IsAutorized()){
												return -1;
								}
								if(!$Group->isValid()){
												return 2;
								}
								$groupId = (int)$Group->getValue("GroupId");
								$part = new Participations($this->db);
								$userp = $part->Find(null,	["GroupId"=>$groupId,"UserId"=>$_SESSION["UserId"]]);
								$owner = (int)$Group->getValue("OwnerId");
								unset($Group);
								if((int)$_SESSION["UserId"]==$owner){
												return 3;
								}
								if(empty($userp)){
											$part->Insert(["UserId"=>$_SESSION["UserId"],"GroupId"=>$groupId,"Post"=>0]);
								}
								else{
												if($userp[0]["Post"]==4){
																return 4;
												}
												$part->Remove(["UserId"=>$_SESSION["UserId"],"GroupId"=>$groupId]);
								}
								return 0;
				}
				public function EditPart(array $post,Group &$Group){
								if(!IsAutorized()) return 0;
									if($Group==null){
												return 2;
								}
								$GroupId = $Group->getValue("GroupId");
								$part = new Participations($this->db);
								if(!RoleAbove(Role::Moderator)){
												if($Group->getValue("OwnerId")!=$_SESSION["UserId"]){
																$role = $part->Role((int)$_SESSION["UserId"],	$GroupId);
																if($role<1&&$role>2){
																				return 1;
																}
												}
								}
								if(!RequireData($post,	"UserId,Post")) return 3;
								$UserId = (int)$post["UserId"];
								$where = ["GroupId"=>$GroupId,"UserId"=>$UserId];
								if($part->Count($where)>0){
												$set = ["Post"=>(int)$post["Post"]];
												if(!$part->Update($set,	$where)){
																return 5;
												}
								}
								else return 4;
								return 0;
				}
				public function Create(array $post=[]){
								if(isRole(Role::Blocked)) header("Location: /$this->partName");
								$this->pageName="Create";
								$this->pageTitle="Создание тематической группы";
								$Group = new Group();
								$Group->setMode(Form::Create);
								if(!empty($post)){
												$Group->Set($post);
												$Group->setValue("OwnerId",	$_SESSION["UserId"]);
												if($Group->isValid()){
																$this->groups->Create($Group);
												}
												else{
																$this->newPage->addMessage('error','Необходимые поля должны быть заполнены правильно!');
												}
								}
								$this->newPage->addData("Group",$Group);
								$this->newPage->addHead("<script type='text/javascript' src='/source/ckfinder/ckfinder.js'></script>");
								$this->newPage->addScript("CreateGroup.js");
								$this->newPage->addScript("ckeditor/ckeditor.js");
								$this->newPage->addData("target","/Groups/Create");
								unset($Group);
				}
				public function Detales(int $groupId){
								$this->pageName="detales";
								$Group = $this->groups->Find(null,["GroupId"=>$groupId]);
								if(empty($Group)){
												header("Location: /$this->partName");
								}
								//Информация:
								$Part = new Participations($this->db);
								//Пользователи группы
								$partit = $Part->Find(null,["GroupId"=>$groupId]);
								$this->newPage->addData("users",$partit);
								unset($partit);
								//Контакты группы
								$Users = new Users($this->db);
								$columns = ["Nickname","Firstname","Surname","UserId"];
								$Users->Limit(5);
								$owner = $Users->Find($columns,	["UserId"=>$Group[0]["OwnerId"]]);
								$owner[0]["Post"]=-1;
								array_push($columns,"Post");
								$contacts = $Part->Find($columns,"Post NOT IN(0,4) AND GroupId = $groupId");
								array_unshift($contacts,$owner[0]);
								$this->newPage->addData("contacts",$contacts);
								//пользовательская информация
								$info = [];
								if(IsAutorized()){
												$info["Autorize"]=true;
												$current = $Part->Find(null,["UserId"=>$_SESSION["UserId"],"GroupId"=>$groupId]);
												$post = -1;
												if(!empty($current)){
																$post = (int)$current[0]["Post"];
																switch ($post){
																				case 1:
																								$info["Useredit"]=true;
																								break;
																				case 2:
																								$info["Useredit"]=true;
																								$info["Groupedit"]=true;
																								break;
																}
																$info["Post"] = $post;
												}
												if($Group[0]["OwnerId"]==$_SESSION["UserId"]){
																$info["Owner"]=true;
																$info["Useredit"]=true;
																$info["Groupedit"]=true;
												}
												if(RoleAbove(Role::Moderator)){
																$info["Useredit"]=true;
																$info["Groupedit"]=true;
												}
												$info["GroupId"] = $groupId;
								}
								else{
													$info = ["Autorize"=>false];
								}
								$this->newPage->addData("userinfo",	json_encode($info));
								unset($contacts);
								unset($Users);
								$this->pageTitle = $Group[0]["Title"];
								$this->newPage->addScript("GropDetales.js");
								$this->newPage->addPart($this->pageTitle,	"Group_$groupId");
								$this->newPage->addData("group",$Group[0]);
								unset($Group);
				}
				public function Info(Group &$Group){
								//echo var_dump($_POST);
								if(empty($_POST)){
												exit();
								}
								$info = [];
								$part = new Participations($this->db);
								if(!isset($_POST["Post"])){
												exit();
								}
								$post = (int)$_POST["Post"];
								$where = ["GroupId"=>$Group->getValue("GroupId")];
								//if(isset($_POST));
								if($post>0){
												$where["Post"] = $post;
								}
								//для поиска
								if(isset($_POST["Search"])){
												if(empty($_POST["Search"])){
																$part->removeLikes();
												}
												else{
																$part->addLike("Nickname",$_POST["Search"]);
																$part->addLike("Firstname",$_POST["Search"]);
																$part->addLike("Surname",$_POST["Search"]);
												}
								}
								else{
												$part->removeLikes();
								}
								$length = $part->Count($where);
								if(isset($_POST["Limit"])){
												$limit = (int)$_POST["Limit"];
												$pages = (int)($length/$limit);
												$page = 1;
												if($pages>1){
																if(isset($_POST["Page"])){
																				$page = (int)$_POST["Page"];
																				if($page>$pages){
																								$page = $pages;
																				}
																}
												}
										
												$part->Limit($limit,	($page-1)*$limit);
												$info["Page"] = $page;
												$info["Pages"] = $pages;
								}
								$info["Count"] = $length;
								
								$items = $part->Find(null,$where);
								$info["Items"] = $items;
								echo json_encode($info);
				}
				public function DelUser(array $post,Group &$Group){
								if(!IsAutorized()) return 0;
									if($Group==null){
												return 2;
								}
								$GroupId = $Group->getValue("GroupId");
								$part = new Participations($this->db);
								if(!RoleAbove(Role::Moderator)){
												if($Group->getValue("OwnerId")!=$_SESSION["UserId"]){
																$role = $part->Role((int)$_SESSION["UserId"],	$GroupId);
																if($role<1&&$role>2){
																				return 1;
																}
												}
								}
								if(!isset($post["UserId"])) return 3;
								$UserId = (int)$post["UserId"];
								$where = ["GroupId"=>$GroupId,"UserId"=>$UserId];
								if($part->Count($where)>0){
												if(!$part->Remove($where)){
																return 5;
												}
								}
								else return 4;
								return 0;
				}
				public function groups_info($post){
								$where = [];
								$post = array_change_key_case($post, CASE_LOWER);
								if(isset($post["userid"])){
												if($post["userid"]>0){
																$where["OwnerId"] = $post["userid"];
												}
												else{
																$where["OwnerId"] = $_SESSION["UserId"];
												}
								}
								if(isset($post["search"])){
												$this->groups->SearchBy(["Title","Description","Theme"],$post["search"]);
								}
								$count = $this->groups->count($where);
								$limit = getLimit("Groups");
								$orders = getOrderBy("Groups");
								$this->groups->orderBy($orders);
								$info = ["Limit"=>$limit,"Count"=>$count];
								if($count>$limit){
												$info["Pages"]=(int)($count/$limit);
												if($count%$limit>0){
																$info["Pages"]++;
												}
												$page = 1;
												if(isset($post["page"])){
																$page = (int)$post["page"];
												}
												if($page<1||$page>$info["Pages"]){
																$page = 1;
												}
												$this->groups->Limit($limit,$limit*($page-1));
												$info["Page"] = $page;
								}
								$info["Items"] = $this->groups->Find(null,$where);
								return json_encode($info);
				}
}
if(!isset($patch)) header	("Location: \ ");
$groups = new GroupsController();
$groups->set($patch);
<?php
include_once	'modules/structures/Discussions.php';
include_once	'modules/structures/Groups.php';
include_once	'modules/structures/Participations.php';
class DiscussionsController{
				private $db;
				private	$disc;
				private $pageName = "Index";
				private $partName = "Discussions";
				private $pageTitle= "Обсуждения";
				private $newPage;
				private $patch = "/Discussions";
				private $Limit = 0;
				public function DiscussionsController(){
								$this->db = new Itstart_db();
								$this->disc = new Discussions($this->db);
								$this->newPage = new PageBuilder();
								$this->newPage->setContentFolder("content/$this->partName");
								$this->newPage->addPart("Главная",	"/");
								$this->newPage->setTitle($this->pageTitle);
								
				}
				public function set(array &$patch){
								if($this->set_patch($patch)!=0){
												header("Location: /$this->partName");
								}
								if(!empty($patch)){
												header("Location: $this->patch");
								}
								$this->newPage->setContent("$this->pageName.php");
								$this->newPage->setTitle($this->pageTitle);
								$this->newPage->build();
				}
				public function set_patch(array &$patch):int{
								$this->newPage->addPart($this->pageTitle,	$this->partName);
								if(empty($patch)){
												$this->Index();
												return 0;
								}
								$page = strtolower(array_pop($patch));
								//Group_N
								if(preg_match("/^group_([0-9]*)$/",	$page)){
												$groupId = (int)ltrim($page,"group_");
												$this->patch.="/Group_$groupId";
												return $this->Groups($patch,$groupId);
								}
								//Discussion_M
								if(preg_match("/^discussion_([0-9]*)$/",	$page)){
												$DiscId = (int)ltrim($page,"discussion_");
												$this->patch.="/Discussion_$DiscId";
												return $this->Disc($patch,$DiscId);
								}
								if($page=="info"){
												echo $this->info($_POST);
												exit();
								}
								return 0;
				}
				public function Index(int $GroupId = -1,array &$patch=null){
								//echo $this->patch;
								$where = null;
								if($GroupId!=-1){
												$where=["GroupId"=>$GroupId];
								}
								$this->disc->OrderBy(getOrderBy("Discussions"));
								$limit = getLimit("Discussions");
								$count = $this->disc->Count($where);
								$page = 1;
								if($count>$limit){
												$pages = (int)($count/$limit)+1;
												if(!empty($patch)){
																if(is_numeric($patch[count($patch)-1])){
																		$page = (int)	array_pop($patch);
																}
																if($page<1&&$page>$pages){
																				header("Location: $this->patch");
																}
												}
												$this->disc->Limit($limit,($page-1)*$limit);
								}
								$items = $this->disc->Search(null,$where);
								$this->newPage->addData("items",$items);
								return 0;
				}
				public function Disc(array &$patch,int $DiscId){
								$Disc = $this->disc->getById($DiscId);
								if($Disc==null){
												return 1;
								}
								$this->newPage->addPart($Disc->getValue("Title"),	"Discussion_$DiscId");
								if(empty($patch)){
												return $this->View($Disc);
								}
								if(!$this->Editing($Disc)) return 1;
								$Command = strtolower(array_pop($patch));
								//exit("here");
								switch	($Command){
												case "edit":
																return $this->Edit($Disc);
												case "delete":
																return $this->Delete($Disc);
												default:
																header("Location: $this->patch");
								}
								$this->patch.="/$Command";
				}
				public function View(Discussion &$Disc):int{
								$this->pageTitle = $Disc->getValue("Title");
								$this->pageName = "View";
								include_once	'modules/structures/Users.php';
								$users = new Users($this->db);
								$groups = new Groups($this->db);
								$user = $users->findById((int)$Disc->getValue("UserId"));
								$group =  $groups->getById((int)$Disc->getValue("GroupId"));
								unset($users);
								if($user==null){
												return 1;
								}
								$this->newPage->addData("group",$group);
								$this->newPage->addData("Disc",$Disc);
								$this->newPage->addData("User",$user);
								$this->newPage->addData("Edit",$this->Editing($Disc));
								$this->newPage->addScript("Comments.js");
								$this->newPage->addScript("DiscView.js");
								$this->newPage->addScript("ckeditor/ckeditor.js");
								$this->newPage->addHead("<script type='text/javascript' src='/source/ckfinder/ckfinder.js'></script>");
								unset($Disc);
								unset($user);
								unset($group);
								return 0;
				}
				
				public function Groups(array &$patch,	int $GroupId):int{
								$page = "/$this->partName/Group_$GroupId";
								$groups = new Groups($this->db);
								$group = $groups->arrayById($GroupId);
								unset($groups);
								//Группа:
								if($group==null){
												return 1;
								}
								$this->newPage->addPart($group["Title"],	"Group_$GroupId");
								if(empty($patch)){
												return $this->Index($GroupId);
								}
								$Command = strtolower(array_pop($patch));
								$result = 0;
								switch($Command){
												case "create":
																$result = $this->Create($_POST,$GroupId);
																break;
												default:
																header("Location: $page");
								}
								$this->patch.="/$Command";
								return $result;
				}
				public function Create(array $post,int $groupId):int{
								if(!IsAutorized()){
												return 1;
								}
								if(isRole(Role::Blocked)){
												return 1;
								}
								$part = new Participations($this->db);
								$role = $part->Role((int)$_SESSION["UserId"],	$groupId);
								if(!RoleAbove(Role::Moderator)){
												if($role==4){
																return 2;
												}
								}
								$disc = new Discussion();
								$disc->setMode(Form::Create);
								if(!empty($post)){
												$disc->Set($post);
												$disc->setValue("UserId",	$_SESSION["UserId"]);
												$disc->setValue("GroupId",	$groupId);
												if($disc->isValid()){
																$result = $this->disc->Create($disc);
																if($result>=0){
																				header("Location: /$this->partName/Discussion_$result");
																}
																else{
																				$this->newPage->addMessage('error','Ошибка при добавлении данных!');
																}
												}
												else{
																$this->newPage->addMessage('error','Необходимые поля должны быть заполнены правильно!');
												}
								}
								$this->newPage->addData("disc",$disc);
								unset($disc);
								$this->pageName="Create";
								$this->pageTitle="Создание обсуждения";
								$this->newPage->addScript("CreateDisc.js");
								$this->newPage->addScript("ckeditor/ckeditor.js");
								$this->newPage->addHead("<script type='text/javascript' src='/source/ckfinder/ckfinder.js'></script>");
								$this->newPage->addData("target","$this->patch/Create");
								return 0;
				}
				public function Edit(Discussion &$Disc):int{
								$this->newPage->addData("disc",$Disc);
								if(!empty($_POST)){
												$Disc->Set($_POST);
												if($Disc->isValid()){
																if($this->disc->Edit($Disc)==0){
																				header("Location: /$this->partName/Discussion_".$Disc->getValue("DiscId"));
																}
																else{
																				$this->newPage->addMessage('error','Ошибка при добавлении данных!');
																}
												}
												else{
																$this->newPage->addMessage('error','Необходимые поля должны быть заполнены правильно!');
												}
								}
								unset($Disc);
								$this->pageName="Create";
								$this->pageTitle="Редактировать обсуждение";
								$this->newPage->addPart($this->pageTitle,	"Edit");
								$this->newPage->addScript("CreateDisc.js");
								$this->newPage->addScript("ckeditor/ckeditor.js");
								$this->newPage->addHead("<script type='text/javascript' src='/source/ckfinder/ckfinder.js'></script>");
								$this->newPage->addData("target","$this->patch/Edit");
								return 0;
				}
				public function Delete(Discussion &$Disc){
								if($this->disc->Delete($Disc)!=0){
												exit("Вы хакер! Вы смогли сломать сайт!!!");
								}
								unset($Disc);
								return 1;
				}
				public function info($post){
								$where = [];
								$post = array_change_key_case($post, CASE_LOWER);
								$UserId = (int)$_SESSION["UserId"];
								if(isset($post["userid"])){
												if($post["userid"]>0){
																$where["UserId"] = $post["userid"];
												}
												else{
																$where["UserId"] = $_SESSION["UserId"];
												}
								}
								if(isset($post["mark"])){
												$mark = $post["mark"];
												$where["issetMark(DiscId,2,$UserId,$mark)"] = "1";
								}
								if(isset($post["search"])){
												$this->disc->SearchBy(["Title","Tags"],$post["search"]);
								}
								$count = $this->disc->count($where);
								$limit = getLimit("Discussions");
								$orders = getOrderBy("Discussions");
								$this->disc->orderBy($orders);
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
												$this->disc->Limit($limit,$limit*($page-1));
												$info["Page"] = $page;
								}
								$items = $this->disc->Search(null,$where);
								//echo	var_dump($items);
								$info["Items"] = $items;
								unset($items);
								return json_encode($info);
				}
				private function Editing(Discussion &$Disc):bool{
								if(!IsAutorized()) return false;
								if(isRole(Role::Blocked)) return false;
								if(RoleAbove(Role::Moderator)) return true;
								if((int)$_SESSION["UserId"]==(int)$Disc->getValue("UserId")) return true;
								$groupId = (int)$Disc->getValue("DiscId");
								$part = new Participations($this->db);
								$role = $part->Role($_SESSION["UserId"],	$groupId);
								unset($part);
								if($role<1&&$role>2) return false;
								return true;
				}
}

if(!isset($patch)) header	("Location: \ ");
$groups = new DiscussionsController();
$groups->set($patch);
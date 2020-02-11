<?php
include_once	'modules/structures/users.php';
include_once	'modules/autorize.php';
class UsersController{
				private $pageName = "Index";
				private $partName = "Users";
				private $pageTitle= "Пользователи";
				private $newPage;
				private $db;
				private $users;
				private $patch = "/Users";
				function UsersController(){
								$this->newPage = new PageBuilder();
								$this->db = new Itstart_db();
								$this->newPage->addPart("Главная",	"/");
								//$this->newPage->addPart("Главная",	"/");
								$this->newPage->addPart($this->pageTitle,	$this->partName);
								$this->newPage->setContentFolder("content/$this->partName");
								$this->users = new Users($this->db);
								
				}
				public function Set(&$patch){
								if(empty($patch)||!IsAutorized()){
													header("Location: /");
								}
								$nick =	strtolower(array_pop($patch));
								if($nick==strtolower($_SESSION["Nickname"])){
												header("Location: /Profile/View");
								}
								$User = $this->users->findByNick($nick);
								if($User->isEmpty()){
												header("Location: /");
								}
								$this->User($User,$patch);
								if(count($patch)>0){
												header("Location: $this->patch");
								}
								$this->newPage->setContent($this->pageName.".php");
								$this->newPage->setTitle($this->pageTitle);
								$this->newPage->build();
				}
				public function User(User &$User, array &$patch){
								$User->setMode(User::Wiew);
								$this->patch.="/".$User->getValue("Nickname");
								$this->newPage->addPart($User->getValue("Nickname"),	$User->getValue("Nickname"));
								$this->pageTitle = $User->getValue("Nickname");
								if(!empty($patch)){
												$comm = strtolower(array_pop($patch));
												switch($comm){
																case "edit":
																				return $this->Edit($User);
																default:
																				array_push($patch,$comm);
																				break;
												}
								}
								$this->newPage->addData("user",$User);
								$this->newPage->addData("edit",$this->Editing($User));
								unset($User);
								return 0;
				}
				public function Edit(User &$User){
								if(!$this->Editing($User)) return 1;
								$editcolumns = array_diff($User->getColumns(),	["UserId","UserPswrd","Email"]);
								$this->patch.="/Edit";
								$this->pageName = "Edit";
								$this->pageTitle = $User->getValue("Nickname").": Редактирование";
								$this->newPage->addPart("Редактировние",$this->pageName);
								if(!empty($_POST)){
												$User->Set(array_intersect_key($_POST,	array_flip($editcolumns)));
												if($User->isValid()){
																if((int)$User->getValue("Priority")>(int)$_SESSION["Priority"]&&$User->getValue("Priority")!=4){
																				$this->newPage->addMessage("error","Невозможно наделить правами пользователя выше своих");
																}
																else{
																				if(!$this->users->Edit($User)){
																								$this->newPage->addMessage("error","Ошибка на сервере");
																				}
																}
												}
												else{
																//echo $User->getErrors();
																$this->newPage->addMessage("error","В водимых данных есть ошибки");
												}
												//echo var_dump(array_intersect_key($_POST,	array_flip($editcolumns)));
								}
								$this->newPage->addData("user",$User);
								$this->newPage->addData("columns",$editcolumns);
								$this->newPage->addData("action",$this->patch);
								unset($User);
								return 0;
				}
				public function Editing(User &$User):bool{
								if(!RoleAbove(Role::Admin))	return false;
								$userRole = (int)$User->getValue("Priority");
								if($userRole==Role::BaseAdmin)return false;
								if($userRole>=(int)$_SESSION["Priority"]&&$userRole!=Role::Blocked)return false;
								return true;
				}
}
if(!isset($patch)){ 
				header("Location: /");
}
else{
				$users = new UsersController();
				$users->Set($patch);
				unset($users);
}
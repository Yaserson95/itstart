<?php
include_once	'modules/structures/articles.php';
class ArticleController{
				private $pageName = "Index";
				private $partName = "Articles";
				private $pageTitle= "Статьи";
				private $newPage;
				private $db;
				private $articles;
				public function ArticleController(){
								$this->newPage = new PageBuilder();
								$this->db = new Itstart_db();
								$this->newPage->addPart("Главная",	"/");
								$this->newPage->addPart($this->pageTitle,	$this->partName);
								$this->newPage->setContentFolder("content/$this->partName");
								$this->articles = new Articles($this->db);
								$this->articles->OrderBy(getOrderBy("Articles"));
				}
				public function locateToPart(string $page=""){
								header("Location: /$this->partName/$page");
				}
				public function index(array $post = null,int $artType = -1, array &$patch = null):int{
								
								$where = null;
								if(IsAutorized()){
												$key = "issetMark(ArtId,1,".$_SESSION["UserId"].",2)";
												$where = [$key=>0];
								}
								$length = 0;
								$page = "";
								if($artType!=-1){
												switch ($artType){
												case 0:
																$page = "News";
																$this->pageTitle = "Новости";
																break;
												case 2:
																$page = "Tutorials";
																$this->pageTitle = "Инструкции";
																break;				
												case 1:
																$page = "Digests";
																$this->pageTitle = "Обзоры";
																break;
												default:
																$this->locateToPart();
																break;
												}
												$this->newPage->addPart($this->pageTitle,$page);
												$where["ArtType"] = $artType;
								}
								$count =  $this->articles->Count($where);
								$limit = getLimit($this->partName);
								$countPages = (int)($count/($limit+1));
								if($countPages>0){
												$length=1;
												$pageNumber = 0;
												//echo var_dump($patch);
												if(!empty($patch)){
																if(!is_numeric($patch[0])){
																				$this->locateToPart($page);
																}
																$pageNumber = (int)	array_pop($patch);
																if(($pageNumber<0)||($pageNumber>$countPages)){
																				$this->locateToPart($page);
																}
												}
												$this->newPage->addPart("Страница (".($pageNumber+1)."/".($countPages+1).")",$pageNumber);
												$this->articles->Limit($limit,$pageNumber*$limit);
												$this->newPage->setPageNavigator($pageNumber,$countPages+1);
								}	
								else	{
												if(!empty($patch)){
															$this->locateToPart($page);
															exit();//Без этого почемуто зацикленное перенаправление
												}
												
								}
								$this->newPage->addData("navigator",$this->newPage->pageNavigator("$this->partName/$page"));
								$arr = $this->articles->getArticles($where);
								$this->newPage->addData("articles",$arr);
								unset($arr);
								return $length;
				}

				public function create(array $post = null){
								if(!IsAutorized()){
												header("Location: /Account/Index");
								}
								$this->pageName = "create";
								$this->pageTitle= "Создание статьи";
								$newArt = new Article();
								$newArt->setMode(Article::Create);
								if(!empty($post)){
												$newArt->Set($post);
												//Подставление Id
												$newArt->setValue("UserId",	$_SESSION["UserId"]);
												if($newArt->isValid()){	
															$m = $this->articles->Create($newArt);
															if($m!=0){
																		$this->newPage->addMessage("error","При добавлении статьи произошла ошибка №$m");		
															}
															header("Location: /$this->partName");
												}else{
																$this->newPage->addMessage("error","Не правильно введены данные!");
												}
								}
								//Подключение скриптов
								$this->newPage->addHead("<script type='text/javascript' src='/source/ckfinder/ckfinder.js'></script>");
								$this->newPage->addScript("createArticle.js");
								$this->newPage->addScript("ckeditor/ckeditor.js");
								$this->newPage->addData("article",$newArt);
								$this->newPage->addData("target","$this->pageName");
								unset($newArt);
				}
				
				public function edit(array $post = null,array &$patch){
								if(!IsAutorized()){
												header("Location: /Account/Index");
								}

								//Если не указали, номер статьи
								if(empty($patch)) header("Location: /Articles");
								$page = strtolower(array_pop($patch));
								$artId = (int)ltrim($page,"article_");
								if(!empty($patch)) header("Location: /Articles/Edit/$page");
								$imgArtPatch = "/articles/images";
								$article = $this->articles->getArticleById($artId,Article::Edit);
								if($article->isEmpty()){
												header("Location: /Articles");
								}
								//Защита от взлома
								if(!RoleAbove(Role::Moderator)){
												if($article->getValue("UserId")!=$_SESSION["UserId"]){
																header("Location: /Articles");
												}
								}
							 $userData = getUsersData($article->getValue("Nickname"));
								if(!empty($post)){
												//echo var_dump($article->Get());
												$post = array_diff($post,	["UserId","NickName"]);
												$article->Set($post);
												if($article->isValid()){
																if($this->articles->Edit($article)===0){
																				header("Location: /Articles/$page");
																}
																else{
																				$this->newPage->addMessage("error","Произошла ошибка при добавлении данных!");
																}
												}
												else{
																//$article->getErrors();
																$this->newPage->addMessage("error","Не правильно введены данные!");
																//echo var_dump($article->Get());
												}
								}
								$article->setMode(Article::Create);
								//Инициализация страници
								$this->pageName = "create";
								$this->pageTitle= "Редактирование";
								$file = $userData."$imgArtPatch/".$article->getValue("Mini");
								$imgPatch = "/".UserPatch."/".$article->getValue("Nickname").$imgArtPatch;
								$article->setColumnProperty("Mini",	"inner",	"<input type = 'hidden' id = 'imgPatch' value='$imgPatch'/>");
								$this->newPage->addHead("<script type='text/javascript' src='/source/ckfinder/ckfinder.js'></script>");
								$this->newPage->addScript("createArticle.js");
								$this->newPage->addScript("ckeditor/ckeditor.js");
								$this->newPage->addScript("ckeditor/adapters/jquery.js");
								$this->newPage->addPart($this->pageTitle,"/Edit/$page");
								$this->newPage->addData("article",$article);
								$this->newPage->addData("target","Edit/$page");
								unset($article);
				}

				public function remove(array &$patch){
								if(empty($patch)) return 1;	
								$artId = (int)	ltrim(strtolower(array_pop($patch)),"article_");
								$article = $this->articles->getArticleById($artId);
								if($article->isEmpty()){
												return 2;
								}
								if(!IsAutorized()) return 3;
								if(!RoleAbove(Role::Moderator)){
												if($_SESSION["UserId"]!=$article->getValue("UserId")){
															return 4;
												}
								}
								return $this->articles->Delete($article);

				}
				public function view(string $page){
								$ArtId = (int)ltrim(strtolower($page),"article_");
								$article = new Article();
								$this->pageName = "view";
								$this->pageTitle= "Создание статьи";
								$article = $this->articles->getArticleById($ArtId);
								if(!$article->isEmpty()){
												$editing = false;
												if(IsAutorized()){
																if(RoleAbove(Role::Moderator)){
																				$editing = true;
																}else{
																				if($article->getValue("UserId")==$_SESSION["UserId"]&&$_SESSION["Priority"]!=	Role::Blocked){
																								$editing = true;
																				}
																}
												}
												$this->pageTitle= $article->getValue("Name");
												$this->newPage->addPart($this->pageTitle,$page);
												$this->newPage->addData("article",$article);
												$this->newPage->addData("editing",$editing);
												$filename = getUsersData($article->getValue("Nickname"))."/Articles/$page.htm";
												$this->newPage->addData("filename",$filename);
												$this->newPage->addScript("Comments.js");
												$this->newPage->addScript("ArticlesView.js");
												$this->newPage->addScript("ckeditor/ckeditor.js");
												//$this->newPage->addDialog("addComment",["title"=>"Добавить комментарий",	"autorize"=>true]);
												unset($article);
								}else header("Location: /$this->partName");
								
								
				}
				public function Show(){
								$this->newPage->setTitle($this->pageTitle);
								$this->newPage->setContent("$this->pageName.php");
								$this->newPage->showPatch=true;
								$this->newPage->build();
				}
				public function set_patch(array &$patch):int{
								$addPatch = true;
								$length=0;
								//Если конец пути
								if(empty($patch)){
												$length = $this->index($_POST,-1);
												$addPatch = false;
												return $length;
								}
								$page =	strtolower(array_pop($patch));
								//Если статья
								if(preg_match("/^Article_[0-9]*$/ui",	$page)){
												$addPatch = false;
												$this->view($page);
												return $length;	
								}
								//Если страница
								if(is_numeric($page)){
												$addPatch = false;
												array_push($patch,	$page);
												$length = $this->index($_POST,-1,$patch);
												return 0;
								}
								//Если выбор
								switch($page){
												case "index": 
																$length = $this->index($_POST,-1,$patch);
																$addPatch = false;
																break;
												case "news": 
																$length = $this->index($_POST,0,$patch);
																$addPatch = false;
																break;
												case "info":
																echo $this->info($_POST);
																exit();
												case "tutorials":
																$length = $this->index($_POST,2,$patch);
																$addPatch = false;
																break;
												case "digests":
																$length = $this->index($_POST,1,$patch);
																$addPatch = false;
																break;
												case "create": $this->create($_POST);
																break;
												case "edit": $this->edit($_POST,$patch);
																$addPatch = false;
																break;
												case "delete":  
																$res = $this->remove($patch);
																if($res!=0){
																				echo "При удалении произошла ошибка №$res";
																				exit();
																}
																header("Location: /$this->partName");
																break;			
												default	: 
																header("Location: /$this->partName");
																break;
								}
								if($addPatch) $this->newPage->addPart($this->pageTitle,	$this->pageName);
								//Ограничение длины пути
								return $length;
				}
				public function set(&$patch){
								$page="";
								if(!empty($patch)){
												$page = $patch[count($patch)-1];
								}
								//echo "$page $this->partName";
								$length = $this->set_patch($patch);
								//echo $page;
								if(count($patch)>$length){
												header("Location: /$this->partName/$page");
								}
				}
				public function info($post){
								/*
								 Post:{
												UserId
												Mark
												Search
												Page
								 }
								 Return:{
												Limit
												Pages-
												Count
												Items
												
								 }
								 */
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
												$where["issetMark(ArtId,1,$UserId,$mark)"] = "1";
								}
								if(isset($post["search"])){
												$this->articles->SearchBy(["Name","Description","Tags"],$post["search"]);
								}
								$count = $this->articles->count($where);
								$limit = getLimit("Articles");
								$orders = getOrderBy("Articles");
								$this->articles->orderBy($orders);
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
												$this->articles->Limit($limit,$limit*($page-1));
												$info["Page"] = $page;
								}
								$items = $this->articles->getArticles($where);
								$info["Items"] = $items;
								unset($items);
								return json_encode($info);
				}
				private function Editing(int $ArtId):bool{
								if(!IsAutorized())return false;
								if(isRole(Role::Blocked)) return false;
								if(RoleAbove(Role::Moderator)) return true;
								$article = $this->articles->getById($ArtId);
								if($article==null)return false;
								if($article["UserId"]==$_SESSION["UserId"]) return true;
								return false;
				}
}

if(!isset($patch)){ 
				header("Location: \ ");
}
else{
				$artCont = new ArticleController();
				$artCont->set($patch);
				$artCont->Show();
				unset($artCont);
}
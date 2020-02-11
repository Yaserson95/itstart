<?php
include_once	'modules/structures/Groups.php';
include_once	'modules/structures/Discussions.php';
include_once	'modules/structures/Articles.php';
include_once	'modules/structures/searchp.php';
class SearchController{
				private $partName = "Search";
				private $pageName = "all";
				private $pageTitle = "Поиск";
				private $db;
				private $newPage;
				public	function	SearchController()	{
								$this->db = new Itstart_db();
								//$this->groups = new Groups($this->db);
								$this->newPage = new PageBuilder();
								$this->newPage->setContentFolder("content/$this->partName");
								$this->newPage->addPart("Главная",	"/");
								$this->newPage->addPart($this->pageTitle,	$this->partName);
								$this->newPage->setTitle($this->pageTitle);
								$this->newPage->addScript("Search.js");
				}
				public function Articles(array &$patch, string $text){
								$this->pageTitle = "Статьи";
								$this->pageName = "Articles";
								$search = new Articles($this->db);
								$search->SearchBy(["Description","Name"],	$text);
								$search->addLike("Tags",	$text,true);
								$this->newPage->addData("items",$search->getArticles());
								
								return 0;
				}
				public function Discussions(array &$patch, string $text){
								$this->pageTitle = "Обсуждения";
								$this->pageName = "Discussions";
								$search = new Discussions($this->db);
								$search->SearchBy(["Title"],	$text);
								$search->addLike("Tags",	$text,true);
								$this->newPage->addData("items",$search->Search());
								return 0;
				}
				public function Groups(array &$patch, string $text){
								$this->pageTitle = "Группы";
								$this->pageName = "Groups";
								$search = new Groups($this->db);
								$search->SearchBy(["Description","Title","Theme"],	$text);
								$this->newPage->addData("items",$search->Find());
								return 0;
				}
				public function All(array &$patch, string $text){
								$this->pageTitle = "Все разделы";
								$this->pageName = "All";
								$search = new Searchp($this->db);
								$search->SearchBy(["Description","Title"],	$text);
								//echo $text;
								$this->newPage->addData("items",$search->Find());
								return 0;
				}
				public function set_patch(array &$patch){
								if(empty($patch)){
													header("Location: /$this->partName/All");
								}
								$page = strtolower(array_pop($patch));
								$search = "";
								if(!empty($patch)){
												$search = array_pop($patch);
								}
								$result = -1;
								switch	($page){
												case "all":
																$result = $this->All($patch,$search);
																break;
												case "articles":
																$result = $this->Articles($patch,$search);
																break;
												case "groups":
																$result = $this->Groups($patch,$search);
																break;
												case "discussions":
																$result = $this->Discussions($patch,$search);
																break;
												default	:
																header("Location: /$this->partName/All");
								}
								$this->newPage->addPart($this->pageTitle,	$page);
								$this->newPage->addData("search",$search);
								return $result;
				}
				public function Set(&$patch){
								$length = $this->set_patch($patch);
								if(count($patch)>$length){
												header("Location: /$this->partName/All");
								}
								$this->newPage->setContent($this->pageName.".php");
								$this->newPage->setTitle($this->pageTitle);
								$this->newPage->Build();
				}
}
//exit();
if(isset($patch)){
				$profile = new SearchController();
				$profile->Set($patch);
}

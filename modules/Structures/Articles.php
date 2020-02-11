<?php
include_once	'DbTable.php';
include_once	'modules/Forms/Article.php';
include_once	'modules/autorize.php';
class	Articles	extends DbTable{
				protected $tableName = "Articles";
				private $ViewInfo = "Articlesinfo";
				private $textSize = 10000;
				private $imWidth = 300;
				private $imheight = 200;
				private $Itdb;
				public function Articles(Itstart_db $base){
								$this->Itdb = $base;
								parent::DbTable($base);
				}
				public function getArticles(array $where = null):array{
								$table = $this->tableName;
								$this->tableName = $this->ViewInfo;
								$data = $this->Find(null,$where);
								$this->tableName = $table;
								if(empty($data))return [];
								return $data;
				}
				public function getArticleById($artId,int $mode = Article::Preview):Article{
								$table = $this->tableName;
								$this->tableName = $this->ViewInfo;
								$result = $this->Find(null,	["ArtId"=>$artId]);
								$Art = new Article();
								$Art->setMode($mode);
								if(!empty($result)){
												if($mode==Article::Edit){
																$filename = getUsersData($result[0]["Nickname"])."/Articles/Article_$artId.htm";
																if(file_exists($filename)){
																				$result[0]['text'] = file_get_contents($filename);
																}
												}
												$Art->setData($result[0]);
												$Art->setValue("Mini",	getUsersUrl($result[0]["Nickname"])."/Articles/Article_$artId.png");
								}
								$this->tableName = $table;
								return $Art;
				}
				public function getById(int $artId){
								$table = $this->tableName;
								$this->tableName = "Articlesinfo";
								$result = $this->Find(null,	["ArtId"=>$artId]);
								$this->tableName = $table;
								if(empty($result)) return null;
								return $result[0];
				}
				public	function Delete(Article $article):int{
								$artId = $article->getValue("ArtId");
								$filename = getUsersData($article->getValue("Nickname"))."/articles/article_$artId.";
								if(!$this->Remove($article->getKeys())) return 1;
								include_once	'modules/structures/comments.php';
								include_once	'modules/structures/marks.php';
								$comments = new Comments($this->Itdb);
								$comments->removeBrunch($artId,	1);
								unset($comments);
								$marks = new Marks($this->Itdb);
								$marks->Remove(["TypePar"=>1,"Parent"=>$artId]);
								unset($marks);
								foreach	(["png","htm"]	as	$value)	{
												if(file_exists($filename.$value)){
																unlink($filename.$value);
												}
								}
								return 0;
				}
				public function Create(Article $art):int{
								$nerror=0;
								//Если установлен режим не "создание"
								if($art->getMode()!=Article::Create) return 1;
								$data = $art->getData(true);								
								$text = $data["text"];
								$mini = $_SERVER["DOCUMENT_ROOT"]."/".$data["Mini"];
								unset ($data["text"]);
								unset ($data["Mini"]);
								$inc = $this->getStatus("Auto_increment");
								$filename = currentUserData()."/articles/article_$inc";
								if($this->Insert($data)){
												//Текст статьи
												if(strlen($text)>$this->textSize)	$nerror = 2;
												$file = fopen("$filename.htm",	"w");
												if(!$file) $nerror = 3;
												fwrite($file,	$text);
												fclose($file);
												//Изображение
												if(!exif_imagetype($mini)) $nerror =  4;
												$image = imageFormarter($mini,$this->imWidth,$this->imheight);
												if(!imagepng($image,"$filename.png")) $nerror =  5;
								}
								else{
												return 6;
								}
								if($nerror!=0){
												if(file_exists("$filename.htm")){
																unlink("$filename.htm");
												}
												if(file_exists("$filename.png")){
																unlink("$filename.png");
												}
												$this->Remove(["ArtId"=>$inc]);
												return $nerror;
								}
								return 0;
				}
				public function Edit(Article $art):int{
								//Если установлен режим не "создание"
								if($art->getMode()!=Article::Edit) return 1;
								$data = $art->getData(true);
								//Вынимаем текст
								$text = $data["text"];
								unset ($data["text"]);
								$mini = $_SERVER["DOCUMENT_ROOT"]."/".$data["Mini"];
								unset ($data["Mini"]);
								$filename = getUsersData($data["Nickname"])."/Articles/Article_".$art->getValue("ArtId");
								unset ($data["Nickname"]);
								if(!$this->Update($data,	$art->getKeys())) return 3;
								$file = fopen("$filename.htm",	"w");
								if(!$file) return 2;
								fwrite($file,	$text);
								fclose($file);
								if(strtolower($mini)!=	strtolower("$filename.png")){
												//Изображение
												if(!exif_imagetype($mini)) return 4;
												$image = imageFormarter($mini,$this->imWidth,$this->imheight);
												if(!imagepng($image,"$filename.png")) return 5;
								}
								return 0;
				}
}

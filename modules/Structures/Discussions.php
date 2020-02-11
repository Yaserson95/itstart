<?php
include_once	'modules/structures/dbtable.php';
include_once	'modules/forms/Discussion.php';
include_once	'modules/autorize.php';
class	Discussions	extends DbTable{
				protected $tableName = "Discussion";
				private $Idb;
				public function Discussions(Itstart_db $base){
								$this->Idb = $base;
								parent::DbTable($base);
				}
				private function gr_putch(int $groupId):string{
								return $_SERVER["DOCUMENT_ROOT"]."/Data/Groups/Group_$groupId/Discussions";
				}

				public function Create(Discussion $disc){
								$Data = $disc->getData();
								$text = $Data["Text"];
								unset($Data["Text"]);
								$groupPt = $this->gr_putch($Data["GroupId"]);
								if(!file_exists($groupPt)){
												return -1;
								}
								$DiscId = $this->getStatus("Auto_increment");
								//echo $DiscId;
								if($this->Insert($Data)){
												file_put_contents("$groupPt/Discussion_$DiscId.htm",	$text);
								}
								else return -2;
								return $DiscId;
				}
				public function Edit(Discussion $disc){
								$Data = $disc->getData(false);
								if($this->Count($disc->getKeys())==0){
												return 1;
								}
								$text = $Data["Text"];
								$patch = $this->gr_putch($Data["GroupId"]);
								$discId = $disc->getValue("DiscId");
								//Выкидываем то что не нужно
								$Data = array_diff_key($Data,["Text"=>1,"UserId"=>1,"GroupId"=>1]);
								if(!$this->Update($Data,$disc->getKeys())) return 3;
								if(!file_exists($patch)) return 2;
								file_put_contents("$patch/Discussion_$discId.htm",	$text);
								return 0;
				}
				public function getById(int $DiscId){
								$rez = $this->Find(null,["DiscId"=>$DiscId]);
								if(empty($rez)){
												return null;
								}
								$disc = new Discussion();
								$disc->setData($rez[0]);
								$filePatch = $this->gr_putch($rez[0]["GroupId"])."/Discussion_$DiscId.htm";
								if(file_exists($filePatch)){
												$disc->setValue("Text",	file_get_contents($filePatch));
								}
								else{
												return null;
								}
								return $disc;
				}
				public function Delete(Discussion $disc){
								include_once	'modules/structures/comments.php';
								include_once	'modules/structures/marks.php';
								$comments = new Comments($this->Idb);
								$discId = (int)$disc->getValue("DiscId");
								if(!$this->Remove($disc->getKeys())) return 3;
								if(!$comments->removeBrunch($discId,	2))return 1;
								$marks = new Marks($this->Idb);
								if(!$marks->Remove(["Parent"=>$discId,"TypePar"=>2]))return 2;
								$file = $this->gr_putch($disc->getValue("GroupId"))."/Discussion_$discId.htm";
								if(file_exists($file)){
												unlink($file);
								}
								return 0;
				}
				public function Search($values	=	null,	$where	=	null):	array	{
								$table = $this->tableName;
								$this->tableName = "Discussionsinfo";
								$result = $this->Find($values,	$where);
								$this->tableName = $table;
								return $result;
				}
}

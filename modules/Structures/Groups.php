<?php
include_once	'modules/structures/dbtable.php';
include_once	'modules/forms/group.php';
include_once	'modules/autorize.php';

class	Groups	extends DbTable{
				private $imWidth = 300;
				private $imheight = 200;
				protected $tableName = "Usgroup";
				private $savePatch="Data/Groups";
				public function Groups(Itstart_db $base){
								$this->savePatch = $_SERVER["DOCUMENT_ROOT"]."/$this->savePatch";
								if(!file_exists($this->savePatch)){
												mkdir($this->savePatch);
								}
								parent::DbTable($base);
				}
				public function Create(Group $group){
								$data = $group->getData(true);
								$body = "";
								$mini = "";
								if(isset($data["Body"])){
												$body = $data["Body"];
												unset($data["Body"]);
								}
								if(isset($data["Mini"])){
												$mini = $_SERVER["DOCUMENT_ROOT"]."/".$data["Mini"];
												if(!exif_imagetype($mini)){
																return 4;
												}
												unset($data["Mini"]);
								}
								$inc = $this->getStatus("Auto_increment");
								if($this->Insert($data)){
												$dir = "$this->savePatch/Group_$inc";
												mkdir($dir);
												if(!empty($body)){
																$bfile = fopen("$dir/body.htm",	"w");
																if(fwrite($bfile,	$body)){
																				fclose($bfile);
																}
												}
												if(!empty($mini)){
																$image = imageFormarter($mini,$this->imWidth,$this->imheight);
																imagepng($image,"$dir/Mini.png");
												}
												mkdir("$dir/Discussions");
								}
								else{
												return 1;
								}
								return 0;
				}
				public function arrayById(int $groupid){
								$result = $this->Find(null,["GroupId"=>$groupid]);
								if(!empty($result)){
												return $result[0];
								}
								else return null;
				}
				public function getById(int $groupid){
								$data = parent::Find(null,["GroupId"=>$groupid]);
								if(empty($data)){
												return null;
								}
								$gr = $data[0];
								unset($data);
								$Url = "/Data/Groups/Group_$groupid";
								$patch = $_SERVER["DOCUMENT_ROOT"].$Url;
								if(file_exists("$patch/Mini.png")){
												$gr["Mini"] = "$Url/Mini.png";
								}
								if(file_exists("$patch/Body.htm")){
												$gr["Body"] = file_get_contents("$patch/Body.htm");
								}
								//echo var_dump($data);
								$group = new Group();
								$group->setData($gr);
								return $group;
				}
				
				public function Edit(Group $group):int{
								$Url = "/Data/Groups/Group_".$group->getValue("GroupId");
								$patch = $_SERVER["DOCUMENT_ROOT"].$Url;
								$Mini = $group->getValue("Mini");
								$Body = $group->getValue("Body");
								$group->setMode(Form::Update);
								$Set = $group->getData();
								$Keys = $group->getKeys();
								unset($group);
								if(!$this->Update($Set,	$Keys)){
												return 3;
								}
								if(!empty($Mini)){
												$Mini = strtolower($_SERVER["DOCUMENT_ROOT"].$Mini);
												if($Mini!=	strtolower("$patch/mini.png")){
																if(!exif_imagetype($mini)){
																				return 4;
																}
																else{
																				$image = imageFormarter($mini,$this->imWidth,$this->imheight);
																				imagepng("$patch/Mini.png");
																}
												}
								}
								if(!empty($Body)){
												file_put_contents("$patch/Body.htm",	$Body);
								}
								else{
												if(file_exists("$patch/Body.htm")){
																unlink("$patch/Body.htm");
												}
								}
								return 0;
				}
				public function Find($values	=	null,	$where	=	null):	array	{
								//echo var_dump($where);
								$table = $this->tableName;
								$this->tableName = "Groupsinfo";
								$res =  parent::Find($values,	$where);
								$this->tableName = $table;
								return $res;
				}
				public function getThemes():array{
								$table = $this->tableName;
								$this->tableName = "Themes";
								$res = parent::Find();
								$this->tableName = $table;
								return $res;
				}
}

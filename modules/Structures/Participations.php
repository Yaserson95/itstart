<?php
include_once	'modules/structures/dbtable.php';
include_once	'modules/forms/Participation.php';
include_once	'modules/autorize.php';

class	Participations	extends DbTable{
				protected $tableName = "Participation";
				public function Participations(Itstart_db $base){
								parent::DbTable($base);
				}
				public function Find($values	=	null,	$where	=	null):	array	{
								$table = $this->tableName;
								$this->tableName = "Partinfo";
								$result = parent::Find($values,	$where);
								$this->tableName = $table;
								return $result;
				}
				public function Role(int $UserId,int $GroupId):int{
								$pr = parent::Find("Post",["UserId"=>$UserId,"GroupId"=>$GroupId]);
								if(empty($pr)){
												return -1;
								}
								return (int)$pr[0]["Post"];
				}
}

<?php
include_once	'modules/structures/dbtable.php';
include_once	'modules/forms/mark.php';
include_once	'modules/autorize.php';
class	Marks	extends DbTable{
				protected $tableName = "Marks";
				public function Marks(Itstart_db $base){
								parent::DbTable($base);
				}
				public function getInfo($parent,$typePar){
								$table = $this->tableName;
								$this->tableName = "MarksInfo";
								$art = $this->Find(null,["Parent"=>$parent,"TypePar"=>$typePar]);
								$this->tableName = $table;
								return $art;
				}
}

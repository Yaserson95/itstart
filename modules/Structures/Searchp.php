<?php
include_once	'modules/structures/dbtable.php';
class	Searchp	 extends DbTable{
				protected $tableName = "Search";
				public function Searchp(Itstart_db $base){
								parent::DbTable($base);
				}
}

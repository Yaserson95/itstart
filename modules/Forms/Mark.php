<?php
include_once	'modules/forms/form.php';

class Mark extends Form{
				public function Mark(array $set){
								$this->setMetaData(__CLASS__);
				parent::Form($set);
				}
}

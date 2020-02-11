<?php
include_once	'modules/forms/form.php';

class	Participation	extends Form{
				public	function	Participation(array	$article	=	null)	{
								$this->setMetaData(__CLASS__);
								parent::Form($article);		
								
				}
}

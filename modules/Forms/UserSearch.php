<?php
include_once	'modules/forms/form.php';

class	Search	extends Form{
				public	function	Search(array	$article	=	null)	{
								$this->setMetaData(__CLASS__);
								parent::Form($article);		
				}
}

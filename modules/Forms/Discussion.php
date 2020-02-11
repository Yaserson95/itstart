<?php
include_once	'modules/forms/form.php';

class	Discussion	extends Form{
				public	function	Discussion(array	$article	=	null)	{
								$this->setMetaData(__CLASS__);
								parent::Form($article);		
				}
				public function setMode(int	$mode)	{
								switch	($mode){
												case Form::Create:
																$this->ignore=["DiscId","DatePubl"];
																break;
								}
								parent::setMode($mode);
				}
}

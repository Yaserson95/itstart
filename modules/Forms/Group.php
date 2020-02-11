<?php
include_once	'modules/forms/form.php';

class	Group extends Form	{
				public	function	Group(array	$article	=	null)	{
								$this->setMetaData(__CLASS__);
								parent::Form($article);		
				}
				public function setMode(int	$mode)	{
								switch($mode){
												case Form::Create:
																$this->ignore=["GroupId"];
																break;
												case Form::Update:
																$this->ignore=["Mini","Body"];
																break;
								}
								parent::setMode($mode);
				}
}

<?php

include_once	'Form.php';
class	Comment extends Form	{
				public function Comment(array $row=null){
								$this->setMetaData(__CLASS__);
								parent::Form($row);
				}
				public function setMode(int	$mode)	{
								switch	($mode){
												case Form::Create:
																$this->ignore=["ComId","DatePubl"];
																break;
												default: $mode=0;
																break;
								}
								parent::setMode($mode);
				}
				
}

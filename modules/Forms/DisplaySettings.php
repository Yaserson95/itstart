<?php
include_once	'Form.php';
/*
	* To change this license header, choose License Headers in Project Properties.
	* To change this template file, choose Tools | Templates
	* and open the template in the editor.
	*/

/**
	* Description of DisplaySettings
	*
	* @author yaros
	*/
class	DisplaySettings	extends Form{
				public function DisplaySettings(array $row=null){
								$this->setMetaData("Display");
								parent::Form($row);
				}
}

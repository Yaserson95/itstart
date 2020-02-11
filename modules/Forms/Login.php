<?php
include_once "Form.php";
class	Login	extends Form{
				protected $columns = [
								"Nickname"=>[
												"type"=>"string",
												"null"=>false,
												"input"=>"text"
								],
								"UserPswrd"=>[
												"type"=>"password",
												"null"=>false,
												"input"=>"password",
								]
				];
}

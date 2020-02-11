<?php
include_once	'Form.php';
class	Article extends Form	{
				public const Create=1;
				public const Preview = 2;
				public const TableShow = 3;
				public const Edit = 4;
				private $added = [
								"text"=>[
												"id"=>"artText",
												"alias"=>"Текст статьи",
												"type"=>"html",
												"input"=>"textarea",
												"null"=>false
								],
								"Nickname" => [
												"alias"=> "Ник",
												"type"=>"string",
												"null"=> false,
												"preg"=> "/^([A-z0-9_.]*)$/ui",
												"input"=> "text"
								]
				];
				public	function	Article(array	$article	=	null)	{
								$this->setMetaData(__CLASS__);
								parent::Form($article);		
								
				}
				public function setMode(int	$mode)	{
								switch($mode){
												case Article::Create: $this->ignore = ["ArtId","DatePubl"];
																$this->columns["text"] = $this->added["text"];
																break;
												case Article::Preview:{
																$this->columns["Nickname"] = $this->added["Nickname"];
																break;
												}
												case Article::TableShow:{
																$this->ignore = ["UserId","Tags","Mini"];
																$this->columns["ArtId"]["alias"] = "Номер";
																break;
												}
												case Article::Edit:{
																$this->ignore = ["DatePubl"];
																$this->columns["Nickname"] = $this->added["Nickname"];
																$this->columns["text"] = $this->added["text"];
																break;
												}
								}
								parent::setMode($mode);
				}
}

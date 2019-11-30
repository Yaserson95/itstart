<?php
$partName = "Home";

$newPage = new PageBuilder();
$newPage->setContentFolder("content/$partName");
$pageName = "";
$pageTitle="";
$newPage->addPart("Главная",	"/$partName");
if(isset($patch)){
				if(empty($patch)){
								$newPage->addScript("slider.js");
				}else{
								$page = array_pop($patch);
								switch($page){
												case "About":
												{ 
																$pageName = "About";
																$pageTitle = "О нас";
																break;
												}
												case "Policy":
												{
																$pageName = "Policy";
																$pageTitle = "Правила";
																break;
												}
												case "Help": 
												{
																$pageName = "Help";
																$pageTitle = "Помощь";
																break;
												}
												default	:{
																header("Location: /$partName");
																break;
												}
								}
								$newPage->setTitle($pageTitle);
								$newPage->setContent("$pageName.html");
								$newPage->addPart($pageTitle,	$pageName);
								$newPage->showPatch=true;
				}
				if(count($patch)>0){
								header("Location: /$partName/$page");
				}
				$newPage->build();
}

<?php
function getUserImages(string $Nickname){
				$dirpatch =  "Data/Users/$Nickname/images";
				echo "<duv id='images'>";
				if(file_exists($dirpatch)){
								$files = scandir($dirpatch);
								$preg="/(.png$)|(.jpg$)|(.gif$)|(.bmp$)/ui";
								foreach	($files	as	$key	=>	$value)	{
												if(!preg_match($preg,	$value)){
																unset($files[$key]);
												}else{
																echo "<div><img src = '/$dirpatch/$value'><div class='bar'></div></div>";
												}
								}
								
				}
				echo "</div>";
				echo "<br style='clear:both'/>";
}
function manageImage($conf,$img){
				$img = ltrim($img,"/");
				switch ($conf){
								case "delete":
												if(file_exists($img)){
																if(unlink($img)){
																				echo 0;
																}else echo 1;
												}else{
																echo 2;
												}
												break;
								default:break;
				}
}
//Если у пользователя нет папки
function initUserData($Nickname){
				$folders = ["articles","questions","images"];
				$filename = "Data/Users/".$Nickname;
				if(!file_exists($filename)){
								if(mkdir($filename)){
												foreach	($folders	as	$value)	{
																mkdir("$filename/$value");
												}
								}
				}
}
initUserData($_SESSION["Nickname"]);
$category = strtolower(array_pop($patch));
switch	($category){
				case "articles":
								break;
				case "comments":
								break;
				case "questions":
								break;
				case "images":
								
//								if(!empty($_POST)){
//												manageImage($_POST["conf"],$_POST["file"]);
//												
//												exit();
//								}
//								getUserImages($_SESSION["Nickname"]);
								break;
				default	:break;
}
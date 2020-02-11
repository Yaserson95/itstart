<?php
				include_once	'modules/autorize.php';
?>

<!DOCTYPE html>
<html>
				<head>
								<meta charset="UTF-8">
								<?php
								if	(!is_null($this->styles))	{
												foreach	($this->styles	as	$style)	{
																echo	"<link rel='stylesheet' type='text/css' href='$this->sources/styles/$style'>";
												}
								}
								if	(!is_null($this->scripts))	{
												foreach	($this->scripts	as	$script)	{
																echo	"<script type='text/javascript' src='$this->sources/scripts/$script'></script>";
												}
								}
								foreach	($this->head as $headItem){
												echo $headItem."\n";
								}
								echo	"<title>$this->title</title>";
								?>
				</head>
				<body>
								<div id="dialogs">
												<?php
												foreach	($this->dialogs as $dial => $prop){
																$filename = $_SERVER["DOCUMENT_ROOT"]."/modules/Dialogs/$dial.php";
																$title = "";
																$params = "";
																if(isset($prop['autorize'])){
																				if(IsAutorized()!=$prop['autorize']){
																								continue;
																				}
																				unset($prop['autorize']);
																}
																foreach	($prop as $key=>$val){
																				if(strtolower($key)==='id')continue;
																				$params.=" $key = '$val'";
																}
																if(file_exists($filename)){
																				echo "<div id = '$dial'$params>";
																				include_once	$filename;
																				echo "</div>";
																}
																
												}
												?>
								</div>
								<?php
								
								//include_once	'modules/share/loginform.php';
								include_once	"modules/share/header.php";
								if(count($this->viewPatch)>0&&$this->showPatch){
												echo "<p class='patch'>";
												$url="";
												foreach	($this->viewPatch 	as $n=>	$item)	{
																if($n>0) echo " > ";
																$url.=	trim($item['href'],"/")."/";
																echo $this->htmlForms->ActionLink($item['title'],$url);
												}
												echo "</p>";
								}
								?>
								<section id="content">
												<?php
												include_once	"$this->contentFolder/$this->content";
												?>
								</section>
								<footer id="footer">
												<div class="hlp">
																<a href="/service/support">Тех. поддержка</a>
																<a href="/service/policy">Правила сайта</a>
																<a href="/service/offer">Ваши предложения</a>
																<a href="/service/advertising">Размещение рекламы</a>
																<a href="/service/help">Помощь</a>
												</div>
												<br style="clear:both">
								</footer>
				</body>
</html>
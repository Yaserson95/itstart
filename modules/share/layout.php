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
								echo	"<title>$this->title</title>";
								?>
				</head>
				<body>
								<div id="regform" class="dialog" title="Регистрация">
												<?php include_once	'modules/share/regForm.php';?>
								</div>
								<div class="dialog" title="Войти" id="loginform" >
												<form method="post" name="login" class="dialForm">
																<table border="0" cellpadding="2" cellspacing="0" >
																				<tbody>
																								<tr>
																												<td><b>Логин/e-mail:</b></td>
																												<td><input name="login" required="required" type="email" /></td>
																								</tr>
																								<tr>
																												<td><b>Пароль:</b></td>
																												<td><input name="password" required="required" type="password" /></td>
																								</tr>
																				</tbody>
																</table>
												</form>
								</div>
								<?php
								include_once	"modules/share/header.php";
								if(count($this->viewPatch)>0&&$this->showPatch){
												echo "<p class='patch'>";
												foreach	($this->viewPatch 	as $n=>	$item)	{
																if($n>0) echo " > ";
																echo $this->htmlForms->ActionLink($item['title'],$item['href']);
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
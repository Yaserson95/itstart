<?php
$inputs = new myHtmlHalper();
$user = $this->Data("user");
if(!$user->isEmpty()){
				echo "<div class ='part'><h1>".$user->getValue("Firstname")." ".$user->getValue("Surname")."</h1></div>";
}
echo "<table class='userInfo'>\n";
foreach($user->getViewColumns() as $name){
				echo "<tr>\n"
								. "<td><b>".$user->getProperty($name,	"alias")."</b></td>\n"
								. "<td>".$user->getField($name)
								."</td>\n"
				. "</tr>\n";
}
echo "</table>\n";

?>

<div class="manage">
				<h6>Настройки:</h6>
				<div class="photo" id="userphoto">
								<img src="<?php echo $this->Data("photo")?>" id="photo"/>
								<div class="load" id="loadPhoto">
												<p><img src="/source/IMG/changeIco.png"/><br/>
												Изменить фото</p>
								</div>
				</div>
				<h6>Основные:</h6>
				<p><a href="/Profile/Manage">Изменить данные</a></p>
				<p><a href="/Profile/Display">Отображение</a></p>
				<h6>Безопасность:</h6>
				<p><a href="/Profile/Nickname">Изменить ник</a></p>
				<p><a href="/Profile/Password">Изменить пароль</a></p>

</div>
<br style="clear:both"/>
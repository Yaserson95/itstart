<?php include_once	'modules/Autorize.php';?>
<ul class="menu">
	<li><a href="/Articles">Статьи</a>
		<div><ul>
			<li><a href="/Articles/News">Новости</a></li>
			<li><a href="/Articles/Tutorials">Инструкции</a></li>
			<li><a href="/Articles/Digests">Обзоры</a></li>
			<li><a href="/Articles/Create">Создать</a></li>
		</ul></div>
	</li>
	<li><a href="/groups">Сообщества</a>
		<div><ul>
			<li><a href="/Groups/developing">Разработка ПО</a></li>
			<li><a href="/Groups/designer">Дизайн</a></li>
			<li><a href="/Groups/computers">Компьютеры</a></li>
			<li><a href="/Groups/micro">Микроконтроллеры</a></li>
		</ul></div>
	</li>
<?php
if(IsAutorized()){
?>		
	<li><a href="/Profile"><?php echo $_SESSION["Nickname"]?></a>
		<div><ul>
			<li><a href="/Profile/View">Информация</a></li>
				<li><span>Сообщения</span></li>
				<li><a href="/Profile/Logout">Выйти</a></li>
		</ul></div>
	</li>
<?php
}else{
?>
	<li><a href="/Account">Авторизация</a>
		<div><ul>
			<li><a href="/Account/Register">Регистрация</a></li>
			<li><span id="login">Войти</span></li>
		</ul></div>
	</li>
<?php
}
?>
	<li><a href="/">Главная</a>
		<div><ul>
			<li><a href="/Home/Policy">Правила</a></li>
			<li><a href="/Home/About">О нас</a></li>
			<li><a href="/Home/Help">Помощь</a></li>
		</ul></div>
	</li>
</ul>

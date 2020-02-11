<header id="header">
				<?php
				echo	"<a href='/'><img src='$this->sources/IMG/logo.png'></a>";
				include_once	"menu.php";
				?>
				<form class='search' id = 'search' action='/Search/All' method='POST'>
								<input type='text' name='Query' placeholder='Введите запрос'/>
								<input type='submit' name='submit_q' value="Найти"/>
				</form>
</header>
<header id="header">
				<?php
				echo	"<a href='/'><img src='$this->sources/IMG/logo.png'></a>";
				include_once	"menu.php";
				?>
				<form name="search" class="search">
								<input type="query" name="text_q" placeholder="Введите запрос">
								<input type="submit" name="submit_q" value="Найти">
				</form>
</header>
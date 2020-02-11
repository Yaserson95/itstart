<?php
				$Group = $this->Data("group");
				$patch = "/Data/Groups/Group_".$Group["GroupId"];
				echo
				  "<div id = 'forumtop'>"
				 . "<img src='$patch/Mini.png'>"
					. "<div id='tools'><input type='hidden' id='us_info' value='".$this->Data("userinfo")."'/></div>"
					. "<h1>".$Group["Title"]."</h1>"
					. "<h2>".$Group["Theme"]."</h2>"
					. "<p class='descript'>".$Group["Description"]."</p>"
				 . "<br style='clear:both'>"
				. "</div>";
?>

<table id="groups" cellpadding="0" cellspacing="0">
		<tr>
			<td class="list">
				<h2 class="list_h2">Подробнее</h2>
				<?php
				if(file_exists($_SERVER["DOCUMENT_ROOT"]."/$patch/body.htm")){
								$body =  file_get_contents($_SERVER["DOCUMENT_ROOT"]."/$patch/body.htm");
								echo htmlspecialchars_decode($body);
				}
				else{
								echo "<p class='info'>Информация отсутствует</p>";
				}
				?>
			</td>
			<td class="categoryes">
				<h2>Контакты</h2>
				<?php
								echo getItem::Contacts($this->Data("Contacts"));
								echo "<h2>Пользователи (".$Group["Users"].")</h2>";
								echo getItem::Users($this->Data("users"));
				?>
			</td>
		</tr>
</table>
<?php
echo "<a href = '/Discussions/Group_".$Group["GroupId"]."'><h2 class='list_h2'>Обсуждения сообщества(".$Group["Discussions"].")</a> "
 . "<a href='/Discussions/Group_".$Group["GroupId"]."/Create'>Добавить</a></h2>";
?>

<div id="discussions">
</div>
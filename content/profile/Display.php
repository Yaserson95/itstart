<?php
$display = $this->Data("Display");
$Parts = [
				"Articles"=>"Статьи",
				"Groups"=>"Группы",
				"Discussions"=>"Обсуждения",
				"Comments"=>"Комментарии"
];
?>
<div class ='part'>
				<h1>Отображение</h1>
</div>
<form name="Display" method="post" action="/Profile/Display" id = "display" class='input_form'>
				<div>
				<table>
				<?php
				echo $this->Message("error");
				echo $this->Message("info");
				foreach	($Parts AS $part=>$title){
								echo "<tr><td colspan=2><h2>$title</h2></td></tr>";
				?>
								
								
												<tr>
												<?php
																echo "<td><p>".$display->getProperty($part."Sort","alias")."</p></td>\n"; 
																echo "<td>".$display->getHtmlField($part."Sort")."</td>\n"; 
												?>
												</tr>
												<tr>
												<?php
																echo "<td><p>".$display->getProperty($part."Order","alias")."</p></td>\n"; 
																echo "<td>".$display->getHtmlField($part."Order")."</td>\n"; 
												?>
												</tr>
												<tr>
												<?php
																echo "<td><p>".$display->getProperty($part."Limit","alias")."</p></td>\n"; 
																echo "<td>".$display->getHtmlField($part."Limit")."</td>\n"; 
												?>
												</tr>
								

<?php
				}
?>		
				</table>
				</div>
				<hr/>
				<input type="submit" value="Сохранить" class="ui-button ui-corner-all ui-widget">
</form>
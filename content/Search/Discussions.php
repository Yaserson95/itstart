<div id = 'forumtop'>
				<form id='search_part' method="POST" >
								<select name='parts'>
												<option value='All'>Все разделы</option>
												<option value='Articles'>Статьи</option>
												<option value='Groups'>Группы</option>
												<option value='Discussions' selected="selected">Обсуждения</option>
												
								</select>
								<input type='text' placeholder="Найти" name='text' value="<?php echo $this->Data("search")?>">
								<input type='submit' value="Найти" class='ui-button ui-widget ui-corner-all'>
				</form>
				<br style='clear:both'>
</div>
<?php
$items = $this->Data("items");
foreach	($items	as	$value)	{
				echo "<div class='themes disc'>"
				."<a class='themes ' href='/Discussions/Discussion_".$value["DiscId"]."'>"
				."<h1>".$value["Title"]."</h1></a>"
				."<p>Опубликовал: ".$value["Nickname"]." ".
								dateDiff($value["DatePubl"],date("Y-m-d H:i:s",time()))." назад</p>"
				."<p>в группе <a href='/Groups/Group_".$value["GroupId"]."'>".$value["Group"]."</a></p>";
			if(!empty($value["Tags"])){
							echo "<p class='tags'>";
							foreach	($this->Tags($value["Tags"]) as $tag){
											echo "	<a href='#'>$tag</a>";
							}
							echo "</p>";
			}
				
				echo "</div>";
}
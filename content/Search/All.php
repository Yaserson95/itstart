<div id = 'forumtop'>
				<form id='search_part' method="POST" >
								<select name='parts'>
												<option value='All' selected="selected">Все разделы</option>
												<option value='Articles'>Статьи</option>
												<option value='Groups'>Группы</option>
												<option value='Discussions'>Обсуждения</option>
												
								</select>
								<input type='text' placeholder="Найти" name='text' value="<?php echo $this->Data("search")?>">
								<input type='submit' value="Найти" class='ui-button ui-widget ui-corner-all'>
				</form>
				<br style='clear:both'>
</div>
<?php
foreach	($this->Data("items") as $item){
				switch((int)$item["TypePar"]){
								case 1:{
												$type =  "Статья";
												$href = "/Articles/Article_".$item["Parent"];
												$title = $item["Title"];
												$description = $item["Description"];
												break;
								}
								case 2:{
												$type =  "Группа";
												$href = "/Groups/Group_".$item["Parent"];
												$title = $item["Title"];
												$description = $item["Description"];
												break;
								}
								case 3:{
												$type =  "Обсуждение";
												$href = "/Discussion/Discussion_".$item["Parent"];
												$title = $item["Description"];
												$description = "";
												break;
								}
				}
				echo "<a href = '$href' class='themes disc'>";
				//."<a class='themes ' href='/Discussions/Discussion_".$item["DiscId"]."'>"
				echo "<h1>$title</h1>"
 . "<p>Тип: $type</p>"
				."<p>$description</p>"
				."<p>Опубликовал: ".$item["Nickname"]."</p>";

				echo "</a>";

}


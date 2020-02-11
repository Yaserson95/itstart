<div id = 'forumtop'>
				<form id='search_part' method="POST" >
								<select name='parts'>
												<option value='All'>Все разделы</option>
												<option value='Articles' selected="selected">Статьи</option>
												<option value='Groups'>Группы</option>
												<option value='Discussions'>Обсуждения</option>
												
								</select>
								<input type='text' placeholder="Найти" name='text' value="<?php echo $this->Data("search")?>">
								<input type='submit' value="Найти" class='ui-button ui-widget ui-corner-all'>
				</form>
				<br style='clear:both'>
</div>
<?php
	foreach($this->Data("items") as $item){
								echo "<a class='themes' href='/Articles/Article_".$item["ArtId"]."'><img src='".getUsersUrl($item["Nickname"])."/Articles/Article_".$item["ArtId"].".png'/>
												<h2>".$item["Name"]."</h2><p>".$item["Description"]."</p>
												<br style='clear:both'>
											</a>";
				}


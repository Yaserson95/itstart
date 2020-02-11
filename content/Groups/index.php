<div class ='part'>
				<h1><?php echo $this->title; ?></h1>
</div>
<p><a href ='/Groups/Create'>[+] Создать</a></p>
<?php
$groups = $this->Data("Groups");
if(empty($groups)){
				echo "<div class = 'void'>
				<div><h1>В данном разделе отсутствуют материалы!</h1>Вы можете <a href='/Groups/Create'>создать</a> их.</div>
				</div>";
}
else{
?>
<table id="groups" cellpadding="0" cellspacing="0">
		<tr>
			<td class="list">
				<?php
				foreach	($groups as $item){
								echo "<a class='themes' href='/Groups/Group_".$item["GroupId"]."'>"
								. "<img src='/Data/Groups/Group_".$item["GroupId"]."/Mini.png'/>
												<h2>".$item["Title"]."</h2>"
									. "<p>".$item["Description"]."</p>
												<br style='clear:both'>
											</a>";
				}
				?>
			</td>
			<td class="categoryes">
				<h2>Все темы:</h2>
				<?php
				foreach	($this->Data("Themes") as $item){
								echo	"<p><a href='/Groups/Theme/".$item["Theme"]."'>(".$item["Pop"].") ".$item["Theme"]."</a></p>";
				}
				?>
			</td>
		</tr>
</table>
<?php } ?>
<div class ='part'>
				<h1><?php echo $this->title; ?></h1>
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
?>
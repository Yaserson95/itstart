<div id='discview'>
<?php
$User = $this->Data("User");
$Disc = $this->Data("Disc");
$Group = $this->Data("Group");
echo "<div id='forumtop' class='discview'>"
 . "<h1>$this->title</h1>"
 . "<h2>Опубликовал: ".$User->getField("Nickname")." ".dateDiff($Disc->getField("DatePubl"),date("Y-m-d H:i:s",time()))." назад</h2>"
 . "<h2>в группе <a href='/Groups/Group_".$Group->getField("GroupId")."'>".$Group->getField("Title")."</a></h2>";
if(!empty($Disc->getValue("Tags"))){
				echo "<p class='tags'>";
				foreach	($this->Tags($Disc->getValue("Tags")) as $tag){
								echo "	<a href='#'>$tag</a>";
				}
				echo "</p>";
}

echo "<div class='bar'>";
echo "<div id='toolbar'></div>";
if($this->Data("Edit")){
				echo "<span id='Editing'></span>";
}
echo "<input type='hidden' value='".$Disc->getField("DiscId")."' id='DiscId'/>";
echo "</div>";
echo "</div>";

echo "<div class='article_text'>".$Disc->getField("Text")."</div>";
?>
</div>
<div id="comments"></div>

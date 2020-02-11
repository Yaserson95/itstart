<?php
$article = $this->Data("article");
echo "<input type = 'hidden' id='editing' value='".$this->Data("editing")."'>";
?>


<div id = "forumtop">
				<img src="<?php echo $article->getValue("Mini");?>">
				<h1><?php echo $article->getField("Name");?></h1>
				<p class="descript"><?php echo $article->getField("Description");?></p>
				<h2>Теги:</h2>
				<p class="tags">
				<?php
				if(!empty($article->getValue("Tags"))){
								foreach	($this->Tags($article->getValue("Tags")) as $tag){
												echo "	<a href='#'>$tag</a>";
								}
				}
				?>
				</p>
				<h2>Опубликовал:
				<?php
								echo $article->getValue("Nickname")." ".$article->getValue("DatePubl");
				?></h2>
				<br style="clear:both">
				<div id="toolbar"></div>
</div>

<div class="article_text">
<?php
$file =  $this->Data("filename");
if(file_exists($file)){
				echo file_get_contents($file);
}
?>
</div>
<div id="comments">
				<hr/>
				<input type='hidden' id='ArtId' value = '<?php echo $article->getValue("ArtId")?>'>

</div>
<div class ='part'>
				<h1><?php echo $this->title; ?></h1>
</div>

<?php
$inputs = new myHtmlHalper();
$article = $this->Data("article");
$target = "/Articles/".$this->Data("target");
?>
<form class ="input_form" method="post" action="<?php echo $target; ?>" id='article_form'>
				<div class="group1">
<?php
				echo $this->Message("Error");
 ?>
				<br style="clear:both"/>
								<table class = 'InputGroup'>
<?php
$auto = ["text","Mini","UserId","Nickname"];
$arr = array_diff($article->getViewColumns(),$auto);
foreach($arr as $name){
				echo "<tr>\n"
								. "<td>".$article->getProperty($name,	"alias")."</td>\n"
								. "<td>".$article->getHtmlField($name)
								."</td>\n"
				. "</tr>\n";
}
				echo "<tr>\n"
								. "<td>".$article->getProperty("Mini",	"alias")."</td>\n"
								. "<td>".$article->getHtmlField("Mini");
								echo "</td>\n"
				. "</tr>\n";
?>
								</table>
								<hr/>
				<?php		echo $inputs->Submit("accept","Сохранить",["class"=>"ui-button"]);?>
				</div>
				
				<div class = "group2">
								<br style="clear:both"/>
								<?php
												echo $article->getHtmlField("text");
								?>
								
				</div>
				
</form>
<div class ='part'>
				<h1><?php echo $this->title; ?></h1>
</div>
<form class ="input_form" method="post" action="<?php echo $this->Data("target");;?>" id='article_form'>
				<div class="group1">
<?php
				echo $this->Message("Error");
 ?>
				<br style="clear:both"/>
								<table class = 'InputGroup'>
<?php
$inputs = new myHtmlHalper();
$Group = $this->Data("Group");
$auto = ["OwnerId","GroupId","Body"];
$arr = array_diff($Group->getViewColumns(),$auto);
foreach($arr as $name){
				echo "<tr>\n"
								. "<td>".$Group->getProperty($name,	"alias")."</td>\n"
								. "<td>".$Group->getHtmlField($name)
								."</td>\n"
				. "</tr>\n";
}
?>
								</table>
								<hr/>
				<?php		echo $inputs->Submit("accept","Сохранить",["class"=>"ui-button"]);?>
				</div>
				
				<div class = "group2">
								<br style="clear:both"/>
								<?php
												echo $Group->getHtmlField("Body");
								?>
								
				</div>
				
</form>
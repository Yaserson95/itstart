<div class ='part'>
				<h1>Изменить ник</h1>
</div>
<form name="register" method="post" action="/Profile/Nickname" id='register' class='InputForm'>
<?php 
				echo $this->Message("Error");
				echo $this->Message("message");
?>
				<table class = 'InputGroup'>
<?php
$inputs = new myHtmlHalper();
$nickForm = $this->Data("Nick");
//Выкидываем ненужные колонки
foreach($nickForm->getViewColumns() as $name){
				echo "<tr>\n"
								. "<td>".$nickForm->getProperty($name,	"alias")."</td>\n"
								. "<td>".$nickForm->getHtmlField($name)."</td>\n"
				. "</tr>\n";
}

?>
				</table>
				
				<?php echo $inputs->Submit("accept","Сохранить",['class'=>'ui-button ui-corner-all ui-widget']);?>
</form>
<?php



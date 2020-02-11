<div class ='part'>
				<h1>Изменить пароль</h1>
</div>
<form name="register" method="post" action="/Profile/Password" id='register' class='InputForm'>
<?php 
				echo $this->Message("Error");
				echo $this->Message("message");
?>
				<table class = 'InputGroup'>
<?php
$inputs = new myHtmlHalper();
$passForm = $this->Data("password");
//Выкидываем ненужные колонки
foreach($passForm->getViewColumns() as $name){
				echo "<tr>\n"
								. "<td>".$passForm->getProperty($name,	"alias")."</td>\n"
								. "<td>".$passForm->getHtmlField($name)."</td>\n"
				. "</tr>\n";
}

?>
				</table>
				
				<?php echo $inputs->Submit("accept","Сохранить",['class'=>'ui-button ui-corner-all ui-widget']);?>
</form>
<?php

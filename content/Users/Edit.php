<div class ='part'>
<?php
$User = new User();
$User = $this->Data("user");
$columns = $this->Data("columns");
echo "<h1>".$this->title."</h1>";
?>
</div>
<form name="manage" method="post" action="<?php echo $this->Data("action")?>" id='register' class='input_form'>
<?php 
				echo $this->Message("Error");
				echo $this->Message("message");
?>
<div class='group'>
				<table class = 'InputGroup'><?php
								$User->setColumnProperty("Priority",	"input",	"select");
								foreach	($columns	as	$value)	{
												echo "<tr>";
												echo "<td>".$User->getProperty($value,	"alias")."</td>";
												echo "<td>".$User->getHtmlField($value)."</td>";
												echo "</tr>";
								}			
				?></table>
				<hr/>
				<input type="submit" value="Сохранить" class="ui-button">
</div>

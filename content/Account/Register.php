<div class ='part'>
				<h1>Регистрация</h1>
</div>
<?php
//include "modules/forms.php";
$inputs = new myHtmlHalper();
$user = $this->Data("User");
if(!empty($_POST)){
				$user->Set($_POST);
}
$user->setMode(User::Register);
?>
<div class="group">
<form name="register" method="post" action="/account/Register" class='input_form'>
<?php
				echo $this->Message("Error");
 ?>
				<table class = 'InputGroup'>
<?php
			//	if(!$user->isEmpty()){
								foreach($user->getViewColumns() as $name){
												echo "<tr>\n"
																. "<td>".$user->getProperty($name,	"alias")."</td>\n"
																. "<td>".$user->getHtmlField($name)
																."</td>\n"
												. "</tr>\n";
							//	}
				}
?>
				</table>
				<hr/>
				<?php		echo $inputs->Submit("accept","Регистрация",['class'=>'ui-button']);

?>
				
</form>
</div>
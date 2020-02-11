<div class ='part'>
				<h1>Изменить личные данные</h1>
</div>
<form name="manage" method="post" action="/Profile/Manage" id='register' class='input_form'>
<?php 
				echo $this->Message("Error");
				echo $this->Message("message");
?>
				<div class='group'>		
								<table class = 'InputGroup'>
<?php
				$inputs = new myHtmlHalper();
				$user = $this->Data("user");
				$user->setMode(User::Update);
				$user->isValid();
				if(!$user->isEmpty()){
								foreach($user->getViewColumns() as $name){
												echo "<tr>\n"
																. "<td>".$user->getProperty($name,	"alias")."</td>\n"
																. "<td>".$user->getHtmlField($name)
																."</td>\n"
												. "</tr>\n";
								}
				}
?>
								</table>
								<hr/>
<?php
				echo $inputs->Submit("accept","Сохранить",['class'=>'ui-button ui-corner-all ui-widget']);
				
?>
				</div>
</form>
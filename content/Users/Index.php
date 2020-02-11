<?php
//$User = new User();
$User = $this->Data("user");
echo "<div class ='part'><h1>".$User->getValue("Firstname")." ".$User->getValue("Surname")."</h1></div>";
?>
<table style='width: 100%' class='userinfo'>
				<tr>
								<td><div class='userinfo'><?php
								$columns = array_diff($User->getViewColumns(),["Firstname","Surname","UserPswrd","UserId"]);
								foreach	($columns as $col){
												echo "<p><span>".$User->getProperty($col,"alias").":</span>".$User->getField($col)."</p>";
								}
								?></div></td>
								<td style='width: 150px' class='manage'>
												<div class="photo">
												<?php echo "<img src='".$User->getPhoto()."'/>";?>
												</div>
												<?php 
																if($this->Data("Edit")){
																				echo "<p><a href='/Users/".$User->getValue("Nickname")."/Edit'>Редактировать</a></p>";
																}
												?>
								</td>
				</tr>
</table>




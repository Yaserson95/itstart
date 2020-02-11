<?php
include_once "modules/Forms/Login.php";
$login = new Login();
?>
<form method="post" name="login" class="input_form">
				<table border="0" cellpadding="2" cellspacing="0" >
								<tr>
												<td><b>Ник/Логин:</b></td>
												<td>
																<?php $login->printHtmlField("Nickname")?>
																<p class="error" id="NicknameError"></p>
												</td>
								</tr>
								<tr>
												<td><b>Пароль:</b></td>
												<td>
																<?php $login->printHtmlField("UserPswrd")?>
																<p class="error" id="UserPswrdError"></p>
												</td>
								</tr>
				</table>
</form>
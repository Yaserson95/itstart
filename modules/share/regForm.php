<form name="regform" class="dialForm">
				<div id = "regTabs">
								<ul>
												<li><a href="#regTab1">Основное</a></li>
												<li><a href="#regTab2">Безопасность</a></li>
												<li><a href="#regTab3">Дополнительно</a></li>
								</ul>
								<div id="regTab1">
												<table border="0" cellpadding="5" cellspacing="0">
																<tr>
																				<td>Имя:</td>
																				<td><input name="FirstName" required="required" type="text" /></td>
																</tr>
																<tr>
																				<td>Фамилия:</td>
																				<td><input name="LastName" required="required" type="text" /></td>
																</tr>
																<tr>
																				<td>E-mail:</td>
																				<td><input name="mail" type="email" /></td>
																				
																</tr>
												</table>
								</div>
								<div id="regTab2">
												<p>Логин пароль</p>
								</div>
								<div id="regTab3">
												<table border="0" cellpadding="5" cellspacing="0">
																										
																<tr>
																				<td>Город:</td>
																				<td><input name="City" type="text" /></td>
																</tr>
																<tr>
																				<td>Телефон:</td>
																				<td><input name="phone" type="tel" /></td>
																</tr>

																<tr>
																				<td>Пол:</td>
																				<td>  
																								<label for="male">Мужской</label>
																								<input type="radio" name="sex" id="male">
																								<label for="female">Женский</label>
																								<input type="radio" name="sex" id="female">
																				</td>
																</tr>
																<tr>
																				<td><p>Год рождения:</p><br><div id="birth"></div></td>
																				<td><p>О себе:</p><textarea name="resum"></textarea></td>
																</tr>	
												</table>
								</div>
				</div>
</form>

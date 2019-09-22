<form name="regform" class="dialForm">
<div id = "regTabs">
				<ul>
						<li><a href="#regTab1">Данные</a></li>
						<li><a href="#regTab2">Безопасность</a></li>
				</ul>
				<div id="regTab1">
								<table border="0" cellpadding="5" cellspacing="0">
												<tbody>
																<tr>
																				<td>Имя:</td>
																				<td><input name="FirstName" required="required" type="text" /></td>
																				<td colspan="1" rowspan="3">Аватар:<br />
																								Красивый виджет загрузки аватара</td>
																</tr>
																<tr>
																				<td>Фамилия:</td>
																				<td><input name="LastName" required="required" type="text" /></td>
																</tr>
																<tr>
																				<td>Город:</td>
																				<td><input name="City" type="text" /></td>
																</tr>
																<tr>
																				<td>Телефон:</td>
																				<td><input name="phone" type="tel" /></td>
																				<td>О себе:</td>
																</tr>
																<tr>
																				<td>E-mail:</td>
																				<td><input name="mail" type="email" /></td>
																				<td colspan="1" rowspan="3"><textarea name="resum"></textarea></td>
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
																				<td>Год рождения:</td>
																				<td><div id="birth"></div></td>
																</tr>
												</tbody>
								</table>
				</div>
				<div id="regTab2">
								<p>Логин пароль</p>
				</div>
</div>
</form>

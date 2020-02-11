

$(document).ready(function(){
    $.fn.datepicker.languages['ru-RU'] = {
	format: 'dd.mm.yyyy',
	days: ['Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресение'],
	daysShort: ["Пн", "Вт", "Ср", "Чт", "Пт", "Сб", "Вс"],
	daysMin: ["Пн", "Вт", "Ср", "Чт", "Пт", "Сб", "Вс"],
	months: ["Январь","Февраль","Март","Апрель","Май","Июнь","Июль","Август", "Сентябрь","Октябрь","Ноябрь","Декабрь"],
	monthsShort: ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'],
	weekStart: 1,
	startView: 0,
	yearFirst: true,
	yearSuffix: 'г.'
    };
    $(register).find("[name='Birth']").datepicker({language: 'ru-RU' });
});
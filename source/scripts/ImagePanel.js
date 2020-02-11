var changed = null;
$(document).ready(function () {

    $("#tabs").tabs({
	load: function (event, ui) {
	    var text = "";
	    var images = $("#images div ");
	    images.find("img").each(function(){
		var t =this;
		$(this).ready(function(){
		    if($(t).height()>$(t).width()){
			$(t).height(150);
		    }else $(t).width(150);
		});
	    });
	    images.find(".bar").append("<input type='button' value='X'/>");
	    images.find(".bar input[type='button']").click(del);
	},
	befireLoad: function(event, ui){
	    ui.jqXHR.fail(function () {
		ui.panel.html(
			"Couldn't load this tab. We'll try to fix this as soon as possible. " +
			"If this wouldn't be a demo.");
	    });
	}
    });
});
function del(){
    var obj = $(this).parent().parent();
    var img = obj.find("img").attr("src");
    var conf = confirm("Вы действительно хотите удалить изображение?");
    if(conf){
	$.ajax({
	    method: "POST",
	    url: "/profile/data/images",
	    data: { "file": img, "conf":"delete"}
	}).done(function(msg) {
	    if(msg!=0) alert("При удалении файла произошла ошибка!");
	    else obj.detach();
	});
    }
}
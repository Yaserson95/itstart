$("#search_part").ready(function(){
    $(this).find("[name=parts]").selectmenu();
    $(this).submit(function(event){
	var text = $(event.target).find("[name=text]").val();
	var part = $(event.target).find("[name=parts]").val();
	if(typeof part==='undefined'||text===""||typeof text==='undefined'){
	    event.preventDefault();
	}
	//alert("http://www.itstart.su/Search/"+part+"/"+text);
	event.target.action = "/Search/"+part+"/"+text;
    });
});
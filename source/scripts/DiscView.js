$(document).ready(function(){
    var DiscId = $("#DiscId").val();
    if(typeof DiscId==='undefined'){
	alert("Нет DiscId!");
	return 1;
    }
    $("#DiscId").remove();
    var logCimments = {
	Type:"Discussion",
	ObjId:DiscId,
	Textarea:null,
	Parent:"comments"
    };
    var logMarks = {
	Type:"Discussion",
	ObjId:DiscId,
	Parent:"toolbar"
    };
    
    $("#Editing").each(function(){
	var buttons = {
	    "edit":{
		Type:"a",
		Href:"/Discussions/Discussion_"+DiscId+"/Edit",
		Text:"Редактировать",
		Icon:"ui-icon-pencil",
		Label:false
	    },
	    "delete":{
		Type:"button",
		Click:function(){
		    var conf = confirm("Вы действительно хотите удалить обсуждение?");
		    if(conf){
			window.location.href="/Discussions/Discussion_"+DiscId+"/Delete";
		    }
		},
		Text:"Удалить",
		Icon:"ui-icon-trash",
		Label:false
	    }
	};
	addButtons(this,buttons);
    });
    
    var marks = new marksToolbar(logMarks);
    var comment = new Comments(logCimments);
});
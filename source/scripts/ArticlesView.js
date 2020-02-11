function InitEditBar(id,elem){
    var buttons = {
	Edit:{
	    Type:"a",
	    Href:"/Articles/Edit/Article_"+id,
	    Text:"Редактировать",
	    Icon:"ui-icon-pencil",
	    Label:false
	},
	Delete:{
	    Type:"button",
	    Click:function(){
		var conf = confirm("Вы действительно хотите удалить выбранную статью?");
		if(conf){
		    window.location.href="/Articles/Delete/Article_"+id;
		}
	    },
	    Text:"Удалить",
	    Icon:"ui-icon-trash",
	    Label:false
	}
    };
    addButtons(elem,buttons);
}

$(document).ready(function(){
    var artId = $("#ArtId").val();
    var logCimments = {
	Type:"Article",
	ObjId:artId,
	Textarea:null,
	Parent:"comments"
    };
    var comment = new Comments(logCimments);
    var toolbar = document.getElementById('toolbar');
	if(typeof toolbar!=="undefined"){
	    var marksSpan = document.createElement("span");
	    var editSpan = document.createElement("span");
	    $(toolbar).append(marksSpan);
	    $(toolbar).append(editSpan);
	    var logMarks = {
		Type:"Article",
		ObjId:artId,
		Parent:marksSpan
	    };
	    var marks = new marksToolbar(logMarks);
	    var editing = document.getElementById("editing");
	    if(typeof editing!=='undefined'){
		var val = editing.value;
		$(editing).remove();
		if(val){
		    InitEditBar(artId,editSpan);
		}
	    }
	}
    
    
    
    
});

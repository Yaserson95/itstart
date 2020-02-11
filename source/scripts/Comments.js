var ckeditorComment = {
    toolbarGroups : [
	{ name: 'basicstyles', groups: [ 'basicstyles'] },
	{ name: 'colors', groups: [ 'colors' ] },
	{ name: 'paragraph', groups: [ 'list'] },
	{ name: 'links', groups: [ 'links' ] },
	{ name: 'insert', groups: [ 'insert' ] }
    ],
    removeButtons:'Templates,Flash,Table,HorizontalRule,PageBreak,Iframe,Anchor,Unlink',
    uiColor: '#ccffcc',
    removePlugins : 'resize'
};
function marksToolbar(config){
    var marks_toolbar={
	buttonsConf:{
	    Like:{
		type:"button",
		text:"Понравилось",
		icon: "ui-icon-plusthick",
		mark:0,
		click:"clickMark"
	    },
	    Dislike:{
		type:"button",
		text:"Не понравилось",
		icon: "ui-icon-minusthick",
		mark:1,
		click:"clickMark"
	    },
	    Comments:{
		type:"a",
		text:"Комментарии",
		icon: "ui-icon-comment",
		href:"#comments"
	    },
	    Hidden:{
		type:"button",
		text:"Скрыть",
		icon: "ui-icon-cancel",
		mark:2,
		click:"clickMark"
	    }
	},
	buttons:{},
	info:null,
	userMarks:null,
	ratingLabel:null,
	autorize:false,
	init: function(){
	    switch(typeof config.Parent){
		case "string":
		    this.Parent = document.getElementById(config.Parent);
		    break;
		case "object": 
		    this.Parent = config.Parent;
		    break;
		default:
		    return 1;
	    }
	    if(this.autorize){
		this.autorizeInit();
		this.userMarksRenew();
	    }
	    else{
		this.noAutorizeInit();
	    }
	},
	addbutton:function(elem,btn){
	    var button = document.createElement(btn.type);
		button.innerHTML = btn.text;
		switch (btn.type){
		    case "button":{
			button.command = btn.id;
			if(typeof (btn.mark)!=="undefined"){
			    button.mark = btn.mark;
			}
			if(typeof btn.click!=="undefined"){
			    button.onclick = marks_toolbar[btn.click];
			}
			break;
		    }
		    case "a":{
			if(typeof btn.href!=="undefined"){
			    button.href = btn.href;
			}
		    }
		}
		
		$(elem).append(button);
		$(button).button({
		    icon: btn.icon,
		    showLabel: false
		});
		return button;
	},
	addButtons:function(elem){
	    if(typeof config.Buttons==="object"){
		for(var btn in config.Buttons){
		    var name = config.Buttons[btn];
		    marks_toolbar.buttons[name] = marks_toolbar.addbutton(elem,this.buttonsConf[name]);
		    //alert(name);
		}
		
	    }
	    else{
		for(var btn in this.buttonsConf){
		    
		    this.buttons[btn] = this.addbutton(elem,this.buttonsConf[btn]);
		}
	    }
	},
	getInfo: function(){
	    $.ajax({
		method: "POST",
		url: "/Marks/Info",
		data: {Type:config.Type,Parent:config.ObjId}
	    }).done(function(msg){
		var Data = JSON.parse(msg);
		if(Data.Request==="info"){
		    marks_toolbar.info = Data;
		    marks_toolbar.getUserMarks();
		}
		else alert(Data.Text);
	    });
	},
	getUserMarks:function(){
	    $.ajax({
		method: "POST",
		url: "/Marks/Usermarks",
		data: {Type:config.Type,Parent:config.ObjId}
	    }).done(function(msg) {
		var Data = JSON.parse(msg);
		marks_toolbar.autorize = Data.Autorize;
		if(Data.Autorize){
		    marks_toolbar.userMarks = Data.Marks;
		}
		marks_toolbar.init();
	    });
	},
	getMark:function(mark){
	    $.ajax({
		method: "POST",
		url: "/Marks/Mark",
		data:mark
	    }).done(function(msg){
		var data = JSON.parse(msg);
		if(data.Request==="error"){
		    alert(data.Text);
		}
		else{
		    marks_toolbar.renewInfo(data);
		}
	    });
	},
	noAutorizeInit:function(){
	    var lblComments = document.createElement("h2");
	    lblComments.innerHTML = "<a href='#comments'>Комментариев: "+this.info.Comments+"</a>";
	    var lblRating = document.createElement("h2");
	    lblRating.innerHTML = "Рейтинг: "+this.info.Rating;
	    $(this.Parent).append(lblRating);
	    $(this.Parent).append(lblComments);
	},
	autorizeInit:function(){
	    this.addButtons(this.Parent);
	    var lblrating = document.createElement("label");
	    lblrating.className = "rating";
	    $(this.buttons.Dislike).css({"margin-right":"22px"});
	    $(this.buttons.Like).after(lblrating);
	    this.ratingLabel = lblrating;
	    this.renewInfo(this.info);
	},
	userMarksRenew:function(){
	    var changemark = this.changeMark;
	    var buttons = this.buttons;
	    $(this.userMarks).each(function(){
		switch(parseInt(this.Mark)){
		    case 0:
			changemark(buttons.Like);
			$(marks_toolbar.ratingLabel).css({"color":"red"});
			break;
		    case 1:
			changemark(buttons.Dislike);
			$(marks_toolbar.ratingLabel).css({"color":"blue"});
			break;
		    case 2:
			changemark(buttons.Hidden);
			break;
		}
	    });
	},
	changeMark:function(elem){
	    if(typeof elem.changed==="undefined"){
		elem.changed = false;
	    }
	    if(elem.changed){
		elem.changed=false;
		elem.className = elem.TempClass;
		//marks_toolbar.ratingLabel.style="";
	    }
	    else{
		elem.TempClass = elem.className;
		elem.changed=true;
		elem.className = elem.className+" chbtn";
		
	    }
	},
	clickMark:function(){
	    //alert(this.mark);
	    var changeMark = marks_toolbar.changeMark;
	    var buttons = marks_toolbar.buttons;
	    var ratingLabel = marks_toolbar.ratingLabel;
	    var mark = {
		Type:config.Type,
		Parent:config.ObjId,
		Mark:this.mark
	    };
	    changeMark(this);
	    marks_toolbar.getMark(mark);
	    switch (this.mark){
		case 0:{
		    if(buttons.Dislike.changed){
			changeMark(buttons.Dislike);
		    }
		    if(this.changed){
			$(ratingLabel).css({"color":"red"});
		    }
		    else{
			ratingLabel.style="";
		    }
		    break;
		}
		case 1:{
		    if(buttons.Like.changed){
			changeMark(buttons.Like);
		    }
		    if(this.changed){
			$(ratingLabel).css({"color":"blue"});
		    }
		    else{
			ratingLabel.style="";
		    }
		    break;
		}
		default:break;
	    }
	},
	
	renewInfo:function(data){
	    var buttons =  this.buttons;
	    for(var item in data){
		if(typeof buttons[item]!=="undefined"){
		    buttons[item].title = marks_toolbar.buttonsConf[item].text+" ("+data[item]+")";
		}
	    }
	    this.ratingLabel.innerHTML = data.Rating;
	}
    };
    marks_toolbar.getInfo();
    return marks_toolbar;
}
function Comments(config){
var comments = {
    parentElement:null,
    currentDialog:null,
    CommentForm:null,
    CommentPannel:null,
    Autorize:false,
    CurrentId:null,
    Order:{
	column:null,
	by:null
    },
    TextDialog:function(id,title){
	var dialDiv = document.createElement("div");
	dialDiv.id = id;
	dialDiv.title = title;
	this.CommentForm = dialDiv;
	var txtComment = document.createElement("textarea");
	txtComment.id = "txtComment";
	var txt = CKEDITOR.replace(txtComment,ckeditorComment);
	dialDiv.getText = function(){
	    return txt.getData();
	};
	dialDiv.setText = function(text){
	    txt.setData(text);
	},
	$(dialDiv).append(txtComment);
	return dialDiv;
    },
    dialogConfig:function(){
	return {
	    modal:true,
	    width:600,
	    resizable:false
	    //autoOpen: false,
	};
    },
    openAddComment:function(query){
	var addForm = comments.TextDialog("addComment","Добавить комментарий");
	var dial = comments.dialogConfig();
	dial.buttons={
	    "Добавить":function(){
		if(addForm.getText()!==""){
		    query.Textcom = addForm.getText();
		    comments.CreateComment(query);
		    $(this).remove();
		}
		else{
		    alert("Введите текст комментария!");
		}
	    },
	    "Отмена":comments.dialogCancel
	};
	comments.currentDialog = $(addForm).dialog(dial);
    },
    dialogCancel:function(){
	var c=confirm("Вы действительно хотите отменить действие?");
	if(c){
	    $(this).remove();
	}
    },
    openEditComment:function(query){
	var editForm = comments.TextDialog("addComment","Изменить комментарий");
	var dial = comments.dialogConfig();
	editForm.setText(query.TextCom);
	dial.buttons={
	    "Сохранить":function(){
		if(editForm.getText()!==""){
		    query.TextCom = editForm.getText();
		    comments.EditComment(query);
		    $(this).remove();
		}
		else{
		    alert("Введите текст комментария!");
		}
	    },
	    "Отмена":comments.dialogCancel
	};
	comments.currentDialog = $(editForm).dialog(dial);
    },
    deleteDialog:function(){
	this.CommentDialog.remove();
    },
    openDialogAdd:function(){
	if(comments.Autorize){
	   comments.openAddComment({Part:config.Type,Parent:config.ObjId});
	}
	else{
	    document.location.href="/Account/Index";
	}
    },
    createPannel:function(){
	this.parentElement = document.getElementById(config.Parent);
	var panel = document.createElement("p");
	panel.target = "comments";
	var btnNew = document.createElement("input");
	btnNew.id = "newComment";
	btnNew.value = "Комментировать";
	btnNew.type="button";
	//$(btnNew).button();
	btnNew.dial = this.CommentDialog;
	btnNew.dform = this.CommentForm;
	btnNew.onclick = this.openDialogAdd;
	$(panel).append(btnNew);
	$(this.parentElement).append(panel);
    },
    addCommentClick:function(){
	var comment = {};
	comment.Type = this.parentType;
	comment.Parent = this.parentId;
	comment.Textcom = CKEDITOR.instances.txtComment.getData();
	CKEDITOR.instances.txtComment.setData("");
	comments.sendComment(comment);
	$(this).dialog( "close" );
    },    
    CreateComment:function(comment){
	$.ajax({
	    method: "POST",
	    url: "/Comments/Create",
	    data: comment
	}).done(function(msg) {
	    if(msg!=="0"){
		alert("При добавлении комментария произошла ошибка!");
	    }
	    else comments.renewCommments();
	});
    },
    EditComment:function(comment){
	$.ajax({
	    method: "POST",
	    url: "/Comments/Edit",
	    data: comment
	}).done(function(msg) {
	    if(msg!=="0"){
		alert("При изменении комментария произошла ошибка!");
	    }
	    else comments.renewCommments();
	});
    },
    getComments:function(query,elem){
	$.ajax({
	    method: "POST",
	    url: "/Comments/getAll",
	    data: query
	}).done(function(msg) {
	    var cmData = JSON.parse(msg);
	    if(cmData.Type==='comments'){
		comments.Autorize = cmData.Autorize;
		$(cmData.Data).each(function(){
		    comments.printComment(elem,this);
		});
	    }
	    else alert(cmData.Text);
	});
    },
    printComment:function(elem,comment){
	var divComm = document.createElement("div");
	divComm.className="comment";
	var header = document.createElement("div");
	header.className="header";
	//header
	header.innerHTML="<span>"+comment.Nickname+" - "+comment.Time+"назад</span>";
	$(divComm).append(header);
	var body = document.createElement("div");
	body.className="body";
	body.innerHTML = comment.Text;
	//body
	$(divComm).append(body);
	var footer = document.createElement("div");
	footer.className="footer";
	//footer
	if(comments.Autorize){
	    var marksSpan = document.createElement("span");
	    $(footer).prepend(marksSpan);
	    var logMarks = {
		Type:"Comment",
		ObjId:comment.CommentId,
		Parent:marksSpan,
		Buttons:['Like','Dislike']
	    };
	    var marks = marksToolbar(logMarks);
	    //alert(typeof logMarks.Parent);
	    //Если пользователь авторизирован - он сможет ответить на комментарии
	    var btnReply = document.createElement("input");
	    btnReply.type="button";
	    btnReply.name = "Reply";
	    btnReply.value="Ответить";
	    btnReply.CommentId = comment.CommentId;
	    btnReply.dial = comments.CommentDialog;
	    btnReply.parentId = comment.CommentId;
	    btnReply.parentType = comment.TypePar;
	    btnReply.onclick = function(){
		comments.openAddComment({Part:"Comment",Parent:comment.CommentId});
	    };
	    $(footer).append(btnReply);
	}
	if(comment.Editing){
	    var btnEdit = document.createElement("input");
	    btnEdit.type="button";
	    btnEdit.value="Редактировать";
	    btnEdit.onclick = function(){
		comments.openEditComment({
		    ComId: comment.CommentId,
		    TextCom: comment.Text
		});
	    };
	    $(footer).append(btnEdit);
	    var btnDelete = document.createElement("input");
	    btnDelete.type="button";
	    btnDelete.onclick = function(){
		var c = confirm("Вы действительно хотите удалить комментарий?");
		if(c){
		    comments.removeComment(comment.CommentId);
		    comments.renewCommments();
		}
	    };
	    btnDelete.value="Удалить";
	    $(footer).append(btnDelete);
	}
	if(comment.Nchild>0){
	    var btnCld = document.createElement("input");
	    btnCld.type="button";
	    btnCld.name = "Showchild";
	    btnCld.value="Развернуть ("+comment.Nchild+")";
	    btnCld.elem = divComm;
	    btnCld.parentId = comment.CommentId;
	    btnCld.Nchild = comment.Nchild;
	    btnCld.exp = false;
	    btnCld.onclick = function(){
		if(!this.exp){
		    var query={
			Part:"Comment",
			ObjectId:comment.CommentId
		    };
		    var divChild = document.createElement("div");
		    divChild.className = "child";
		    comments.getComments(query,divChild);
		    this.child = divChild;
		    this.value="Свернуть";
		    $(this.elem).append(divChild);
		}
		else{
		    $(this.child).remove();
		    this.value = "Развернуть ("+this.Nchild+")";
		}
		this.exp = !this.exp;
	    };
	    $(footer).append(btnCld);
	}
	$(divComm).append(footer);
	$(elem).append(divComm);
	return divComm;
    },
    renewCommments:function(){
	if(this.CommentPannel!==null){
	    $(this.CommentPannel).remove();
	}
	var compan = document.createElement("div");
	this.CommentPannel = compan;
	$(this.parentElement).append(compan);
	var query = {
	    ObjectId:config.ObjId,
	    Part:config.Type
	};
	this.getComments(query,compan);
    },
    removeComment:function(ID){
	$.ajax({
	    method: "POST",
	    url: "/Comments/Remove",
	    data: {CommentId:ID}
	}).done(function(msg) {
	    if(msg!=="0"){
		alert("При удалении комментария произошла ошибка: "+msg);
	    }
	});
    }
};
    //comments.createDialog();
    comments.createPannel();
    comments.renewCommments();
    return comments;
}



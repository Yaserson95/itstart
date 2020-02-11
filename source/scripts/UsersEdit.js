function UserEdit(config){
    var user_edit = {
	Parent:null,
	Tabs:{},
	Current:null,
	Init:function(){
	    switch(typeof config.Parent){
		case "string":
		    this.Parent = document.getElementById(config.Parent);
		    break;
		case "object":
		    this.Parent = config.Parent;
		    break
		default:
		    return 1;
	    }
	    this.InitTabs();
	},
	InitTabs:function(){
	    var ul = document.createElement("ul");
	    $(this.Parent).append(ul);
	    for(var i in config.Tabs){
		var item = config.Tabs[i];
		$(ul).append("<li><a href='#"+i+"'>"+item.title+"</a></li>");
		var tab = document.createElement("div");
		tab.id = i;
		if(typeof item.init==="function"){
		    tab.init = item.init;
		}
		this.Tabs[i] = tab;
		$(this.Parent).append(tab);
	    }
	    $(this.Parent).tabs({
		activate:function(event, ui){
		    user_edit.SetTab($(ui.newPanel).attr("id"));
		},
		create:function(event, ui){
		    user_edit.SetTab($(ui.panel).attr("id"));
		}
	    });
	},
	SetTab:function(tab){
	    if(this.Current!==null){
		this.Current.innerHTML="";
	    }
	    this.Current = this.Tabs[tab];
	    var item = config.Tabs[tab];
	    if(typeof item.html!=="undefined"){
		this.Current.innerHTML = item.html;
	    }
	    if(typeof this.Current.init==="function"){
		this.Current.init();
	    }
	}
    };
    user_edit.Init();
    return user_edit;
}
function updateTable(user,Data){
    var table = getTable({
	UserId:"№",
	Nickname:"Псевдоним",
	Firstname:"Имя",
	Surname:"Фамилия",
	Post:"Должность"
    },Data.Items,{
	UserId:{"width":"40px","text-align":"center"},
	Post:{"width":"250px"}
    });
    table.className = "elemList";
    $(table).find("tbody tr").each(function(){
	var id = this.firstChild.innerHTML;
	var postTd = this.lastChild;
	var post = postTd.innerHTML;
	postTd.innerHTML = "";
	var select = new getSelect({
	    "0":"Пользователь",
	    "1":"Модератор",
	    "2":"Администратор",
	    "4":"Заблокированный"
	},post);
	$(postTd).append(select);
	var btnDel = document.createElement("button");
	btnDel.innerHTML="Удалить";
	btnDel.tr = this;
	btnDel.onclick = function(){
	    var c = confirm("Вы действительно хотите убрать пользователя?");
	    var tr = this.tr;
	    if(c){
		$.ajax({
		    method: "POST",
		    url: user.Patch+"/Deluser",
		    data: {UserId:id}
		}).done(function(msg){
		    if(msg!=="0"){
			alert("Ошибка №"+msg);
		    }
		    else{
			$(tr).remove();
		    }
		});
	    }
	};
	$(btnDel).button();
	$(postTd).append(btnDel);
	select.onchange = function(){
	    var query = {
		UserId:id,
		Post:this.value
	    };
	    $.ajax({
		method: "POST",
		url: user.Patch+"/Editpart",
		data: query
	    }).done(function(msg){
		if(msg!=="0"){
		    alert("Ошибка №"+msg);
		}
	    });
	};
    });
    return table;
}
function createTable(user,Data){
    var frame = document.createElement("div");
    var table = updateTable(user,Data);
    frame.className="frame";
    //Переключение страниц
    if(Data.Pages>1){
	var label = document.createElement("label");
	label.innerHTML="Страница ";
	var spinner = document.createElement("input");
	spinner.type = "text";
	spinner.value="1";
	$(label).append(spinner);
	$(label).append(" из "+Data.Pages);
	$(frame).append(label);
	$(spinner).spinner({
	    min:1,
	    max:Data.Pages,
	    stop:function(){
		user.Page = this.value;
		$(table).remove();
		$.ajax({
		method: "POST",
		url: user.Patch+"/Info",
		data: user
	    }).done(function(msg){
		//alert(msg);
		var Data = JSON.parse(msg);
		table = updateTable(user,Data);
		$(frame).append(table);
	    });
	    }
	});
    }
    $(frame).append(table);
    return frame;
}
function GroupsUsers(user,elem){
    if(typeof user.Patch==='undefined'){
	return 1;
    }
    var patch = user.Patch;
    //Панель с поиском:
    var tableframe = null;
    var tools = document.createElement("div");
    var search = document.createElement("input");
    search.type="text";
    var searchbar = document.createElement("label");
    searchbar.innerHTML = "Найти: ";
    $(searchbar).append(search);
    $(tools).append(searchbar);
    search.timeout=null;
    search.oninput = function(){
	var text = this.value;
	if(this.timeout!==null){
	    clearTimeout(this.timeout);
	}
	this.timeout = setTimeout(function(){
	    if(text===""&&(typeof user.Search ==="undefined")){
		delete(user.Search);
	    }
	    else{
		user.Search=text;
	    }
	    $(tableframe).remove();
	    $.ajax({
		method: "POST",
		url: patch+"/Info",
		data: user
	    }).done(function(msg){
		var Data = JSON.parse(msg);
		tableframe = createTable(user,Data);
		$(elem).append(tableframe);
	    });
	    
	}, 500);
    };
    $(elem).append(tools);
    //тбл
    
    $.ajax({
	method: "POST",
	url: patch+"/Info",
	data: user
    }).done(function(msg){
	//alert(msg);
	var Data = JSON.parse(msg);
	tableframe = createTable(user,Data);
	$(elem).append(tableframe);
    });
}
function Query(GroupId,post){
    var USERS = {
	Limit:100,
	Page:1,
	Patch:"/Groups/Group_"+GroupId,
	Post:post
    };
    return USERS;
}
$(document).ready(function(){
    var group = document.getElementById("GroupId");
    if(typeof group==='undefined'){
	return 0;
    }
    var GroupId = group.value;
    
    new UserEdit({
	Parent:"tabs",
	Tabs:{
	    users:{
		title:"Все пользователи",
		init:function(){
		    var query = new Query(GroupId,-1);
		    GroupsUsers(query,this);
		}
	    },
	    moderators:{
		title:"Модераторы",
		init:function(){
		    var query = new Query(GroupId,1);
		    GroupsUsers(query,this);
		}
	    },
	    administrators:{
		title:"Администраторы",
		init:function(){
		    var query = new Query(GroupId,2);
		    GroupsUsers(query,this);
		}
	    },
	    blocked:{
		title:"Заблокированные",
		init:function(){
		    var query = new Query(GroupId,4);
		    GroupsUsers(query,this);
		}
	    }
	}
    });
});
function createTools(elemId){
    var tools = document.getElementById(elemId);
    if(typeof tools === "undefined"){
	return 1;
    }
}
function addBtn(btn){
    var button = document.createElement(btn.type);
    button.innerHTML = btn.text;
    if(typeof btn.click!=="undefined"){
	button.onclick = btn.click;
    }
    if(btn.type.toLowerCase()==="a"){
	if(typeof btn.href!=="undefined"){
	    button.href = btn.href;
	}
    }
    return button;
}
function initGr(info){
    if(typeof info.Noautorize === "undefined"){
	if(typeof info.Post === "undefined"){
	    
	}
    }
}
function Group(config){
    var gr = {
	Autorize:false,
	Useredit:false,
	Groupedit:false,
	Owner:false,
	Post:-1,
	Toolbar:null,
	Buttons:{},
	ConfBtns:{
	    Enter:{
		type:"a",
		text:"Вступить",
		href:"/Groups/Group_"+config.GroupId+"/Enter"
	    },
	    Exit:{
		type:"span",
		text:"Выйти из группы",
		click:function(){
		    var c = confirm("Вы действительно хотите выйти из группы?");
		    if(c){
			document.location.href="/Groups/Group_"+config.GroupId+"/Enter";
		    }
		}
	    },
	    Useredit:{
		type:"a",
		text:"Управление пользователями",
		href:"/Groups/Group_"+config.GroupId+"/Users"
	    },
	    Groupedit:{
		type:"a",
		text:"Редактировать группу",
		href:"/Groups/Group_"+config.GroupId+"/Edit"
	    }
	},
	Set:function(Data){
	    var items=["Autorize","Useredit","Groupedit","Post","Owner"];
	    for(var i in items){
		var item = items[i];
		if(typeof Data[item]!=="undefined"){
		    this[item] = Data[item];
		}
	    }
	},
	InitButtons:function(){
	    if(!this.Autorize||this.Post===4){
		return 1;
	    }
	    if(!this.Owner){
		if(this.Post===-1){
		    this.Buttons.Enter = addBtn(this.ConfBtns.Enter);
		}
		else{
		    this.Buttons.Exit = addBtn(this.ConfBtns.Exit);
		}
	    }
	    var items = ["Useredit","Groupedit"];
	    for(var i in items){
		var item = items[i];
		if(this[item]){
		    this.Buttons[item] = addBtn(this.ConfBtns[item]);
		}
	    }
	    for(var i in this.Buttons){
		$(this.Toolbar).append(this.Buttons[i]);
		//$(this.Toolbar).append("<br/>");
		//$(this.Buttons[i]).button();
		
	    }
	},
	Init:function(){
	    switch(typeof config.Toolbar){
		case "string":
		    this.Toolbar = document.getElementById(config.Toolbar);
		    break;
		case "object":
		    this.Toolbar = config.Toolbar;
		    break
		default:
		    return 1;
	    }
	    if(typeof this.Toolbar==="undefined"){
		return 2;
	    }
	    this.Toolbar.className='tools group';
	    this.InitButtons();
	}
    };
    gr.Set(config);
    gr.Init();
    return gr;
}
function Discussuons(config){
    
}

$(document).ready(function(){
    var UserInfo = document.getElementById("us_info");
    if(typeof UserInfo !== "undefined"){
	var info = JSON.parse(UserInfo.value);
	info.Toolbar='tools';
	var group = new Group(info);
    }
    new Discussions({
	Parent:"discussions"
    });
});
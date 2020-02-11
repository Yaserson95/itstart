var Parts = [
	{
	    title:"Articles",
	    columns:{
		"ArtId":"№",
		"Name":"Название",
		"Description":"Описание",
		"ArtType":"Тип статьи",
		"Nickname":"Автор",
		"DatePubl":"Опубликовано",
		"Rating":"Рейтинг"
	    }
	},
	{
	    title:"Discussions",
	    columns:{
		"DiscId":"№",
		"Title":"Название",
		"DatePubl":"Опубликовано",
		"Group":"Группа",
		"Nickname":"Автор",
		"Rating":"Рейтинг"
	    }
	},
	{
	    title:"Groups",
	    columns:{
		"GroupId":"№",
		"Theme":"Тема",
		"Title":"Название",
		"Description":"Описание",
		"Owner":"Создатель",
		"Users":"Пользователей",
		"Discussions":"Обсуждений"
	    }
	    
	}
    ];
function getInfo(config,part){
    var configs = {
	Articles:["UserId","Mark","Search","Page"],
	Groups:["UserId","Search","Page"],
	Discussions:["UserId","Mark","Search","Page"]
    };
    var info = {
	Query:{},
	Info:function(Data){
	    
	},
	Set:function(conf = {}){
	    if(typeof conf.Load ==="function"){
		this.Info = conf.Load;
	    }
	    if(typeof configs[part]==="undefined") return 1;
	    for(var i in configs[part]){
		var item = configs[part][i];
		if(typeof conf[item]!=="undefined"){
		    this.Query[item] = conf[item];
		}
	    }
	    $.ajax({
		method: "POST",
		url: "/"+part+"/Info",
		data: this.Query
	    }).done(function(msg) {
		var json = JSON.parse(msg);
		info.Info(json);
	    });
	},
	ClearQuery:function(){
	    this.Query = {};
	},
	SetPart:function(p){
	    part = p;
	}
    };
    info.Set(config);
    return info;
}

function TabInit(elem,Config={}){
    var tabmenu = $(elem).find("#tabmenu");
    var tabcont = $(elem).find("#tabcont");
    var frame = document.createElement("div");
    frame.style="width:100%";
    frame.className = "frame";
    var table = {};
    var info = new getInfo({
	Load:function(Data){
	    frame.innerHTML="";
	    if(parseInt(Data.Count)===0){
		$(frame).append("<div class='info'>Нет элементов</div>");
		return 0;
	    }
	    var table = getTable(Parts[info.curent].columns,Data.Items);
	    table.className="elemList";
	    $(frame).append(table);
	},
	UserId:-1
    });
    var search = CreateSearch({
	Search:function(Text){
	    info.Set({Search:Text});
	}
    });
    
    var menu = CreateLIstView({
	List:Config.List,
	Selected:0,
	Select:function(elem,i){
	    search.Clear();
	    info.SetPart(Parts[i].title);
	    info.curent = i;
	    info.Set(Config);
	}
    });
    $(tabmenu).append(menu);
    var contentDiv = document.createElement("div");
    contentDiv.innerHTML = "Найти: ";
    $(contentDiv).append(search.Field);
    $(tabcont).append(contentDiv);
    $(tabcont).append(frame);
    
}
function Tbl(Data){
    
}
$(document).ready(function(){
    var HTML = "<table class='tabtbl'><tr><td id = 'tabmenu'></td><td id = 'tabcont'></td></tr></table>";
    var tabs = CreateTabs({
	Parent:"tabs",
	Tabs:{
	    My:{
		title:"Моё",
		init:function(){
		    TabInit(this,{
			UserId:-1,
			List:["Статьи","Обсуждения","Группы"]
		    });
		},
		html:HTML
	    },
	    Like:{
		title:"Понравилось",
		init:function(){
		    TabInit(this,{
			Mark:0,
			List:["Статьи","Обсуждения"]
		    });
		},
		html:HTML
	    },
	    Dislike:{
		title:"Не понравилось",
		init:function(){
		    TabInit(this,{
			Mark:1,
			List:["Статьи","Обсуждения"]
		    });
		},
		html:HTML
	    },
	    Hidden:{
		title:"Скрыто",
		init:function(){
		    TabInit(this,{
			Mark:2,
			List:["Статьи","Обсуждения"]
		    });
		},
		html:HTML
	    },
	    Images:{
		title:"Галлерея",
		init:function(){
		    CKFinder.widget("Images", {
			width: '100%',
			height: '500px'
		    } );
		}
	    }
	}
    });
});
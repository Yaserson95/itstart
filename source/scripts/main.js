//Удалить:
var testUsers = [
    {
	nick:"Yaser",
	pass:"Qwerty1"
    },
    {
	nick:"Alex",
	pass:"Asdfgh2"
    },
    {
	nick:"Victor",
	pass:"Ytrewq1"
    },
    {
	nick:"Igor",
	pass:"Zxcvbnm64"
    }
];


var dialogs = {
    loginform:{
	command:"login",
	dialog:{
	    modal:true,
	    width:"350px",
	    resizable:false,
	    autoOpen: false,
	    buttons:{
		"Войти":function(){
		    Logining(this);
		},
		"Отмена":function(){
		    $(this).dialog( "close" );
		}
	    }
	}
    },
    test:{
	command:"testbtn",
	dialog:{
	    modal:true,
	    width:"350px",
	    resizable:false,
	    autoOpen: false,
	    buttons:{
		"Войти":function(){
		    var UserId = $(this).find("input[name = 'UserId']:checked").val();
		    login(testUsers[UserId].nick,testUsers[UserId].pass);
		},
		"Отмена":function(){
		    $(this).dialog( "close" );
		}
	    }
	}
    }
};

//Подгонка размеров страницы
function setSize() {
    var contHeight = $(window).height() - ($("#header").height() + $("#footer").height() + 30);
    $("#content").css({"min-height": contHeight + "px"});
}


var DialogLogin ={};
var changeUserPhoto={};
var DialogReg = {};
var regFormTabs = {};

var chImage = null;

$(window).resize(function () {
    setSize();
});

function login(nickname,password){
    $.ajax({
	method: "POST",
	url: "/Account/Login",
	data: { Nickname: nickname, UserPswrd: password}
    }).done(function(msg) {
	if(msg=="1") alert("Неправильное имя пользователя, или пароль!");
	else location.reload();
    });
}

function Logining(elem){
    var Nick = $(elem).find("[name = 'Nickname']");
    var NickPreg = /^([A-z0-9_.]*)$/i;
    var Pass = $(elem).find("[name = 'UserPswrd']");
    var PassPreg=/(?=[-_a-zA-Z0-9]*?[A-Z])(?=[-_a-zA-Z0-9]*?[a-z])(?=[-_a-zA-Z0-9]*?[0-9])[-_a-zA-Z0-9]{6,}/;
    var NickValid = checkRegexp( Nick, NickPreg, "Не верно введены данные!" );
    var Passvalid = checkRegexp( Pass, PassPreg, "Не верно введены данные!" );
    if(NickValid&&Passvalid){
	login(Nick.val(),Pass.val());
    }
}
function checkRegexp( o, regexp, n ) {
    var err = o.attr("name")+"Error";
    if (!( regexp.test(o.val() ))){
	    $("#loginform").find("#"+err).html(n);
	return false;
    } else {
	$("#loginform").find("#"+err).html("");
	return true;
    }
}

function initDialogs(dialogs){
    for(var key in dialogs){
	var item = dialogs[key];
	var elem = document.getElementById(key);
	var cmd = document.getElementById(item.command);
	if(cmd===null) continue;
	cmd.dialog = $(elem).dialog(item.dialog);
	$(cmd).click(function(){
	    this.dialog.dialog( "open" );
	});
    }
} 

$(document).ready(function () {
    setSize();
    initDialogs(dialogs);
    $("#search").each(function(){
    $(this).submit(function(event){
	var text = $(event.target).find("[name=Query]").val();
	if(text===""||typeof text==='undefined'){
	    event.preventDefault();
	}
	event.target.action = "/Search/All/"+text;
    });
});
    
});


//Вспомогательный функционал
function ImageLoader(config){
    var image_loader = {
	ImageField:null,
	ImagePrev:null,
	FileName:null,
	viewpanel:null,
	Init:function(){
	    switch (typeof config.Replace){
		case "object":
		    this.ImageField = config.Replace;
		    break;
		case "string":
		    this.ImageField = document.getElementById(config.Replace);
		    break;
		default: return 1;
	    }
	    if(typeof this.ImageField==="undefined") return 0;
	    $(this.ImageField).attr('type',"hidden");
	    var pannel = document.createElement("div");
	    pannel.className = "imageloader";
	    $(pannel).insertAfter(this.ImageField);
	    this.CreateBar(pannel);
	    this.viewpanel=this.CreateView(pannel);
	    if(typeof config.Id!=="undefined"){
		this.viewpanel.id = config.Id;
	    }
	    if(this.ImageField.value!==""){
		this.SetImage(this.ImageField.value);
	    }
	},
	CreateBar:function(pannel){
	    var bar = document.createElement("div");
	    var btnUrl = document.createElement("input");
	    var btnManager = document.createElement("input");
	    btnUrl.type = btnManager.type = "button";
	    btnUrl.className = btnManager.className = "ui-button";
	    btnUrl.value = "URL";
	    btnUrl.onclick = this.imageUrlClick;
	    $(bar).append(btnUrl);
	    if(this.ImageField.style.border!==""){
		var style = {
		    "border":this.ImageField.style.border,
		    "color":"red",
		    "background":"#ffcece"
		};
		$(btnUrl).css(style);
		$(btnManager).css(style);
	    }
	    if(typeof CKFinder!=="undefined"){
		btnManager.value = "Галерея";
		btnManager.onclick = this.imageManagerClick;
		$(bar).append(btnManager);
	    }
	    else{
		delete(btnManager);
	    }
	    
	    bar.className = "bar";
	    $(pannel).append(bar);
	},
	CreateView:function(pannel){
	    var view = document.createElement("div");
	    var image = document.createElement("div");
	    var filename = document.createElement("p");
	    view.style.display="none";
	    view.className = "view";
	    image.className = "image";
	    $(view).append(image);
	    $(view).append(filename);
	    $(pannel).append(view);
	    this.ImagePrev = image;
	    this.FileName = filename;
	    return view;
	},
	SetImage:function(image){
	    var img = new Image();
	    img.src = image;
	    img.onload = function(){
		var dimens = image_loader.GetImageDimens(img.width,img.height);
		$(image_loader.ImagePrev).css({
		    "width":config.Width,
		    "height":config.Height,
		    "background-image":"url("+image+")",
		    "background-repeat": "no-repeat",
		    "background-position":dimens.X+" "+dimens.Y,
		    "background-size":dimens.Width+" "+dimens.Height
		});
		var filename = image.substring(image.lastIndexOf('/')+1,image.length);
		image_loader.FileName.innerHTML = filename;
		image_loader.viewpanel.style="";
		image_loader.ImageField.value=image;
	    };
	    img.onerror = function(){
		alert("Изображение не найдено!");
	    };
	},
	GetImageDimens:function(width,height){
	    var imgVertical = height>width;
	    var x=0,y=0,k=1;
	    var vertical = config.Height>config.Width;
	    if(imgVertical&&!vertical){
		k = config.Width/width;
		y = parseInt((config.Height-height*k)/2);
	    }
	    else{
		k = config.Height/height;
		x = parseInt((config.Width-width*k)/2);
	    }
	    return {
		X:x+"px",
		Y:y+"px",
		Width:parseInt(width*k)+"px",
		Height:parseInt(height*k)+"px"
	    };
	},
	imageUrlClick:function(){
	    var url = prompt("Введите ссылку на картинку");
	    if(url){
		image_loader.UploadImage(url);
	    }
	},
	imageManagerClick:function(){
	    CKFinder.modal({
		chooseFiles: true,
		resizeImages: false,
		width: 600,
		height: 450,
		onInit: function (finder) {
		    finder.on('files:choose', function (evt) {
			var file = evt.data.files.first();
			image_loader.SetImage(file.getUrl());
		    });
		    finder.on('file:choose:resizedImage', function (evt) {
			image_loader.SetImage(evt.data.resizedUrl);
		    });
		}
	    });
	},
	UploadImage:function(url){
	    $.ajax({
		method: "POST",
		url: config.ImageUploader,
		data: {
		    "url":url
		}
	    }).done(function(mess) {
		//alert(mess);
		var Data = JSON.parse(mess);
		if(Data.Type!=="error"){
		    image_loader.SetImage(Data.Text);
		}
		else{
		    alert(Data.Text);
		}
	    });
	}
	
    };
    image_loader.Init();
    return image_loader;
}
function addButton(item){
    var btn = document.createElement(item.Type);
    btn.innerHTML = item.Text;
    switch (item.Type){
	case "button":{
	    btn.onclick = item.Click;
	    break;
	}
	case "a":{
	    btn.href = item.Href;
	    break;
	}
    }
    return btn;
}
function addButtons(elem,buttons){
    for(var btnId in buttons){
	var btn = addButton(buttons[btnId]);
	$(elem).append(btn);
	$(btn).button({
	    icon: buttons[btnId].Icon,
	    showLabel: buttons[btnId].Label
	});
    }
}
function getTable(elems,items,styles={}){
    var table = document.createElement("table");
    var thead = document.createElement("thead");
    $(table).append(thead);
    var tr = document.createElement("tr");
    $(thead).append(tr);
    for(var i in elems){
	var th = document.createElement("th");
	th.innerHTML = elems[i];
	$(tr).append(th);
	if(typeof styles[i]!=="undefined"){
	    $(th).css(styles[i]);
	}
    }
    var tbody = document.createElement("tbody");
    $(table).append(tbody);
    for(var i in items){
	var tr = document.createElement("tr");
	$(tbody).append(tr);
	for(var j in elems){
	    var td = document.createElement("td");
	    td.innerHTML = items[i][j];
	    if(typeof styles[j]!=="undefined"){
		$(td).css(styles[j]);
	    }
	    $(tr).append(td);
	}
    }
    return table;
}
function getSelect(options,value){
    var select = document.createElement("select");
    for(var i in options){
	var o = document.createElement("option");
	o.innerHTML = options[i];
	o.value = i;
	if(i===value){
	    o.selected=true;
	}
	$(select).append(o);
    }
    return select;
}
function CreateTabs(config){
    var ct = {
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
		    ct.SetTab($(ui.newPanel).attr("id"));
		},
		create:function(event, ui){
		    ct.SetTab($(ui.panel).attr("id"));
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
		this.Current.init(this);
	    }
	}
    };
    ct.Init();
    return ct;
}
function getOL(list){
    var ol = document.createElement("ol");
    for(var i in list){
	var li = document.createElement("li");
	li.innerHTML=list[i];
	$(ol).append(li);
    }
    return ol;
}
function getUL(list){
    var ul = document.createElement("ul");
    for(var i in list){
	var li = document.createElement("li");
	li.innerHTML=list[i];
	$(ul).append(li);
    }
    return ul;
}
function CreateLIstView(config){
    var lst = {
	List:null,
	Items:[],
	Curent:null,
	Select:function(curent){},
	init:function(){
	    if(typeof config.Select==="function"){
		this.Select = config.Select;
	    }
	    this.List = document.createElement("ol");
	    for(var i in config.List){
		var li = document.createElement("li");
		li.innerHTML=config.List[i];
		li.Index = parseInt(i);
		li.onclick = function(){
		    if(this.Index!==lst.Curent){
			lst.SelectItem(this.Index);
			lst.Select(this,this.Index);
		    }
		};
		this.Items[i] = li;
		$(this.List).append(li);
	    }
	    if(typeof config.Selected!=="undefined"){
		if(typeof this.Items[config.Selected]!=="undefined"){
		    var index = parseInt(config.Selected);
		    lst.SelectItem(index);
		    lst.Select(this,index);
		}
	    }
	    this.List.className = "selectable";
	},
	SelectItem:function(i){
	    if(this.Curent!==null){
		this.Items[this.Curent].className="";
	    }
	    this.Curent = parseInt(i);
	    this.Items[this.Curent].className="selected";
	}
    };
    lst.init();
    return lst.List;
}
function CreateSearch(config){
    var sr={
	Field:null,
	Timeout:null,
	Delay:500,
	Text:"",
	Init:function(){
	    this.Field = document.createElement("input");
	    this.Field.type = "text";
	    this.Field.oninput = this.OnInput;
	    if(typeof config.Search!=="undefined"){
		this.Search = config.Search;
	    }
	},
	OnInput:function(){
	    sr.Text = this.value;
	    if(sr.Timeout!==null){
		clearTimeout(sr.Timeout);
	    }
	    sr.Timeout = setTimeout(sr.Searching,sr.Delay);
	},
	Search:function(){
	    
	},
	Searching:function(){
	    sr.Search(sr.Text);
	},
	Clear:function(){
	    if(this.Timeout!==null){
		clearTimeout(this.Timeout);
	    }
	    this.Field.value="";
	}
    };
    sr.Init();
    return sr;
}
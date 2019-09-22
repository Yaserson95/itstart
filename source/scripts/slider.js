var NEWS = [
	{
		title:"Добро пожаловать",
		mess:"У нас можно найти уникальные и интересные статьи, проверить свои навыки, изучит  чего-нибудь новое и многое другое!",
		pict:"news00.png",
		href:"#"
	},{
		title:"7 полезных программ, улучшающих стандартные функции Windows",
		mess:"Эти бесплатные программы сделают работу в Windows более быстрой и удобной.",
		pict:"frame2.png",
		href:"#"
	},{
		title:"3 программы, которые помогут понять, чем занято место на жёстком диске",
		mess:"С помощью этих трёх утилит вы сможете найти самые большие ненужные файлы на жёстком диске и безжалостно их удалить.",
		pict:"frame3.png",
		href:"#"
	},{
		title:"Добавте свою статью",
		mess:"Если у вас есть что нам рассказать создайте статью. Мы её обязательно прочитаем и оценим.",
		pict:"news00.png",
		href:"#"
	},{
		title:"Новость пять",
		mess:"Текст новости пять",
		pict:"news00.png",
		href:"#"
	}
];
panel={
	buttons:[],
	active:{},
	setBtn:function(arr){
		buttons = arr;
	},
	setActive: function(val){
		if(typeof active != "undefined"){
			active.className="btn";
		}
		active = buttons[val];
		active.className="btnActive";
	},
	getActive:function(){
		return active;
	},
	getIndex:function(){
		if(typeof active != "undefined")
		return active.value;
		else return 0;
	},
	getLength:function(){
		return buttons.length;
	}
}
$(document).ready(function(){
	createPanel($("#slider_panel"));
	setActive(0);
	//setCurent(0,NEWS,$("#slider_frame"));
	//$("#slider_frame").append(newSlide(NEWS[CURR_FRAME]));
    /*addItemsBar($("#slider_panel"));
     CURR_FRAME=newSlide(NEWS[CURR_NEWS]);
	$("#slider_frame").append(CURR_FRAME);
	$("#slider_panel li")[CURR_NEWS].style.listStyleType="disc";
    $("#slider_panel li").each(function(index,elem){
		elem.index=index;
		$(elem).click(setFrame);
    });
    $("#next").click(nextFrame);
    $("#prev").click(prevFrame);
    CURR_FRAME.timeout=setTimeout(nextFrame, 7000);*/
});
function setActive(val){
	panel.setActive(val);
}
function createPanel(node){
	divPrev = document.createElement("div");
	divNext = document.createElement("div");
	spanPrev = document.createElement("span");
	spanNext = document.createElement("span");
	Navigator = document.createElement("div");
	divPrev.id = "prev";
	divNext.id = "next";
	Navigator.id = "navigator";
	var arrB = [];
	for(i=0;i<NEWS.length;i++){
		btn = document.createElement("div");
		btn.className="btn";
		$(Navigator).append(btn);
		btn.value = i;
		btn.onclick=function(){
			setActive(this.value);
		};
		arrB.push(btn);
	}
	$(divPrev).click(prevFrame);
	$(divNext).click(nextFrame);
	panel.setBtn(arrB);
	$(spanPrev).text("<");
	$(spanNext).text(">");
	$(divPrev).append(spanPrev);
	$(divNext).append(spanNext);
	node.append(divPrev);
	node.append(Navigator);
	node.append(divNext);
}
function nextFrame(){
	var cur = panel.getIndex();
	if(cur==panel.getLength()-1)cur=0;
	else cur++;
	setActive(cur);
}
function prevFrame(){
	var cur = panel.getIndex();
	if(cur==0)cur=panel.getLength()-1;
	else cur--;
	setActive(cur);
}
function setFrame(cur,frm,node){
	slade = newSlide(NEWS[frm]);
	if(cur<frm){
		$("#slider_frame").prepend(slade);
	}
	if(cur>frm){
		$("#slider_frame").apend(slade);
	}
}
function newSlide(item){
	divSlide = document.createElement("div");
	divGroup = document.createElement("div");
	hTitle = document.createElement("h1");
	pArt = document.createElement("p");
	imgBack = document.createElement("img");
	$(divSlide).append(divGroup);
	$(divSlide).append(imgBack);
	$(divGroup).append(hTitle);
	$(divGroup).append(pArt);
    $(hTitle).text(item.title);
    $(pArt).text(item.mess);
	imgBack.src="img/"+item.pict;
    divSlide.className="slide";
    return divSlide;
}
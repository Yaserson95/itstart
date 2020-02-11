function Slider(config){
    var slider_cm = {
	Parent:null,
	Current:0,
	Frame:null,
	Frames:null,
	Circle:null,
	Circles:null,
	Timeout:10000,
	TimeoutId:null,
	Init:function(){
	    switch(typeof config.Target){
		case "string":
		    this.Parent = document.getElementById(config.Target);
		    break;
		case "object":
		    this.Parent = config.Target;
		    break;
		default:
		    return 1;
	    }
	    this.Parent.className="slider";
	    var frames = document.createElement("div");
	    $(this.Parent).append(frames);
	    this.Frames = frames;
	    if(config.Slides.length>0){
		this.Current = Math.floor(Math.random() * Math.floor(config.Slides.length));
		this.Frame = this.InitFrame(this.Current);
		$(this.Parent).append(this.InitBar(this.Current));
		$(this.Frames).append(this.Frame);
		this.CalcSizes();
		this.newTimeout(this.Timeout);
	    }
	},
	newTimeout:function(time){
	    if(this.TimeoutId!==null){
		clearTimeout(this.TimeoutId);
	    }
	    this.TimeoutId = setTimeout(function(){
		slider_cm.NextFrame();
	    }, time);
	},
	CalcSizes:function(){
	    var h = $(this.Frame).height();
	    var hl = $(this.Frame).find("div").height();
	    $(this.Frame).find("div").css({
		top:parseInt((h-hl)/2)+"px"
	    });
	},
	InitFrame:function(slide){
	   var frame = document.createElement("div");
	   var img = document.createElement("img");
	   var content = document.createElement("div");
	   frame.className = "frame";
	   $(frame).css({
	       width:config.Width+"px",
	       height:config.Height+"px"
	   });
	   img.src = config.Slides[slide].img;
	   content.innerHTML = config.Slides[slide].html;
	   $(frame).append(img);
	   $(frame).append(content);
	   return frame;
	},
	InitBar:function(slide){
	    var bar = document.createElement("div");
	    var prev = document.createElement("button");
	    var next = document.createElement("button");
	    prev.className = "navi prev";
	    prev.innerHTML = "<";
	    prev.onclick = function(){
		slider_cm.PrevFrame();
	    };
	    next.className = "navi next";
	    next.innerHTML = ">";
	    next.onclick = function(){
		slider_cm.NextFrame();
	    };
	    var circles = document.createElement("span");
	    circles.className = "circles";
	    for(var sl in config.Slides){
		var circle = document.createElement("div");
		circle.id=parseInt(sl);
		circle.onclick = function(){
		    slider_cm.ChangeFrame(this.id);
		};
		if(slide==sl){
		    circle.className="current";
		    this.Circle = circle;
		}
		$(circles).append(circle);
		
	    }
	    $(bar).append(prev);
	    $(bar).append(circles);
	    $(bar).append(next);
	    this.Circles = circles;
	    bar.className="bar";
	    return bar;
	},
	ChangeFrame:function(frame){
	    if(this.Current!==frame){
		this.Current = frame;
		var newFrame = this.InitFrame(frame);
		var oldFrame = this.Frame;
		$(this.Frames).append(newFrame);
		$(newFrame).css({
		    opacity:0
		});
		$(newFrame).animate({
		    opacity:1
		},1000,function(){
		    $(oldFrame).remove();
		});
		this.Frame = newFrame;
		this.CalcSizes();
		this.Circle.className="";
		this.Circle = this.Circles.childNodes[frame];
		this.Circle.className="current";
		this.newTimeout(this.Timeout);
	    }  
	},
	NextFrame:function(){
	    if(this.Current===config.Slides.length-1){
		this.Current = -1;
	    }
	    this.ChangeFrame(parseInt(this.Current)+1);
	},
	PrevFrame:function(){
	    if(this.Current<=0){
		this.Current = config.Slides.length;
	    }
	    this.ChangeFrame(parseInt(this.Current)-1);
	}
    };
    slider_cm.Init();
    return slider_cm;
}

$(document).ready(function(){
    var data = "/Source/IMG/Slider/";
    var config = {
	Width:450,
	Height:300,
	Target:"slider",
	Slides:[
	    {
		img:data+"design.png",
		html:"<h1>Дизайнерам</h1><p>Вы можете найти сдесь оригиналы изображений</p>"
	    },
	    {
		img:data+"developing.png",
		html:"<h1>Разработчикам</h1><p>Тут есть множество различных решений</p>"
	    },
	    {
		img:data+"forums.png",
		html:"<h1>Если у вас возникла проблема</h1><p>Наши эксперты помогут вам!</p>"
	    },
	    {
		img:data+"learning.png",
		html:"<h1>Покажите нам</h1><p>Вы можете поделиться с нами своими знаниями</p>"
	    },
	    {
		img:data+"news.png",
		html:"<h1>Узнайте что-то новое</h1><p>У нас можно найти интересные статьи, которые вам понравятся</p>"
	    }
	]
    };
    var slider = new Slider(config);
});
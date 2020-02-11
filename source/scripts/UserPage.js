function SendImage(config){
    $.ajax({
	method: "POST",
	url: "/Profile/Setphoto",
	data: {Image:config.Patch}
    }).done(function(msg) {
	//alert(msg);
	var Data = JSON.parse(msg);
	if(Data.Type==='info'){
	    location.reload();
	}else if(Data.Type==='error'){
	    switch(parseInt(Data.Value)){
		case 1:
		    alert("Данные не отправленны!");
		    break;
		case 2:
		    alert("Изображение не выбранно!");
		    break;
		case 3:
		    alert("Изображение не выбранно!");
		    break;
		case 4:
		    alert("Изображение не найдено!");
		    break;
	    }
	}
    });
}
function UserPhoto(img){
    var form = document.createElement("form");
    form.id="user_photo_form";
    var image = document.createElement("input");
    form.title = "Выбор фотографии";
    image.type = "text";
    image.value = img.src;
    var cancel = function(){
	$(form).remove();
    };
    $(form).append(image);
    new ImageLoader({
	Replace:image,
	Width:160,
	Height:160,
	ImageUploader:"/Profile/UploadImage",
	Id:"preview"
    }); 
    //image.value = "dd";
    $(form).dialog({
	resizeble:false,
	close:cancel,
	resizable:false,
	width:"360px",
	modal:true,
	buttons:{
	    "Отмена":cancel,
	    "OK":function(){
		SendImage({
		    Image:img,
		    Patch:image.value,
		    Form:form
		});
		
	    }
	}
    });
}

$("#userphoto").ready(function(){
    var photo = document.getElementById("photo");
    //alert(photo.src);
    $(this).find("#loadPhoto").click(function(){
	UserPhoto(photo);
    });
});
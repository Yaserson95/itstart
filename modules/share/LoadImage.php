<?php
$imgPatch = "";
switch($post["type"]){
				case "putch": 
								$imgPatch = strtolower($_SERVER["DOCUMENT_ROOT"].$post['text']);
								break;
				case "url":
								$imgPatch = downloadImage($post['text'],	$_SERVER["DOCUMENT_ROOT"].$path1);
								break;
				default:
								echo json_encode(["type"=>"error","text"=>"Неизвестная команда!"]);
								exit();
}
//Изображение получено
if($imgPatch===false){
				echo json_encode(["type"=>"error","text"=>"Изображение не найдено!"]);
				exit();
}
$imgPatch2 = strtolower($_SERVER["DOCUMENT_ROOT"].$patch2);
$imginfo = pathinfo($imgPatch);
$dirname = $imginfo["dirname"];
				//Если выбран существующий файл в папке
if(checkImgSize($imgPatch,	450,	300)){
			if($imgPatch2==$dirname){
								//Если взяли готовое изображение из нужной папки
								$imgPatch2 =  $imgPatch;
								//echo $imgPatch2;
			}else{
								$imgPatch2 = copyFile($imgPatch,	$imgPatch2);
								if(!copy($imgPatch,$imgPatch2)){
												echo json_encode(["type"=>"error","text"=>"При добавлении изображения произошла ошибка"]);
												exit();
								}
			}
}else{
				$image = imageFormarter($imgPatch,	450,	300);
				$imgPatch2 = copyFile($imgPatch,	$imgPatch2);
				$inf2 = pathinfo($imgPatch2);
				$imgPatch2 = $inf2["dirname"]."/".$inf2["filename"].".png";
				imagepng($image,$imgPatch2);
				imagedestroy($image);
}

$inf2 = pathinfo($imgPatch2);
echo json_encode(["type"=>"image","patch"=>$patch2,"file"=>$inf2["filename"].".png"]);
<div class ='part'>
				<h1><?php echo $this->title; ?></h1>
</div>
<p><a href ='/Articles/Create'>[+] Создать</a></p>
<?php
$ar = $this->Data("articles");
echo $this->Data("navigator");
if(empty($ar)){
?>
<div class = "void">
				<div><h1>В данном разделе отсутствуют материалы!</h1>Вы можете <a href="/Articles/Create">создать</a> их.</div>
</div>
<?php
}else{
				foreach($ar as $item){
								echo "<a class='themes' href='/Articles/Article_".$item["ArtId"]."'><img src='".getUsersUrl($item["Nickname"])."/Articles/Article_".$item["ArtId"].".png'/>
												<h2>".$item["Name"]."</h2><p>".$item["Description"]."</p>
												<br style='clear:both'>
											</a>";
				}
}
echo $this->Data("navigator");
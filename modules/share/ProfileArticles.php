<?php
if(!empty($arrart)){
?>

<table id="artList" cellpadding = "0px" cellspacing = "0px">
				<thead>
				<?php
				$article = new Article();
				$article->setMode(Article::TableShow);
				$columns = $article->getViewColumns();
				echo "<tr>\n";
				foreach($columns as $value){
							echo "<th>".$article->getProperty($value,	"alias")."</th>";
				}
				echo "<th></th>";
				echo "</tr>\n";
				?>
				</thead>
				<tbody>
				<?php
				foreach	($arrart as $art){
				echo "<tr>\n";
								$article->Set($art);
								foreach($columns as $value){
												echo "<td>".$article->getField($value)."</td>";
								}
				echo "<td class='tools'></td>";
				echo "</tr>\n";
				}
				?>
				</tbody>
</table>
<?php
}
else{
?>
<div class = "void">
				<div><h1>У вас нет статей</h1>Вы можете <a href="/Articles/Create">создать</a> их.</div>
</div>
<?php 
}
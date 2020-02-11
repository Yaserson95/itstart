<div class ='part'>
				<h1><?php echo $this->title; ?></h1>
</div>
<form class ="input_form" method="post" action="<?php echo $this->Data("target");?>" id='disc_form'>
				<div class="discform">
				<?php
								echo $this->Message("Error");
								$disc = $this->Data("Disc");
								echo "<label class='row'><span>Заголовок:</span>".$disc->getHtmlField("Title")."</label>";
								$disc->printHtmlField("Text");
								echo "<label class='row'><span>Теги:</span>".$disc->getHtmlField("Tags")."</label>";
				?>
				</div>
				<hr style="width: 70%;min-width: 300px;"/>
				<input type="submit" class='ui-button' value="Сохранить">
</form>
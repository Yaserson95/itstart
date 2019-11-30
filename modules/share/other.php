<?php
class baseHtmlatr{
				public function baseHtmlatr(){}
				protected function atributes (array $atr = null){
								$str = "";
								if($atr!=null){
												foreach	($atr	as	$name	=>	$value)	{
																$str.="$name='$value' ";
												}
								}
								return $str;
				}
}
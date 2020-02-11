<?php
include "other.php";

class	myHtmlHalper extends baseHtmlatr{
				protected $value="";
				public function myHtmlHalper(){
				}
				public static function atributes (array $atr = null){
								$str = "";
								if($atr!=null){
												foreach	($atr	as	$name	=>	$value)	{
																$str.="$name='$value' ";
												}
								}
								return $str;
				}
				public static function DropDownList(string $name, array $data, string $checked=null,array $atr=null){
								$dr = "<select name='$name' ".myHtmlHalper::atributes($atr).">\n";
								foreach	($data	as	$key	=>	$value)	{
												$selected = "";
											if($checked==$key)$selected="selected = 'selected'";
											$dr .="<option value='$key' $selected>$value</option>\n";
								}
								$dr.= "</select>\n";
								return $dr;
				}
				public	function ActionLink(string $title,string $href,$atr = null){
								return "<a href='$href' ".$this->atributes($atr).">$title</a>";
				}
				public function TextArea(string $name,string $value="",array $atr=null){
								return "<textarea name='$name' ".$this->$this->atributes($atr).">$value</textarea>";
				}
				public function Button(string $name,string $value,array $atr=null){
								return "<input type = 'button' name='$name' value = '$value' ".$this->atributes($atr)."/>";
				}
				public function Checkbox(string $name,string $value,bool $checked = false ,array $atr=null){
								$a =  "<input type = 'checkbox' name='$name' value = '$value' ";
								if($checked)$a.="checked = 'checked'";
								$a.=$this->atributes($atr)."/>";
								return $a;
				}
				//public function File(){}
				public function Hidden(string $name,string $value="",array $atr=null){
								return "<input type = 'hidden' name='$name' value = '$value' ".$this->atributes($atr)."/>";
				}
				//public function image(){}
				public function Password(string $name,string $value="",array $atr=null){
								return "<input type = 'password' name='$name' value = '$value' ".$this->atributes($atr)."/>";
				}
				public function Radio(string $name,string $value ,array $atr=null){
								$a =  "<input type = 'radio' name='$name' value = '$value' ";
								$a.=$this->atributes($atr)."/>";
								return $a;
				}
				public function Reset(string $name,string $value=null,array $atr=null){
								return "<input type = 'reset' name='$name' value = '$value' ".$this->atributes($atr)."/>";
				}
				public function Submit(string $name,string $value="",array $atr=null){
								return "<input type = 'submit' name='$name' value = '$value' ".$this->atributes($atr)."/>";
				}
				public function Text(string $name,string $value="",array $atr=null){
								return "<input type = 'text' name='$name' value = '$value' ".$this->atributes($atr)."/>";
				}
				
				
}

class InputHelper extends baseHtmlatr{
				private $inputTypes = ["button","checkbox","file","hidden","image","password","radio","reset","submit","text"];
				protected $name="";
				protected $value="";
				private $atr=[];
				protected $type="";
				private $data=[];
				public function InputHelper(string $type=null, string $name=null, string $value = null){
								if($type!=null)$this->setType	($type);
								if($name!=null)$this->setName	($name);
								if($value!=null)$this->setValue	($value);
				}
				public function setValue(string $value){
								$this->value = $value;
				}
				public function setName(string $name){
								$this->name = $name;
				}
				public function setType(string $type){
								$this->type = $type;
				}
				public function setData(array $data){
								$this->data = $data;
				}
				public function setAtributes(array $atributes){
								$this->atr= $atributes;
				}
				public function addAtribute(string $key,string $value){
								$this->atr[$key] = $value;
				}
				public function build(){
								if(in_array($this->type,	$this->inputTypes)){
												return $this->BuildInput();
								}else{
												return $this->BuildTag();
								}
				}
				private function BuildInput(){
								return "<input type='$this->type' name='$this->name' value='$this->value' "
								.$this->atributes($this->atr)."/>";
				}
				
				private function BuildTag(){
								switch($this->type){
												case "select": return myHtmlHalper::DropDownList($this->name,	$this->data,	$this->value,	$this->atr);
												default	: 
														return	"<$this->type ".$this->atributes($this->atr)." name='$this->name'>$this->value</$this->type>";
								}
								
				}
				
				 
				
}



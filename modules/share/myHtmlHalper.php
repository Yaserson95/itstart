<?php
include "other.php";
class	myHtmlHalper extends baseHtmlatr{
				public function myHtmlHalper(){
				}
				
				public function ActionLink(string $title,string $href,$atr = null){
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
				private $name="";
				private $value="";
				private $atributes=[];
				private $type="";
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
				public function setAtributes(array $atributes){
								$this->atributes = $atributes;
				}
				public function addAtribute(string $key,string $value){
								$this->atributes[$key] = $value;
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
								.$this->atributes($this->atributes)."/>";
				}
				private function BuildTag(){
								return "<$this->type".$this->atributes($this->atributes)." name='$this->name'>$this->value</$this->type>";
				}
}



<?php
const UserPatch = "Data/Users";
abstract class Role{
				const User = 0;
				const Moderator = 1;
				const Admin = 2;
				const BaseAdmin = 3;
				const Blocked = 4;
}
function getUsersData(string $nick = null):string{
				$pt = $_SERVER["DOCUMENT_ROOT"]."/".UserPatch;
				if(!empty($nick)) $pt.="/".$nick;
				if(file_exists($pt)) return $pt;
				else	return "";
}
function getUsersUrl(string $nick = null):string{
				$pt = "/".UserPatch;
				if(!empty($nick)) $pt.="/".$nick;
				return $pt;
}

function IsAutorized():	bool{
				return isset($_SESSION["UserId"])&&isset($_SESSION["Nickname"]);
}
function currentUserData($absolute = true):string{
				if(IsAutorized()){
								if($absolute)	return getUsersData($_SESSION["Nickname"]);
								else return "/".UserPatch."/".$_SESSION["Nickname"];
				}
				return "";
}
function isUrl($link){
				return preg_match('/^(http:\/\/|https:\/\/)?[0-9a-zA-Zа-яА-ЯёЁ]{1,3}+[.][0-9a-zA-Zа-яА-ЯёЁ]+[.][0-9a-zA-Zа-яА-ЯёЁ]{2,6}+$/', $link);
}

function isRole(int $role):bool{
				if(!IsAutorized()) return false;
				return $_SESSION["Priority"]==$role;
}
function RoleAbove(int $role):bool{
				if(!IsAutorized()) return false;
				return ($_SESSION["Priority"]>=$role)&&($_SESSION["Priority"]!=Role::Blocked);
}
function getDisplaySettingsFile(string $nickmame = ""):array{
				$file = getUsersData()."/displaySettings.json";//default settings
				if(!empty($nickmame)){
								$filename = getUsersData($nickmame)."/displaySettings.json";
								if(file_exists($filename)){$file = $filename;}
				}
				return (array)json_decode(file_get_contents($file));
}
function getDisplaySettings(string $nickmame):array{
				//$Parts = ["Articles","Groups","Discussions","Comments"];
				$data = getDisplaySettingsFile($nickmame);
				$settings = [];
				foreach	($data	as	$key	=>	$value)	{
								foreach	((array)$value as $key2=>$item){
												$settings[$key.$key2] = $item;
								}
				}
				return $settings;
}

function setDisplaySettings(array $data,string $nickmame){
				$filename = getUsersData($nickmame)."/displaySettings.json";
				$settings = [];
				foreach	($data as $key=>$value){
								$str1 = preg_replace("/(Order$)|(Sort$)|(Limit$)/i","",	$key);
								$str2 = ltrim($key,$str1);
								$settings[$str1][$str2] = $value;
				}
				$file  = fopen($filename,	"w");
				if(!$file){
								return 1;
				}
				if(!fwrite($file,	json_encode($settings))) return 2;
				return 0;
}

function getOrderBy(string $part):array{
				$Nickname = "";
				if(IsAutorized()){
								$Nickname = $_SESSION["Nickname"];
				}
				$data = getDisplaySettingsFile($Nickname);
				$result = [];
				if(isset($data[$part])){
								$inf = (array)$data[$part];
								$result = [$inf["Sort"]=>$inf["Order"]];
				}
				return $result;
}

function getLimit(string $part):int{
				$Nickname = "";
				if(IsAutorized()){
								$Nickname = $_SESSION["Nickname"];
				}
				$data = getDisplaySettingsFile($Nickname);
				$result = 0 ;
				if(isset($data[$part])){
								$result = $data[$part]->Limit;
				}
				return $result;
}
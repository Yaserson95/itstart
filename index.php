<?php
    include "modules/itstart.php";
    $itstart = new itstart();
    if(isset($_REQUEST["patch"]))$itstart->setPatch($_REQUEST["patch"]);
    $itstart->showPage();
    /*if(isset($_REQUEST['adress'])){
        igetAdr($_REQUEST['adress']);
    }else{ 
        $title = "Главная";
        $content = "home.php";
        array_push($scripts,"slider.js");
        include_once 'modules/page.php';
    }*/

    


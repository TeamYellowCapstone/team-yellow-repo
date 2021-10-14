<?php
    session_start();
    if(!isset($_SESSION["cart"])){
        $_SESSION["cart"] = array();
    }
    
    if(!isset($_COOKIE["sessID"])){
        setcookie("sessID",session_id(), strtotime("+30 days"), "/");
    }
    if(!isset($_COOKIE["cartQty"])){
        setcookie("cartQty", 0,strtotime("+30 days"),"/");
    }
?>
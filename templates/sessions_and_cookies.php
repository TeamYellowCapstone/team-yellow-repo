<?php
    session_start();
    if(!isset($_SESSION["cart"])){
        $_SESSION["cart"] = array();
    }
    if(!isset($_SESSION["cartOption"])){
        $_SESSION["cartOption"] = array();
    }
    if(!isset($_SESSION["cartQty"])){
        $_SESSION["cartQty"] = 0;
    }
    if(!isset($_SESSION["UserID"])){
        $_SESSION["UserID"] = 0;
    }
    if(!isset($_SESSION["role"])){
        $_SESSION["role"] = 3;
    }
?>
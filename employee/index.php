<?php
    session_start();

    if(isset($_SESSION["role"])){
        if($_SESSION["role"] == "employee"){
            echo "found";
        }
    }
    else{
        header("HTTP/1.1 403 Forbidden");
        header("Location: ../index.php");
        return;
    }
?>
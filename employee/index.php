<?php
    session_start();

    if(isset($_SESSION["role"]) && $_SESSION["role"] == 1){
            echo "found";
    }
    else{
        header("HTTP/1.1 403 Forbidden");
        header("Location: ../index.php");
        return;
    }
?>
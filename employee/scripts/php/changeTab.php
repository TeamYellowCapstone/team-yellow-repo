<?php
    session_start();
    if(isset($_SESSION["action"])){
        $_SESSION["action"] = $_SESSION["action"] == "add" ? "update" : "add";
    }
    echo "success";

?>
<?php
    session_start();

    if(isset($_SESSION["role"]) && $_SESSION["role"] == 1){
            
    }
    else{
        header("HTTP/1.1 403 Forbidden");
        header("Location: ../index.php");
        return;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php
        require (dirname(__DIR__).'/templates/head.php');
    ?> 
    <title>Home Page</title>
</head>

    <body>
        <?php
                require (dirname(__DIR__).'/templates/navigation.php');
        ?>
        <div class= "welcome_text">
           
        </div>
        <div class="background-wrap">
            
        </div>
    </body>
</html>
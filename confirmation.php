<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php
            require "templates/head.php";
        ?> 
        <title>Accounted Created</title>
    </head>

    <body>
        <?php
            require "templates/navigation.php";
        ?>
        <?php
            if(isset($_SESSION["acct"])){
                echo "<h1 class='centerText'>Your Account Has Been Created Successfully</h1>";
                echo "<p>You can log in <a href='login.php'>here</a></p>";
                unset($_SESSION["acct"]);
            }
        ?>
        <div class="background-wrap">
            
            </div>
    </body>
</html>
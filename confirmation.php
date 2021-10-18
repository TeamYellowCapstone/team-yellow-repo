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
            $lastTime = new DateTime("2021-10-12 11:21:00"); 
            $currentTime =  new DateTime("2021-10-12 12:21:00");
            $elapsedMinute = $lastTime->diff($currentTime);
            echo "prev: ".$lastTime->format("i")." now: ".$currentTime->format("i")." diff: ".$elapsedMinute->format("%i");
            $lastTime = new DateTime("2021-10-12 12:21:05");
            $currentTime =  new DateTime("2021-10-12 12:35:00");
            $elapsedMinute = ($currentTime->getTimeStamp()) - ($lastTime->getTimeStamp()) ;
            echo "<br>prev: ".$currentTime->format("H-i-s")." now: ".$lastTime->format("H-i-s")." diff: ".$elapsedMinute/60;
        ?>
    </body>
</html>
<?php 
    require "scripts/php/menuPageLoad.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php
        require "templates/head.php";
    ?> 
    <title>Home Page</title>
</head>

    <body>
        <?php
                require "templates/navigation.php";
            ?>
        <div class= "center-text">
            <h1 id="welcome">Welcome
                <?php 
                    if(isset($_SESSION["UserName"]) && isset($_SESSION["role"])){
                        if($_SESSION["role"] == 2){
                            echo "<span class='small-text'> " . $_SESSION["UserName"] . "</span>";
                        }
                    }
                    echo "<span class='small-text'>!</span><br>";
                ?>
            </h1>
            
            <img src="images/bxf6-hero.jpg" alt="cup of coffee with latte foam art" width="35%" class="img home-img">
            <div class="">
                <p> We've progressed from beans simmered in a shelled nut roaster to a variety of smooth, tasty mixtures sold across the country. </p>
            </div>
            <div class="home-wrapper">
                <div class="home-main flex-box">
                    <div class="left-cont first">
                        <p  class="cont-title"> It All Began Here  </p>
                    </div>
                    <div class="right-cont second">
                        <p class="home-p"> In Columbus, we began as Sister`s Coffee, preparing shockingly smooth espresso with only a 12-pound shelled nut roaster. 
                        We quickly outgrew our small roaster and relocated to beautiful Westerville, where we continued to roast and blend delicious espresso. </p>
                    </div>
                </div>
            </div>
            <div class="home-wrapper">
                <div class="home-main flex-box">
                    <div class="left-cont second">
                        <p class="home-p"> We competed for the title of "some espresso in Columbus," and when our silky smooth flavor won us the title, we changed our name to Love You a Latte. </p>
                    </div>
                    <div class="right-cont first">
                        <p class="cont-title"> Becoming Successful </p>
                    </div>
                </div>
            </div>
            <div class="home-wrapper">
                <div class="home-main flex-box">
                    <div class="left-cont first">
                        <p class="cont-title"> Craving for a perfect Coffee or Smoothie?</p>
                        <p class="home-p"> Well, wait no More! Order Now and head to the store.</p>
                        <a href="menu.php" class="btn order-btn">Order</a>
                    </div>
                    <div class="right-cont second">
                        <div class="home-img-div">
                            <img src="images/uploads/breakfast.png" alt="Breakfast image showing coffee, croissant, and orange juice." class="home-img img">
                        </div>
                        
                    </div>
                </div>
            </div>
        </br>
        </div>
        
    </body>
    
</html>
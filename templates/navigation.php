<?php
$currentLocation = $currentLocation == null ? "" : $currentLocation;
if(preg_match("/[.]*\/employee\/[.]*/","".$_SERVER["REQUEST_URI"])){
    $currentLocation = "../";
}


?>
<nav class="navigation">
    <div class="logo"><a><img class="logo-img img" src="<?php echo $currentLocation;?>images/ico/GitHub-Mark.png"></a></div>
    <ul class="navigation-item-container">
        <?php
        echo "
        <li><a href=".$currentLocation."index.php>Home</a></li>
        <li><a href=".$currentLocation."menu.php>Menu</a></li>
        <li><a href=".$currentLocation."faq.php>Faq</a></li>
        <li><a href=".$currentLocation."contact.php>Contact Us</a></li>
        ";
            if(isset($_SESSION["role"]) && $_SESSION["role"] != 3 ){
                echo "<li><a href=".$currentLocation."logout.php>Logout</a></li>";
            }
            else{
                echo "<li><a href=".$currentLocation."login.php>Login</a></li>";
            }
        ?>
        
        <li class="right">
        <?php
        
                if(isset($_SESSION["FirstName"]) && $_SESSION["role"] != 3){
                    $location = "index.php";
                    if(isset($_SESSION["role"])){
                        if($_SESSION["role"] == 1){
                            $location = "employee/maintenance.php";
                        }
                        else{
                            $location = "user/index.php";
                        }
                    }
                    echo "<p id='fname'><a href=".$currentLocation.$location.">";
                    echo $_SESSION["FirstName"];
                    echo "</a></p>";
                }
                
        ?>
        </li>
        <?php
            echo "<a href=".$currentLocation."cart.php id='a-cart'  class='right'><div id='cart-container'>
                <span id='qty'>";
                if(isset($_SESSION["cartQty"])){
                    echo $_SESSION["cartQty"];
                }
                else{
                    echo 0;
                }
                echo "</span></div></a>";
        ?>
    </ul>
    <div class="menu-icon" id="menu-bar">
        <div class="top"></div>
        <div class="middle"></div>
        <div class="bottom"></div>            
    </div>
    
</nav>

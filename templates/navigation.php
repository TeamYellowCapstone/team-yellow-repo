<?php
$currentLocation = $currentLocation == null ? "" : $currentLocation;
if(preg_match("/[.]*\/employee\/[.]*/","".$_SERVER["REQUEST_URI"])){
    $currentLocation = "../";
}


?>
<nav class="navigation">
    <ul>
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
                echo "<a href=".$currentLocation."cart.php id='a-cart'><div id='cart-container'>
                            <span id='qty'>";
                if(isset($_SESSION["cartQty"])){
                    echo $_SESSION["cartQty"];
                }
                else{
                    echo 0;
                }
                echo "</span></div></a>";
        ?>
        </li>
    </ul>
    
</nav>

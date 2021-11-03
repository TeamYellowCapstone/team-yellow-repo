<?php
$parent = "";
if(preg_match("/[.]*\/employee\/[.]*/","".$_SERVER["REQUEST_URI"])){
    $parent = "../";
}


?>
<nav class="navigation">
    <ul>
        <?php
        echo "
        <li><a href=".$parent."index.php>Home</a></li>
        <li><a href=".$parent."menu.php>Menu</a></li>
        <li><a href=".$parent."faq.php>Faq</a></li>
        <li><a href=".$parent."contact.php>Contact Us</a></li>
        ";
            if(isset($_SESSION["role"])){
                echo "<li><a href=".$parent."logout.php>Logout</a></li>";
            }
            else{
                echo "<li><a href=".$parent."login.php>Login</a></li>";
            }
        ?>
        
        <li class="right">
        <?php
        
                if(isset($_SESSION["FirstName"])){
                    $location = "index.php";
                    if(isset($_SESSION["role"])){
                        if($_SESSION["role"] == 1){
                            $location = "employee/maintenance.php";
                        }
                        else{
                            $location = "user/index.php";
                        }
                    }
                    echo "<p id='fname'><a href=".$parent.$location.">";
                    echo $_SESSION["FirstName"];
                    echo "</a></p>";
                }
                echo "<a href=".$parent."cart.php id='a-cart'><div id='cart-container'>
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

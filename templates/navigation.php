<nav class="navigation">
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="menu.php">Menu</a></li>
        <li><a href="faq.php">Faq</a></li>
        <li><a href="contact.php">Contact Us</a></li>
        <?php
            if(isset($_SESSION["role"])){
                echo "<li><a href='logout.php'>Logout</a></li>";
            }
            else{
                echo "<li><a href='login.php'>Login</a></li>";
            }
        ?>
        
        <li class="right">
        <?php
        
                if(isset($_SESSION["FirstName"])){
                    echo "<p id='fname'><a href='#'>";
                    echo $_SESSION["FirstName"];
                    echo "</a></p>";
                }
                echo "<a href='cart.php' id='a-cart'><div id='cart-container'>
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

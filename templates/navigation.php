<nav class="navigation">
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="menu.php">Menu</a></li>
        <li><a href="faq.php">Faq</a></li>
        <li><a href="contact.php">Contact Us</a></li>
        <li><a href="login.php">Login</a></li>
        <li class="right">
        <?php
        
                if(isset($_SESSION["FirstName"])){
                    echo "<p id='fname'><a href='#'>";
                    echo $_SESSION["FirstName"];
                    echo "</a></p>";
                }
                    echo "<div class='cart-cont-li'><div id='cart-container'>
                            <div class='cart-display'>
                                <span id='qty'>";
                    if(isset($_COOKIE["cartQty"])){
                        echo $_COOKIE["cartQty"];
                    }
                    else{
                        echo 0;
                    }
                    echo "</span></div></div>";
        ?>
        </li>
    </ul>
    
</nav>

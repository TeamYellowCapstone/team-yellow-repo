<nav class="navigation">
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="menu.php">Menu</a></li>
        <li><a href="faq.php">Faq</a></li>
        <li><a href="contact.php">Contact Us</a></li>
    </ul>
    <div id="cart-container">
        <div class="cart-display">
            <?php
                echo "<span id='qty'>";
                if(isset($_COOKIE["cartQty"])){
                    echo $_COOKIE["cartQty"];
                }
                else{
                    echo 0;
                }
                echo "</span>";
            ?>
        </div>
    </div>
</nav>

<?php
    require "scripts/php/menuPageLoad.php";
    require "connection/connection.php";

    if($conn->connect_error){
        die("Connection Error");
    }
    $products = array();
    $productSize = array();
    $query = "SELECT * FROM Product_Item;";
    if($result = $conn->query($query)){
        $products = $result->fetch_all(MYSQLI_ASSOC);
    }
    $query = "SELECT * FROM Product_Size;";
    if($result = $conn->query($query)){
        $productSize = $result->fetch_all(MYSQLI_ASSOC);
    }
    $conn->close();
?>
<!Doctype html>
<html>
    <head>
    <?php
        require "templates/head.php";
    ?>
    <script type="text/javascript" src="scripts/add-to-cart.js" defer></script>
    <!-- <script type="text/javascript" src="scripts/clear-cart.js" defer></script>
    <script type="text/javascript" src="scripts/checkout.js" defer></script> -->
    <title>Menu</title>
    </head>
    <body>
        <?php
            require "templates/navigation.php";
        ?>
        <div class="content menu">
            <h1 class="centerText"><u>Our Menu</u></h1>
            <div class="menu-item-container flex-box">
                <?php
                    foreach($products as $product){
                        echo "
                        <div class='menu-item' id='item".$product['ItemID']."'>
                            <h2 class='item-name centerText'>".$product['ProductName']."</h2>
                            <p class='item-desc'>".$product["Description"]."</p>
                            <div class='item-size-container'>";
                            foreach ($productSize as $size){
                                echo "
                                <div class='item-size'>
                                    <input class='radio radio-btn size' type='radio' name='size".$product['ItemID']."' id='". strtolower($size['SizeName']).$product['ItemID']."' value={$size['SizeID']}>
                                    <label for='". strtolower($size['SizeName']).$product['ItemID']."'>".substr($size['SizeName'],0,1)."</label>
                                </div>";                                
                            } 
                            echo 
                            "<p class='item-price'>Price: ".$product['Price']."</p>
                            </div>
                            <button class='btn add-to-cart'>Add to Cart</button>
                        </div>";
                    }
                ?>
            </div>
            <!-- <button class="btn" id="clear-cart">Clear Cart</button>
            <button class="btn" id="checkout-btn">Checkout</button> -->
        </div>
        <div class="background-wrap">
        
        </div>

    </body>
</html>
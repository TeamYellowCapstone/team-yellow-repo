<?php 
    require "scripts/php/menuPageLoad.php";
    
    $user = isset($_SESSION["UserID"]) ? $_SESSION["UserID"] : 0;
    // if user is not logged skip to the html display
    if($user != 0){
        
        require "connection/connection.php";
        if($conn->connect_error) {
            die("Connection Error");
        }
        
        //fetch cart item
        // $cart_query = "SELECT CartView.ID, CartView.ProductName, CartView.Price, PricePercentage, SizeName, CartView.Quantity, OptionTotalPrice FROM CartView 
        // INNER JOIN OptionPriceView ON CartView.ID = OptionPriceView.ID
        // WHERE CartView.UserID = ?;";
        // $cart_stmt = $conn->prepare($cart_query);
        // $cart_stmt->bind_param("i",$user);
        // $cart_stmt->execute();
        // $cart_result = $cart_stmt->get_result();

        // $cart_stmt->close();
        // $conn->close();
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php
            require "templates/head.php";
        ?> 
        <!-- <script type="text/javascript" src="scripts/clear-cart.js" defer></script> -->
        <script type="text/javascript" src="scripts/checkout.js" defer></script>
        <!-- <script type="text/javascript" src="scripts/remove-item.js" defer></script> -->
        <script type="text/javascript" src="scripts/login.js" defer></script>
        <title>Checkout</title>
    </head>

    <body>
        <?php
            require "templates/navigation.php";
        ?>
        <h1 class="centerText">Your Latte</h1>
        <?php
            if($user == 0){
                echo "<div class='container-fluid'>";
                    echo "<div class='login-form form'>";
                        require_once "templates/loginTemplate.php";
                    echo "</div>";
                    echo "<hr>";
                    echo "<div class='guest-div'>";
                        echo "<a href='checkout/guestCheckout.php' class='btn btn-link'>Gust Checkout</a>";
                    echo "</div>";
                echo "</div>";
            }
            else{
                echo "
                <div id='display'>
                    <button id='checkout-btn' class='btn'>Checkout</button>
                </div>";
            }
        ?>
        <div class="background-wrap">
            
        </div>
    </body>
</html>
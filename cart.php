<?php 
    require "scripts/php/menuPageLoad.php";
    require "connection/connection.php";

    if($conn->connect_error) {
        die("Connection Error");
    }
    $user = isset($_SESSION["UserID"]) ? $_SESSION["UserID"] : 0;
    if($_SERVER["REQUEST_METHOD"] == "GET"){
        if(isset($_GET["item"])){
            $id = $_GET["item"];
            $query = "DELETE FROM Cart WHERE CartID = ? AND UserID = ?;";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ii",$id,$user);
            $stmt->execute();
            $stmt->close();
            $conn->close();
            $_SESSION["remove_item"] = "Item has been removed";
            header ("Location: cart.php");
            return;
        }
    }

    
    
    $cart_query = "SELECT * FROM CartView WHERE UserID = ?;";
    $cart_stmt = $conn->prepare($cart_query);
    $cart_stmt->bind_param("i",$user);
    $cart_stmt->execute();
    $cart_result = $cart_stmt->get_result();
    if($cart_result->num_rows > 0){
        
    }
    else{

    }
    $cart_stmt->close();
    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php
        require "templates/head.php";
    ?> 
    <script type="text/javascript" src="scripts/clear-cart.js" defer></script>
    <script type="text/javascript" src="scripts/checkout.js" defer></script>
    <!-- <script type="text/javascript" src="scripts/remove-item.js" defer></script> -->
    <title>Cart</title>
</head>

    <body>
        <?php
            require "templates/navigation.php";
        ?>
        <h1 class="centerText">Your Latte</h1>
        <?php
            if(isset($_SESSION["remove_item"])){
                echo "<p>".$_SESSION["remove_item"]."</p>";
                unset($_SESSION["remove_item"]);
            }
        ?>
        <!-- start display items here -->
        <div id="cart-display">
            <?php require_once "scripts/php/displayCartItems.php";?>
        </div>
        <!-- end display items here -->
        <button class="btn" id="clear-cart">Clear Cart</button>
        <button class="btn" id="checkout-btn">Checkout</button>
        <div class="overlay">
            <div id="display-cont">
                <div id="close"><span>&cross;</span></div>
                <div id="display"></div>
                <button class='btn close-btn' id='close-btn'>Close</button>

            </div>
        </div>
        <div class="background-wrap">
            
        </div>

    </body>
</html>
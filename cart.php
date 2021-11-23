<?php 
    require "scripts/php/menuPageLoad.php";
    require "connection/connection.php";

    if($conn->connect_error) {
        die("Connection Error");
    }
    $user = isset($_SESSION["UserID"]) ? $_SESSION["UserID"] : 0;

    //delete individual item from cart
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

    //fetch cart item
    $cart_result;
    $cart_option_result;
    if($user == 0){
        $cart_result = array();
        $cart_option_result = $_SESSION["options"];
        $counter = 0;
        foreach($_SESSION["cart"] as $value => $item){
            $key = array_keys($item)[0];
            array_push($cart_result,array("ProductName"=>$item[$key]["ProductName"],"SizeName"=>$item[$key]["SizeName"],
            "ID"=>$item[$key]["id"],"Quantity"=>$item[$key]["qty"],"Price"=>$item[$key]["Price"],"PricePercentage"=>$item[$key]["PricePercentage"],"CartID"=>$item[$key]["CartID"]));
        }
    }
    else{
        $cart_query = "SELECT * FROM CartView WHERE UserID = ?;";
        $cart_stmt = $conn->prepare($cart_query);
        $cart_stmt->bind_param("i",$user);
        $cart_stmt->execute();
        $cart_result = $cart_stmt->get_result();
        $cart_stmt->close();

        $cart_option_query = "SELECT * FROM CartOptionView WHERE UserID = ?;";
        $cart_stmt = $conn->prepare($cart_option_query);
        $cart_stmt->bind_param("i",$user);
        $cart_stmt->execute();
        $cart_option_result = $cart_stmt->get_result();
        $cart_stmt->close();

    }
    
    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php
        require "templates/head.php";
    ?> 
    <script type="text/javascript" src="scripts/clear-cart.js" defer></script>
    <!-- <script type="text/javascript" src="scripts/checkout.js" defer></script> -->
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
        
        <?php
            if( $_SESSION["cartQty"] != 0){
                echo "<button class='btn' id='clear-cart'>Clear Cart</button>";
                echo "<a class='btn' id='checkout-btn' href='checkout.php'>Checkout</button>";
            }
        ?>
        <div class="background-wrap">
            
        </div>

    </body>
</html>
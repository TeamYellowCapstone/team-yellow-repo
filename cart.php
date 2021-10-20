<?php 
    require "scripts/php/menuPageLoad.php";
    require "connection/connection.php";

    if($conn->connect_error) {
        die("Connection Error");
    }
    $user = isset($_SESSION["UserID"]) ? $_SESSION["UserID"] : 0;
    // $query = "SELECT Product_Item.ProductName, Product_Item.Price, Product_Size.PricePercentage, Product_Size.SizeName, Cart.Quantity FROM Cart 
    //             INNER JOIN Product_Item ON
    //             Product_Item.ItemID = Cart.ItemID
    //             INNER JOIN Product_Size ON
    //             Product_Size.SizeID = Cart.SizeID
    //             WHERE Cart.UserID = ?;";
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
    <title>Cart</title>
</head>

    <body>
        <?php
            require "templates/navigation.php";
        ?>
        <h1 class="centerText">Your Latte</h1>
        <?php
            if(isset($_SESSION["cartQty"])){
                if($_SESSION["cartQty"] == 0){
                    echo "<p class='centerText'>Your cart is empty!</p>";
                }
                else{
                    $cartQty = 0;
                    $cartTotal = 0;
                    echo "<table class='tbl'> 
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Size</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>'";
                    foreach($cart_result as $item){
                        echo "<tr>
                                <td>".$item["ProductName"]."</td>
                                <td>".$item["SizeName"]."</td>
                                <td>".$item["Price"] + ($item["Price"]*$item["PricePercentage"]/100)."</td>
                                <td>".$item["Quantity"]."</td>
                                <td>".($item["Price"] + ($item["Price"]*$item["PricePercentage"]/100)) * $item["Quantity"]."</td>
                            <tr>";
                            $cartQty += $item["Quantity"];
                            $cartTotal += ($item["Price"] + ($item["Price"]*$item["PricePercentage"]/100)) * $item["Quantity"];
                    }
                    echo "<tr>
                            <td colspan='3'></td>
                            <td>".$cartQty."</td>
                            <td>".$cartTotal."</td>
                        </tr>
                        </tbody>
                        </table>";
                }
            }
            else{
                    echo "<p class='centerText'>Your cart is empty!</p>";
            }

        ?>
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
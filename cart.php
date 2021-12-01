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
            if($user != 0){
                $query_cart_item = "SELECT MasterSKU, Quantity FROM Cart WHERE UserID = ? AND CartID = ?";
                $stmt = $conn->prepare($query_cart_item);
                $stmt->bind_param("ii", $user, $id);
                $sku;
                $invQty;
                if($stmt->execute()){
                    $inv_result = $stmt->get_result();
                    while($row = $inv_result->fetch_assoc()){
                        $sku = $row["MasterSKU"];
                        
                    }
                }
                //update inventory
                $query = "UPDATE Product_Item SET Quantity = (Quantity + ?) WHERE MasterSKU = ? AND Quantity > 0;";
                $stmt = $conn->prepare($query);
                $invQty = 1;
                $stmt->bind_param("is",$invQty,$sku);
                $stmt->execute();
                $stmt->close();
                //update inventory for options
                $query_option = "SELECT OptionMasterSKU, Quantity FROM Cart_Options WHERE CartID = ?;";
                $stmt_option = $conn->prepare($query_option);
                $stmt_option->bind_param("i", $id);
                
                if($stmt_option->execute()){
                    $option_result = $stmt_option->get_result();
                    foreach ($option_result as $result_key) {
                        $query_inv = "UPDATE Product_Item SET Quantity = (Quantity + ?) WHERE MasterSKU = ?;";
                        $stmt_inv = $conn->prepare($query_inv);
                        $invQty = $result_key["Quantity"];
                        $sku = $result_key["OptionMasterSKU"];
                        $stmt_inv->bind_param("is",$invQty,$sku);
                        $stmt_inv->execute();
                        $stmt_inv->close();
                    }
                }
                $stmt_option->close();

                //delete item if qty = 1
                $query_delete = "DELETE FROM Cart WHERE CartID = ? AND UserID = ? AND Quantity = 1;";
                $stmt = $conn->prepare($query_delete);
                $stmt->bind_param("ii",$id,$user);
                $stmt->execute();
                $stmt->close();
                //if more qty reduce qty
                $query_update = "UPDATE Cart SET Quantity = (Quantity - 1) WHERE CartID = ? AND UserID = ? AND Quantity > 1;";
                $stmt = $conn->prepare($query_update);
                $stmt->bind_param("ii",$id,$user);
                $stmt->execute();
                $stmt->close();
                
                //display
                $_SESSION["remove_item"] = "Item has been removed";
                $_SESSION["cartQty"] -= 1;
                header ("Location: cart.php");
                return;
            }
            else{
                $previous = $_SESSION["cart"];
                $_SESSION["cart"] = array();
                for ($index=0; $index < count($previous); $index++) { 
                    if($index != $id){
                        array_push($_SESSION["cart"],$previous[$index]);
                    }
                    else{
                        if($previous[$index][array_key_first($previous[$index])]["qty"] > 1){
                            $previous[$index][array_key_first($previous[$index])]["qty"] -= 1;
                            array_push($_SESSION["cart"],$previous[$index]);
                            $_SESSION["cartQty"] -= 1;
                        }
                        else{
                            $_SESSION["cartQty"] -= 1;
                        }
                            
                    }
                }
            }
           
        }
    }

    //fetch cart item
    $cart_result;
    // $cart_option_result;
    $option_price = 0;
    if($user == 0){
        $cart_result = array();
        $option_price = 0;
        // $cart_option_result = array();
        foreach($_SESSION["cart"] as $value => $item){
            $key = array_keys($item)[0];
            foreach($item[$key]["option"] as $price){
                $option_price += $price["price"];
            }
            array_push($cart_result,array("ProductName"=>$item[$key]["ProductName"],"SizeName"=>$item[$key]["SizeName"],
            "ID"=>$item[$key]["id"],"Quantity"=>$item[$key]["qty"],"Price"=>round(($item[$key]["Price"] * $item[$key]["PricePercentage"]/100)+$item[$key]["Price"],2),
            "Options"=>$item[$key]["option"]));
        }
    }
    else{
        $cart_query = "SELECT * FROM CartView WHERE CartView.UserID = ?;";
        $cart_stmt = $conn->prepare($cart_query);
        $cart_stmt->bind_param("i",$user);
        $cart_stmt->execute();
        $cart_result = $cart_stmt->get_result();
        $cart_stmt->close();

        $cart_option_query = "SELECT * FROM CartOptionView WHERE UserID = ?;";
        $opt_stmt = $conn->prepare($cart_option_query);
        $opt_stmt->bind_param("i",$user);
        $opt_stmt->execute();
        $cart_option_result = $opt_stmt->get_result();
        $opt_stmt->close();

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
            require "templates/message_box.php";
        ?>
        <h1 class="centerText">Your Latte</h1>
        <?php
            if(isset($_SESSION["remove_item"])){
                echo "<p>".$_SESSION["remove_item"]."<p>";
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
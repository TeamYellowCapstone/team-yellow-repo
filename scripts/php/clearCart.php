<?php
    require "../../templates/sessions_and_cookies.php";

    include_once "../../connection/connection.php";


    if($conn->error){
        die("error:" . mysqli_error);
    }
    $message = "Error has occured while connecting to the database.";

    $user = isset($_SESSION["UserID"]) ? $_SESSION["UserID"] : 0;
    //update inventory first
    $query_cart_item = "SELECT MasterSKU, Quantity, CartID FROM Cart WHERE UserID = ?;";
    $stmt = $conn->prepare($query_cart_item);
    $stmt->bind_param("i", $user);
    if($stmt->execute()){
        $result = $stmt->get_result();
        foreach ($result as $key) {
            $query_inv = "UPDATE Product_Item SET Quantity = (Quantity + ?) WHERE MasterSKU = ?;";
            $stmt_inv = $conn->prepare($query_inv);
            $invQty = $key["Quantity"];
            $sku = $key["MasterSKU"];
            $stmt_inv->bind_param("is",$invQty,$sku);
            $stmt_inv->execute();
            $stmt_inv->close();
            //update inventory for options
            $query_option = "SELECT OptionMasterSKU, Quantity FROM Cart_Options WHERE CartID = ?;";
            $stmt_option = $conn->prepare($query_option);
            $stmt_option->bind_param("i", $key["CartID"]);
            
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
        }
    }
    $stmt->close();
    
    //delete the user identifier from the cart
    $query_select = "DELETE FROM Cart WHERE UserID = ?;";
    $stmt = $conn->prepare($query_select);
    $stmt->bind_param("i",$user);
    $stmt->execute();
    if($stmt){
        //update qty to display
        unset($_SESSION["cartQty"]);
        //cleare session
        unset($_SESSION["cart"]);
        $message = "Your cart has been cleared!";
    }
    $stmt->close();
    $conn->close();
    echo $message;

?>
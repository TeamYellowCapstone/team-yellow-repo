<?php

    if(isset($_SESSION["cart"]) && isset($_SESSION["UserID"])){
        include "connection/connection.php";

        if($conn->connect_error){
            die("connection Error");
        }

        //also update qty
        if($_SESSION["UserID"] != 0){
            foreach($_SESSION["cart"] as $item){
                $query = "INSERT INTO Cart (UserID, MasterSKU, SizeID, Quantity) VALUES (?,?,?,?);";
                $cart_stmt = $conn->prepare($query);
                $userID = $_SESSION["UserID"];
                $itemid = $item["id"];
                $sizeid = intval($item["size"]);
                $qty = intval($item["qty"]);
                $cart_stmt->bind_param("isii",$userID,$itemid,$sizeid,$qty);
                if($cart_stmt->execute()){
                    $_SESSION["cart"] = array();
                }
                else{
                    //
                }

            }
        }
        
    }
?>
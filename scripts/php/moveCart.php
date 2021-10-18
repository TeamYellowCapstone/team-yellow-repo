<?php
    session_start();

    if(isset($_SESSION["cart"]) && isset($_SESSION["UserID"])){
        include "../../connection/connection.php";

        if($conn->connect_error){
            die("connection Error");
        }
        if($_SESSION["UserID"] != 0){
            foreach($_SESSION["cart"] as $item){
                $query = "INSERT INTO Cart (UserID, ItemID, SizeID, Quantity) VALUES (?,?,?,?);";
                $cart_stmt = $conn->prepare($query);
                $userID = $_SESSION["UserID"];
                $itemid = intval($item["id"]);
                $sizeid = intval($item["size"]);
                $qty = intval($item["qty"]);
                $cart_stmt->bind_param("iiii",$userID,$itemid,$sizeid,$qty);
                if($cart_stmt->execute()){
                    //
                }
                else{
                    //
                }

            }
        }
        
    }
?>
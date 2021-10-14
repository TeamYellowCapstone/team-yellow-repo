<?php
    //this is needed to initiate cookie and session for all pages
    require "../../templates/sessions_and_cookies.php";
    //use this inorder to avoid uploading to github accidenetaly
    require_once "../../connection/connection.php";


    if($conn->error){
        die("error:" . mysqli_error);
    }
    
    $message = "";
    //if the request comes with item id and item size id process it
    if(isset($_GET["id"]) && isset($_GET["size"])){
        
        //get item ID, SizeID, and sessionID
        $currID = $_GET["id"];
        $currSize = $_GET["size"];
        $cookie = $_COOKIE["sessID"];

        //before adding item to cart check if there is the same item with same size and session
        $query_select = "SELECT * FROM Cart WHERE SessionID = ? AND ItemID = ? AND SizeID = ?;";

        $stmt = $conn->prepare($query_select);
        $stmt->bind_param("sii",$cookie,$currID,$currSize);
        $stmt->execute();
        $stmt->store_result();

        //from the above request if item, size and session exists in the database update the qty
        if($stmt->num_rows == 1){
            $query_update = "UPDATE Cart SET Quantity = Quantity + 1 WHERE SessionID = ? AND ItemID = ? AND SizeID = ?;";
            $stmt->close();
            $stmt = $conn->prepare($query_update);
            $stmt->bind_param("sii",$cookie,$currID,$currSize);
            $stmt->execute();
            $stmt->close();
            setcookie("cartQty", $_COOKIE["cartQty"] + 1,strtotime("+30 days"),"/");//update cookie to display qty
            $message = "The quantity of the current item has been updated.";
        }
        //if item doesn't exist in the db add the item to the db
        else if($stmt->num_rows == 0){
            $query_insert = "INSERT INTO Cart (SessionID, ItemID, SizeID, Quantity) VALUES (?,?,?,?);";
            $stmt->close();
            $stmt = $conn->prepare($query_insert);
            $qty = 1;
            $stmt->bind_param("siii",$cookie,$currID,$currSize,$qty);
            $stmt->execute();
            $stmt->close();
            setcookie("cartQty", $_COOKIE["cartQty"] + 1,strtotime("+30 days"),"/");
            $message = "Item has been added to the cart.";
        }
        else{
            $message = "Error Adding Item";
        }
    }
    else{
        $message = "Error Adding Item";
    }
    $conn->close();
    echo $message;
?>
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
        
        //get item ID, SizeID, and UserID
        $currID = $_GET["id"];
        $currSize = $_GET["size"];
        $user = isset($_SESSION["UserID"]) ? $_SESSION["UserID"] : 0;

        //if logged in
        if($user !=0){
            //before adding item to cart check if there is the same item with same size and session
            $query_select = "SELECT * FROM Cart WHERE UserID = ? AND ItemID = ? AND SizeID = ?;";

            $stmt = $conn->prepare($query_select);
            $stmt->bind_param("iii",$user,$currID,$currSize);
            $stmt->execute();
            $stmt->store_result();

            //from the above request if item, size and session exists in the database update the qty
            if($stmt->num_rows == 1){
                $query_update = "UPDATE Cart SET Quantity = Quantity + 1 WHERE UserID = ? AND ItemID = ? AND SizeID = ?;";
                $stmt->close();
                $stmt = $conn->prepare($query_update);
                $stmt->bind_param("iii",$user,$currID,$currSize);
                $stmt->execute();
                $stmt->close();
                $_SESSION["cartQty"] += 1;//update cookie to display qty
                $message = "The quantity of the current item has been updated.";
            }
            //if item doesn't exist in the db add the item to the db
            else if($stmt->num_rows == 0){
                $query_insert = "INSERT INTO Cart (UserID, ItemID, SizeID, Quantity) VALUES (?,?,?,?);";
                $stmt->close();
                $stmt = $conn->prepare($query_insert);
                $qty = 1;
                $stmt->bind_param("iiii",$user,$currID,$currSize,$qty);
                $stmt->execute();
                $stmt->close();
                $_SESSION["cartQty"] += 1;//update cookie to display qty
                $message = "Item has been added to the cart.";
            }
            else{
                $message = "Error Adding Item";
            }
        }
        else{
            //{"idsize"=>{id,size,qty,price},""=>{}}
            if(isset($_SESSION["cart"][$currID.",".$currSize])){
                $_SESSION["cart"][$currID.",".$currSize]["qty"] += 1; 
                $message = "Item has been updated.";
            }
            else{
                $_SESSION["cart"][$currID.",".$currSize] = array("id"=>$currID,"size"=>$currSize,"qty"=>1,"price"=>12);
                $message = "Item has been added to the cart.";
            }
        }
        
    }
    else{
        $message = "Error Adding Item";
    }
    $conn->close();
    echo $message;
?>
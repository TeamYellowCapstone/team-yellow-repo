<?php
    require "../../templates/sessions_and_cookies.php";

    include_once "../../connection/connection.php";


    if($conn->error){
        die("error:" . mysqli_error);
    }
    $message = "Error has occured while connecting to the database.";

    $cookie = $_COOKIE["sessID"];
    //delete the user identifier from the cart
    $query_select = "DELETE FROM Cart WHERE SessionID = ?;";
    $stmt = $conn->prepare($query_select);
    $stmt->bind_param("s",$cookie);
    $stmt->execute();
    if($stmt){
        //update qty to display
        setcookie("cartQty",0, strtotime("+30 days"),"/");
        $message = "Your cart has been cleared!";
    }
    $stmt->close();
    $conn->close();
    echo $message;

?>
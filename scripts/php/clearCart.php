<?php
    require "../../templates/sessions_and_cookies.php";

    include_once "../../connection/connection.php";


    if($conn->error){
        die("error:" . mysqli_error);
    }
    $message = "Error has occured while connecting to the database.";

    $user = isset($_SESSION["UserID"]) ? $_SESSION["UserID"] : 0;
    //delete the user identifier from the cart
    $query_select = "DELETE FROM Cart WHERE UserID = ?;";
    $stmt = $conn->prepare($query_select);
    $stmt->bind_param("i",$user);
    $stmt->execute();
    if($stmt){
        //update qty to display
        setcookie("cartQty",0, strtotime("+30 days"),"/");
        //cleare session
        unset($_SESSION["cart"]);
        $message = "Your cart has been cleared!";
    }
    $stmt->close();
    $conn->close();
    echo $message;

?>
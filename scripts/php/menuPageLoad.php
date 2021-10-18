<?php
    //this file is used to initialize the session and cookie for all web pages
    require "templates/sessions_and_cookies.php";
    require_once "connection/connection.php";

    if($conn->error){
        die("Connection Error". $conn->mysqli_error);
    } 
    $user = isset($_SESSION["UserID"]) ? $_SESSION["UserID"] : 0;
    //get qty from db cart if logged in
    if($user != 0){
        $query = "SELECT SUM(Quantity) AS Qty FROM Cart WHERE UserID = ?;";
        $result = runQuery($query, $user, "i", $conn);
        if($result->num_rows > 0){
            $row = $result->fetch_assoc();
            setcookie("cartQty", $row["Qty"], strtotime("+30 days"),"/");
        }
    }
    //get qty from session if user not logged in yet
    else{
        $qty = 0;
        $cart = $_SESSION["cart"];
        foreach($cart as $item){
            $qty = $qty + $item["qty"];
        }
        setcookie("cartQty", $qty, strtotime("+30 days"),"/");
    }

    $conn->close();
    function runQuery($sqlQuery,$value,$type,$conn){
        $sql = $sqlQuery;

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($type,$value);

        $stmt->execute();
        $r = $stmt->get_result();
        $stmt->close();
        return $r;
    }

    echo "";
?>
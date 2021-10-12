<?php
    require "../../templates/sessions_and_cookies.php";
    require_once "../../connection/connection.php";
    
    $message = "Fail";
    if(isset($_GET["id"]) && isset($_GET["size"])){
        $currID = $_GET["id"];
        $sizeID = $_GET["size"];
        if($conn->error){
            die("Connection Error: ". mysqli_error);
        }
        $query = "SELECT Price, PricePercentage FROM Product_Item, Product_Size WHERE ItemID = ? and SizeID = ?;";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii",$currID, $sizeID);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows == 1){
            $row = $result->fetch_assoc();
            $price = $row["Price"];
            $percentage = $row["PricePercentage"];
            $message = $price + $price*$percentage/100;
        }
    }
    else{
        $message = "Fail";
    }
    echo $message;
?>
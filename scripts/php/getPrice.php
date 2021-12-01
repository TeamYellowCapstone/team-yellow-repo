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
        $query = "SELECT Price, PricePercentage FROM Product_Item, Product_Size WHERE MasterSKU = ? and SizeID = ?;";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si",$currID, $sizeID);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows == 1){
            $row = $result->fetch_assoc();
            $price = $row["Price"];
            $percentage = $row["PricePercentage"];
            $message = $price + $price*$percentage/100;
        }
        $stmt->close();
        $conn->close();
    }
    else if(isset($_GET["pump"])){
        $pump = $_GET["pump"];
        if($conn->error){
            die("Connection Error: ". mysqli_error);
        }
        $query = "SELECT Price FROM Product_Item WHERE MasterSKU = ?;";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s",$pump);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows == 1){
            $row = $result->fetch_assoc();
            $price = $row["Price"];
            $message = $price;
        }
        $stmt->close();
        $conn->close();
    }
    else{
        $message = "Fail";
    }
    echo $message;
?>
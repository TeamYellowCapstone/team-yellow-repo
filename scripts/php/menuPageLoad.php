<?php
    //this file is used to initialize the session and cookie for all web pages
    require "templates/sessions_and_cookies.php";
    require_once "connection/connection.php";

    if($conn->error){
        die("Connection Error". $conn->mysqli_error);
    } 
    $cookie = $_COOKIE["sessID"];
    $query = "SELECT ProductName, SizeName, Quantity, Price, PricePercentage
                FROM Cart INNER JOIN
                Product_Item ON Cart.ItemID = Product_Item.ItemID
                INNER JOIN
                Product_Size ON Cart.ItemID = Product_Size.SizeID
                WHERE SessionID = ?;";
    $result = runQuery($query, $cookie, "s", $conn);
    //not in use currently
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            $arrlength = count($_SESSION["cart"]);
            $_SESSION["cart"][$arrlength] = array("name"=>$row["ProductName"],"size"=>$row["SizeName"], 
            "price"=>$row["Price"],"percentage"=>$row["PricePercentage"]);
        }
    }

    //get total qty in the cart
    $query = "SELECT SUM(Quantity) AS Qty FROM Cart WHERE SessionID = ?;";
    $result = runQuery($query, $cookie, "s", $conn);
    if($result->num_rows > 0){
        $row = $result->fetch_assoc();
        setcookie("cartQty", $row["Qty"], strtotime("+30 days"),"/");
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
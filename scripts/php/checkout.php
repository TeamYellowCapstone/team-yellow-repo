<?php
    require "../../templates/sessions_and_cookies.php";
    require_once "../../connection/connection.php";


    if($conn->error){
        die("error:" . mysqli_error);
    }
    $message = "";

    $cookie = $_COOKIE["sessID"];
    //if the GET request comes with the current time value called log process the following
    if(isset($_GET["log"])){
        $time = $_GET["log"];
        $id; //used to store current receipt number
        //first insert the identifier(session id) to the checkout table with timestamp
        $query_insert = "INSERT INTO Checkout (SessionID, TimeStamp) VALUES (?,?);";
        $stmt = $conn->prepare($query_insert);
        $stmt->bind_param("ss",$cookie,$time);
        //if excuted correctly get the id(receipt number) of the checkout 
        if($stmt->execute()){
            $id = $conn->insert_id;
            //unset every session and generate new identifier, and set the cart session to empty
            session_unset();
            session_regenerate_id();
            if(isset($_SESSION["cart"])){
                $_SESSION["cart"] = array();
            }
            //set the user identifier to the new session and set cartqty to 0
            setcookie("cartQty",0,time() -3600,"/");
            setcookie("sessID",0,time() -3600,"/");
            setcookie("cartQty",0,strtotime("+30 days"),"/");
            setcookie("sessID",session_id(),strtotime("+30 days"),"/");
           
        }
        else{
            $message = "Error";
            $conn->close();
            echo $message;
            return;
        }
        $stmt->close();

        //after adding user id(session id) to checkout history get the items detail to display
        //use inner join to get both price and size
        $query = "SELECT ProductName, SizeName, Price, PricePercentage, Quantity
                FROM Cart
                INNER JOIN Product_Item ON
                Cart.ItemID = Product_Item.ItemID
                INNER JOIN Product_Size ON
                Cart.SizeID = Product_Size.SizeID
                WHERE Cart.SessionID = ?;";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s",$cookie);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        //create at table in a loop to display the receipt
        $message = '<p class="leftText"> <b>Receipt</b>: '.$id.'&emsp;&emsp;<b>Time</b>: '.$time.'</p>'
                    .'<p class="centerText" id="chk-detail">Checkout Detail</p>'
                    .'<table class="tbl"> 
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Size</th>
                                <th>Price</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>
                        <tbody>';

        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                $price = $row["Price"] + $row["Price"] * $row["PricePercentage"] / 100;
                $message = $message . "<tr>"
                                        ."<td>".$row["ProductName"] . "</td>"
                                        ."<td>".$row["SizeName"]."</td>"
                                        ."<td>".$price."</td>"
                                        ."<td>".$row["Quantity"]."</td>"
                ."</tr>";
            }
            $message = $message . "</tbody></table>";
        }
        else{
            $message = "Error";
        }
        
        

    }
    $conn->close();
    echo $message;


?>
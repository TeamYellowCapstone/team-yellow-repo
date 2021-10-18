<?php
    require "../../templates/sessions_and_cookies.php";
    require_once "../../connection/connection.php";


    if($conn->error){
        die("error:" . mysqli_error);
    }
    $message = "";

    $user = isset($_SESSION["UserID"]) ? $_SESSION["UserID"] : 0;
    if($user != 0){
        //if the GET request comes with the current time value called log process the following
        if(isset($_GET["log"])){
            $time = $_GET["log"];

            $cart_query = "SELECT * FROM Cart WHERE UserID = ?;";
            $cart_stmt = $conn->prepare($cart_query);
            $cart_stmt->bind_param("i",$user);
            $cart_stmt->execute();
            $result = $cart_stmt->get_result();
            if($result->num_rows > 0){
                $cart_stmt->close();
                $id; //used to store current receipt number
                //first insert the identifier(user id) to the checkout table with timestamp
                $query_insert = "INSERT INTO Checkout (UserID, TimeStamp) VALUES (?,?);";
                $stmt = $conn->prepare($query_insert);
                $stmt->bind_param("is",$user,$time);
                //if excuted correctly get the id(receipt number) of the checkout 
                if($stmt->execute()){
                    $id = $conn->insert_id;
                    //add detail to checkout detail table
                    $query_insert = "INSERT INTO Checkout_Detail (ItemID, SizeID, Quantity, CheckoutID)
                    SELECT ItemID, SizeID, Quantity, CheckoutID FROM Cart, Checkout WHERE Cart.UserID = ? AND Checkout.CheckoutID = ?;";
                    $add_stmt = $conn->prepare($query_insert);
                    $add_stmt->bind_param("ii",$user,$id);
                    $add_stmt->execute();
                    $add_stmt->close();
                    //delete cart from db
                    $delete_query = "DELETE FROM Cart WHERE UserID = ?;";
                    $delete_stmt = $conn->prepare($delete_query);
                    $delete_stmt->bind_param("i",$user);
                    $delete_stmt->execute();
                    //unset every session and generate new identifier, and set the cart session to empty
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
                        FROM Checkout
                        INNER JOIN Checkout_Detail ON
                        Checkout.CheckoutID = Checkout_Detail.CheckoutID
                        INNER JOIN Product_Item ON
                        Checkout_Detail.ItemID = Product_Item.ItemID
                        INNER JOIN Product_Size ON
                        Checkout_Detail.SizeID = Product_Size.SizeID
                        WHERE Checkout.UserID = ? AND Checkout.TimeStamp = ?;";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("is",$user,$time);
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
            else{
                $cart_stmt->close();
                $message = "Cart is Empty!";
            }
            
        }
    }
    else{
        $message = "Please Log in First";
    }
    $conn->close();
    echo $message;
// use ebdb;
// SELECT ProductName, Price + (Price * PricePercentage/100) AS CurrPrice, SizeName, Quantity, (( Price + (Price * PricePercentage/100)) * Quantity) AS Subtotal, SUM(( Price + (Price * PricePercentage/100)) * Quantity) AS Total FROM Cart 
// INNER JOIN Product_Item ON
// Product_Item.ItemID = Cart.ItemID
// INNER JOIN Product_Size ON
// Product_Size.SizeID = Cart.SizeID
// WHERE UserID = 52;

?>



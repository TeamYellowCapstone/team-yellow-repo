<?php
    // require "menuPageLoad.php";
    require "../../templates/sessions_and_cookies.php";
    require "../../connection/connection.php";


    if($conn->error){
        die("error:");
    }
    $message = "";

    $user = isset($_SESSION["UserID"]) ? $_SESSION["UserID"] : 0;
    if($user != 0){
        //if the GET request comes with the current time value called log process the following
        if(isset($_GET["log"])){
            $time = $_GET["log"];

            $cart_query = "SELECT CartView.ID, CartView.ProductName, CartView.Price, PricePercentage, SizeName, CartView.Quantity, OptionTotalPrice FROM CartView 
        INNER JOIN OptionPriceView ON CartView.ID = OptionPriceView.ID
        WHERE CartView.UserID = ?;";
        $cart_stmt = $conn->prepare($cart_query);
        $cart_stmt->bind_param("i",$user);
        $cart_stmt->execute();
        $id; //used to store current receipt number
        $result = $cart_stmt->get_result();
            if($result->num_rows > 0){
                $cart_stmt->close();
                
                //first insert the identifier(user id) to the checkout table with timestamp
                $query_insert = "INSERT INTO Checkout (UserID, TimeStamp) VALUES (?,?);";
                $stmt = $conn->prepare($query_insert);
                $stmt->bind_param("is",$user,$time);
                //if excuted correctly get the id(receipt number) of the checkout 
                if($stmt->execute()){
                    $id = $conn->insert_id;
                    //add detail to checkout detail table
                    $query_insert = "INSERT INTO Checkout_Detail (MasterSKU, SizeID, Quantity, CheckoutID)
                    SELECT MasterSKU, SizeID, Quantity, CheckoutID FROM Cart, Checkout WHERE Cart.UserID = ? AND Checkout.CheckoutID = ?;";
                    $add_stmt = $conn->prepare($query_insert);
                    $add_stmt->bind_param("ii",$user,$id);
                    $add_stmt->execute();
                    $chekoutId = $conn->insert_id;
                    $add_stmt->close();
                    //add option details to option detail table
                    $query_insert = "INSERT INTO Checkout_Options (OptionMasterSKU, Quantity, CheckoutDetailID)
                    SELECT OptionMasterSKU, Quantity, ? FROM CartOptionView WHERE CartOptionView.UserID = ?;";
                    $add_stmt = $conn->prepare($query_insert);
                    $add_stmt->bind_param("ii",$chekoutId,$user);
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
                    if(isset($_SESSION["cartQty"])){
                        $_SESSION["cartQty"] = 0;
                    }
                    //set the user identifier to the new session and set cartqty to 0
                
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
                // $query = "SELECT ProductName, SizeName, Price, PricePercentage, Quantity
                //         FROM Checkout
                //         INNER JOIN Checkout_Detail ON
                //         Checkout.CheckoutID = Checkout_Detail.CheckoutID
                //         INNER JOIN Product_Item ON
                //         Checkout_Detail.MasterSKU = Product_Item.MasterSKU
                //         INNER JOIN Product_Size ON
                //         Checkout_Detail.SizeID = Product_Size.SizeID
                //         WHERE Checkout.UserID = ? AND Checkout.TimeStamp = ?;";
                // $stmt = $conn->prepare($query);
                // $stmt->bind_param("is",$user,$time);
                // $stmt->execute();
                // $result = $stmt->get_result();
                // $stmt->close();

                $query = "SELECT CheckoutView.ID, CheckoutView.ProductName, CheckoutView.Price, PricePercentage, SizeName, CheckoutView.Quantity, OptionTotalPrice FROM CheckoutView 
                INNER JOIN CheckoutOptionPriceView ON CheckoutView.ID = CheckoutOptionPriceView.ID
                WHERE CheckoutView.CheckoutID = ?;";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i",$id);
                $stmt->execute();
                $result = $stmt->get_result();
                $stmt->close();
                //create a table in a loop to display the receipt
                $cartQty = 0;
                $cartTotal = 0;
                $message = "'<p class='leftText'> <b>Receipt</b>: ".$id."&emsp;&emsp;<b>Time</b>: ".$time."</p>"
                ."<p class='centerText' id='chk-detail'>Checkout Detail</p>"
                ."<table class='tbl'>
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Size</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>";
                    $row = 0;

                if($result->num_rows > 0){
                    foreach($result as $item){
                        $message = $message. "<tr>
                                <td>".$item["ProductName"]."</td>
                                <td>".$item["SizeName"]."</td>
                                <td>".$item["Price"] + ($item["Price"]*$item["PricePercentage"]/100)."</td>
                                <td>".$item["Quantity"]."</td>
                                <td>".$item["OptionTotalPrice"] + ($item["Price"] + ($item["Price"]*$item["PricePercentage"]/100)) * $item["Quantity"]."</td>
                            <tr>";
                            $cartQty += $item["Quantity"];
                            $cartTotal += $item["OptionTotalPrice"] + ($item["Price"] + ($item["Price"]*$item["PricePercentage"]/100)) * $item["Quantity"];
                    }
                    $message = $message. "<tr>
                            <td colspan='3'></td>
                            <td>".$cartQty."</td>
                            <td>".$cartTotal."</td>
                            </tbody></table>";
                    if($_SESSION["role"] == 3){
                        session_unset();
                        session_destroy();
                    }
                    
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
        header("Location: ../../signin.php");
        return;
    }
    $conn->close();
    echo $message;

?>



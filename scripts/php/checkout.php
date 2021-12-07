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

            $cart_query = "SELECT * FROM CartView WHERE CartView.UserID = ?;";
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
                //if executed correctly get the id(receipt number) of the checkout 
                if($stmt->execute()){
                    $id = $conn->insert_id;
                    //add detail to checkout detail table
                    $items_to_insert_query = "SELECT * FROM Cart WHERE UserID = ?";
                    $items_to_insert_stmt = $conn->prepare($items_to_insert_query);
                    $items_to_insert_stmt->bind_param("i",$user);
                    $items_to_insert_stmt->execute();
                    $items_to_insert_result = $items_to_insert_stmt->get_result();
                    $items_to_insert_stmt->close();
                    foreach ($items_to_insert_result as $item) {
                        $query_insert = "INSERT INTO Checkout_Detail (MasterSKU, SizeID, Quantity, CheckoutID)
                        VALUES (?, ?, ?, ?)";
                        $add_stmt = $conn->prepare($query_insert);
                        $sku = $item["MasterSKU"];
                        $sizeid = $item["SizeID"];
                        $qty = $item["Quantity"];
                        $add_stmt->bind_param("siii",$sku,$sizeid,$qty,$id);
                        $add_stmt->execute();
                        $detailId = $conn->insert_id;
                        $add_stmt->close();
                        
                        //add option details to option detail table
                        $options_to_insert_query = "SELECT * FROM Cart_Options WHERE CartID = ?";
                        $options_to_insert_stmt = $conn->prepare($options_to_insert_query);
                        $cartid = $item["CartID"];
                        $options_to_insert_stmt->bind_param("i",$cartid);
                        $options_to_insert_stmt->execute();
                        $options_to_insert_result = $options_to_insert_stmt->get_result();
                        $options_to_insert_stmt->close();
                        foreach ($options_to_insert_result as $option) {
                            $query_insert = "INSERT INTO Checkout_Options (OptionMasterSKU, Quantity, CheckoutDetailID)
                            VALUES(?, ?, ?)";
                            $add_stmt = $conn->prepare($query_insert);
                            $optionsku = $option["OptionMasterSKU"];
                            $optionqty = $option["Quantity"];
                            $add_stmt->bind_param("sii",$optionsku,$optionqty,$detailId);
                            $add_stmt->execute();
                            $add_stmt->close();
                        }
                        
                    }

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
                    
                    //after adding user id(session id) to checkout history get the items detail to display

                    $item_query = "SELECT * FROM CheckoutView WHERE CheckoutID = ?;";
                    $item_stmt = $conn->prepare($item_query);
                    $item_stmt->bind_param("i",$id);
                    $item_stmt->execute();
                    $item_result = $item_stmt->get_result();
                    $item_stmt->close();

                    $option_query = "SELECT * FROM CheckoutOptionView WHERE UserID = ?;";
                    $opt_stmt = $conn->prepare($option_query);
                    $opt_stmt->bind_param("i",$user);
                    $opt_stmt->execute();
                    $option_result = $opt_stmt->get_result();
                    $opt_stmt->close();
                    //create a table in a loop to display the receipt
                    $cartQty = 0;
                    $cartTotal = 0;
                    $message = "'<p class='leftText'> <b>Receipt</b>: ".$id."&emsp;&emsp;<b>Time</b>: ".$time."</p>"
                    ."<p class='centerText' id='chk-detail'>Checkout Detail</p>"
                    ."<table class='tbl'>"
                    ."<tbody>";
                    $row = 0;
                    foreach($item_result as $item){
                        $message .= "<tr>
                                <td colspan=2>".$item["Quantity"]." X ".$item["SizeName"]." ".$item["ProductName"]." (".$item["Price"]."/item)";
                                $optPrice = 0;
                                
                                $option = array();
                                foreach($option_result as $item_option){
                                    if($item_option["ID"] == $item["ID"]){
                                        array_push($option, array("ProductName" => $item_option["ProductName"],"Catagory" => 
                                        $item_option["Catagory"], "pump" => $item_option["Quantity"]));
                                    }
        
                                }
                                $e =0; $s=0; $c=0; $p=0;
                                $espersso = "Add Ons ($ 1.5/shot): ";
                                $syrup = "Syrup ($ 0.25/pump): ";
                                $sweetener = "Sweetener: ";
                                $creamer = "Creamer: ";
                                foreach ($option as $key) {
                                    switch ($key["Catagory"]){
                                        case "AddOns":
                                            $espersso .= $key["pump"] . " X " . $key["ProductName"]. " ";
                                            $optPrice += 1.5 * $key["pump"];
                                            $e++;
                                            break;
                                        case "Syrup":
                                            $syrup .= $key["pump"] . " X " . $key["ProductName"]. " ";
                                            $optPrice += 0.25 * $key["pump"];
                                            $p++;
                                            break;
                                        case "Sweetener":
                                            $sweetener .= $key["pump"] . " X " . $key["ProductName"]. " ";
                                            $s++;
                                            break;
                                        case "Creamer":
                                            $c++;
                                            $creamer .= $key["pump"] . " X " . $key["ProductName"]. " ";
                                            break;
                                    }
                                }
        
                                if($e != 0){
                                    $message .= "<br>&emsp;&emsp;" .$espersso;
                                }
                                if($p != 0){
                                    $message .= "<br>&emsp;&emsp;" .$syrup;
                                }
                                if($s != 0){
                                    $message .= "<br>&emsp;&emsp;" .$sweetener;
                                }
                                if($c != 0){
                                    $message .= "<br>&emsp;&emsp;" .$creamer;
                                }
                                $message .= "</td><td>".$item["Quantity"]*$item["Price"] + $optPrice."</td></tr>";
                            $cartQty += $item["Quantity"];
                            $cartTotal += $item["Quantity"]*$item["Price"] + $optPrice;
                    }
                    $message .= "<tr>
                            <td></td>
                            <td>Total Qty ".$cartQty."</td>
                            <td>Sub Total $".$cartTotal."</td>
                        </tr>
                        </tbody>
                        </table>";
                    
                }
                else{
                    $message = "Error";
                    $conn->close();
                    echo $message;
                    return;
                }
                $stmt->close();
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
    if($_SESSION["role"] == 3){
        unset($_SESSION["FirstName"]);
        unset($_SESSION["UserID"]);
        unset($_SESSION["UserName"]);
    }
    $conn->close();
    echo $message;

?>



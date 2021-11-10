<?php
    //this is needed to initiate cookie and session for all pages
    require "../../templates/sessions_and_cookies.php";
    //use this inorder to avoid uploading to github accidenetaly
    $_SESSION["itemAdded"] = "Item has been added";
    if($_SERVER["REQUEST_METHOD"] == "GET"){
        if(!isset($_GET["itemid"])){
            header("Location: ../../menu.php");
            return;
        }
        else{
            require_once "../../connection/connection.php";
            if($conn->error){
                die("Connectio Error");
            }
            
            
            $message = "";
            //if the request comes with item id and item size id process it
            if(isset($_GET["itemid"]) && isset($_GET["size"])){
                
                //get item ID, SizeID, and UserID
                $currID = $_GET["itemid"];
                $currSize = $_GET["size"];
                $creamer_option;
                if(isset($_GET["creamers"])){
                    
                    $creamer_option = $_GET["creamers"];
                }
                else{
                    $creamer_option = array();
                }
                $user = isset($_SESSION["UserID"]) ? $_SESSION["UserID"] : 0;
        
                //if logged in
                if($user !=0){
                    //before adding item to cart check if there is the same item with same size
                    $get_cart_id = "SELECT CartID  FROM Cart WHERE UserID = ? AND MasterSKU = ? AND SizeID = ?;";
                    $get_options = "SELECT OptionMasterSKU FROM Cart_Options INNER JOIN Cart ON
                    Cart_Options.CartID = Cart.CartID
                    WHERE Cart.CartID = ?;";
                    
        
                    $stmt = $conn->prepare($get_cart_id);
                    $stmt->bind_param("isi",$user,$currID,$currSize);
                    $stmt->execute();
                    $cart_id_result = $stmt->get_result();
                    $stmt->close();
                    //if the item exists
                    $match_found = FALSE;
                    if($cart_id_result->num_rows > 0){
                        while($row = $cart_id_result->fetch_assoc()){
                            $get_option_stmt = $conn->prepare($get_options);
                            $get_option_stmt->bind_param("i",$row["CartID"]);
                            $get_option_stmt->execute();
                            $get_option_result= $get_option_stmt->get_result();
                            $get_option_stmt->close();
                            $get_array_result = $get_option_result->fetch_all();
                            $options = array();
                            //for each creamer options
                            for($i = 0; $i<count($get_array_result);$i++){
                                array_push($options,$get_array_result[$i][0]);
                            }
                            $options = $options[0] == null ? array() : $options;
                            sort($options);

                            sort($creamer_option);
                            if($options === $creamer_option){
                                //from the above request if item, size and option exists in the database update the qty
                                $query_update = "UPDATE Cart SET Quantity = Quantity + 1 WHERE CartID = ?;";
                                $stmt = $conn->prepare($query_update);
                                $stmt->bind_param("i",$row["CartID"]);
                                $stmt->execute();
                                $stmt->close();
                                $_SESSION["cartQty"] += 1;//update cookie to display qty
                                $_SESSION["itemAdded"] = "The quantity of the current item has been updated";
                                header("Location: ../../details.php?itemid=".$currID);
                                return;
                            }
                        }
                        if(!$match_found){
                            addItem($conn,$user,$currID,$currSize,$creamer_option);
                            header("Location: ../../details.php?itemid=".$currID);
                            return;
                        }
                    }                    
                    //if item doesn't exist in the db add the item to the db
                    else if($cart_id_result->num_rows == 0){
                        addItem($conn,$user,$currID,$currSize,$creamer_option);
                        header("Location: ../../details.php?itemid=".$currID);
                        return;
                    }
                    else{
                        $message = "Error Adding Item";
                        header("Location: ../../details.php?itemid=".$currID);
                        return;
                    }
                }
                else{
                    sort($creamer_option);
                    $get_product_query = "SELECT * FROM ProductItemsView WHERE MasterSKU = ? AND SizeID = ?";
                    $get_product_stmt = $conn->prepare($get_product_query);
                    $get_product_stmt->bind_param("si",$currID, $currSize);
                    $get_product_stmt->execute();
                    $get_product_result = $get_product_stmt->get_result();
                    $get_product_stmt->close();
                    if($get_product_result->num_rows > 0){
                        $row_item = $get_product_result->fetch_assoc();
                        $sizeid = $currID.",".$currSize;
                        //cart=>{"idsize"=>{id,size,productname, sizename,qty,price,option=>{}},""=>{}}
                        $found = FALSE;
                        $matched = FALSE;
                        foreach($_SESSION["cart"] as $item_key => $item_value){
                            $key = array_keys($item_value)[0];
                            if($key == $sizeid){
                                $found =TRUE;
                                foreach ($item_value as $item) {
                                    $currentOption = $item["option"];
                                    sort($currentOption);
                                    //same item with same option exists update so the old
                                    if($currentOption == $creamer_option){
                                        $c = $_SESSION["cart"][$item_key];
                                        $b = $_SESSION["cart"][$item_key][$key];
                                        $a=$_SESSION["cart"][$item_key][$key]["qty"];

                                        $_SESSION["cart"][$item_key][$key]["qty"] += 1; 
                                        $_SESSION["itemAdded"] = "The quantity of the current item has been updated";
                                        $_SESSION["cartQty"] += 1;//update cookie to display qty
                                        $matched = TRUE;
                                        break;
                                    }
                                }
                            }
                        }
                            
                        //same item with different option exists so add new one
                        if($found && !$matched){
                            array_push($_SESSION["cart"],array($currID.",".$currSize=>array("id"=>$currID,"size"=>$currSize,
                        "ProductName"=>$row_item["ProductName"],"SizeName"=>$row_item["SizeName"],"qty"=>1,
                        "Price"=>$row_item["Price"],"PricePercentage"=>$row_item["PricePercentage"],"option"=>$creamer_option)));
                            $_SESSION["itemAdded"] = "Item has been added to cart.";
                            $_SESSION["cartQty"] += 1;//update cookie to display qty
                        }
                        
                        //item doesnot exists in cart so add new one
                        if(!$found && !$matched){
                            array_push($_SESSION["cart"],array($currID.",".$currSize=>array("id"=>$currID,"size"=>$currSize,
                            "ProductName"=>$row_item["ProductName"],"SizeName"=>$row_item["SizeName"],"qty"=>1,
                            "Price"=>$row_item["Price"],"PricePercentage"=>$row_item["PricePercentage"],"option"=>$creamer_option)));
                            $_SESSION["itemAdded"] = "Item has been added to cart.";
                            $_SESSION["cartQty"] += 1;//update cookie to display qty
                        }
                        header("Location: ../../details.php?itemid=".$currID);
                        return;
                    }
                    else{
                        $message = "Error Adding Item";
                        header("Location: ../../details.php?itemid=".$currID);
                        return;
                    }
                }
                
            }
            else{
                $_SESSION["itemAdded"] = "Error adding item to cart.";
            }
            $conn->close();
            echo $message;
        }
        
    }
    else{
        header("Location: ../../menu.php");
        return;
    }
    function addItem($conn,$user,$currID,$currSize,$creamer_option){
        $query_insert_item = "INSERT INTO Cart (UserID, MasterSKU, SizeID, Quantity) VALUES (?,?,?,?);";
        $stmt = $conn->prepare($query_insert_item);
        $qty = 1;
        $stmt->bind_param("isii",$user,$currID,$currSize,$qty);
        $stmt->execute();
        $lastid = $stmt->insert_id;
        $stmt->close();
        $_SESSION["cartQty"] += 1;//update cookie to display qty
        $_SESSION["itemAdded"] = "Item has been added to cart.";
        if(count($creamer_option) != 0){
            $query_insert_option = "INSERT INTO Cart_Options (OptionMasterSKU, CartID) VALUES (?,?);";
            foreach($creamer_option as $creamer){
                $stmt = $conn->prepare($query_insert_option);
                $stmt->bind_param("si",$creamer,$lastid);
                $stmt->execute();
                $stmt->close();
            }
        }
        else{
            $query_insert_option = "INSERT INTO Cart_Options (CartID) VALUES (?);";
            $stmt = $conn->prepare($query_insert_option);
            $stmt->bind_param("i",$lastid);
            $stmt->execute();
            $stmt->close();
        }
    }

?>
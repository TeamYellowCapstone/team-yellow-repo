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
                $item_options = array();

                $option_name_query = "SELECT DISTINCT Catagory FROM Product_Item WHERE Department = ?";
                $option_name_stmt = $conn->prepare($option_name_query);
                $dept = "Options";
                $option_name_stmt->bind_param("s", $dept);
                $option_name_stmt->execute();
                $option_name_result = $option_name_stmt->get_result();
                $option_name_stmt->close();
                $all_selected_options = array();
                $all_options = array();
                if($_GET["size"] != 4){
                    foreach($option_name_result as $result){
                        //if this option is set then add to option array
                        $catagory = strtolower($result["Catagory"]);
                        $price = $catagory == "syrup" ? 0.25 : 0;
                        
                        for($index = 0; $index < count($_GET[$catagory]); $index++){
                            if($_GET["pump-".$catagory][$index] != 0){
                                array_push($all_selected_options,array("mastersku" => $_GET[$catagory][$index], "pump" => $_GET["pump-".$catagory][$index], "price" => $price));
                            }
                            
                        }
                        
                    }
                }
                $user = isset($_SESSION["UserID"]) ? $_SESSION["UserID"] : 0;
        
                //if logged in
                if($user !=0){
                    //before adding item to cart check if there is the same item with same size
                    $get_cart_id = "SELECT CartID  FROM Cart WHERE UserID = ? AND MasterSKU = ? AND SizeID = ?;";
                    //fix quantity
                    $get_options = "SELECT OptionMasterSKU, Cart_Options.Quantity, Product_Item.Price FROM Cart_Options INNER JOIN Cart ON
                    Cart_Options.CartID = Cart.CartID
                    INNER JOIN Product_Item ON
                    Cart_Options.OptionMasterSKU = Product_Item.MasterSKU
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
                                array_push($options,array("mastersku" => $get_array_result[$i][0], "pump" => $get_array_result[$i][1], "price" => $get_array_result[$i][2]));
                            }
                            $options = count($options) == 0 ? array() : $options;
                            sort($options);
                            
                            sort($all_selected_options);
                            if($options === $all_selected_options){
                                //from the above request if item, size and option exists in the database update the qty
                                $match_found = TRUE;
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
                            addItem($conn,$user,$currID,$currSize,$all_selected_options);
                            header("Location: ../../details.php?itemid=".$currID);
                            return;
                        }
                    }                    
                    //if item doesn't exist in the db add the item to the db
                    else if($cart_id_result->num_rows == 0){
                        addItem($conn,$user,$currID,$currSize,$all_selected_options);
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
                    sort($all_selected_options);
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
                                    if($currentOption == $all_selected_options){
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
                            getItemNames($conn,$all_selected_options);
                            array_push($_SESSION["cart"],array($currID.",".$currSize=>array("id"=>$currID,"size"=>$currSize,
                        "ProductName"=>$row_item["ProductName"],"SizeName"=>$row_item["SizeName"],"qty"=>1,
                        "Price"=>$row_item["Price"],"PricePercentage"=>$row_item["PricePercentage"],"option"=>$all_selected_options)));
                            $_SESSION["itemAdded"] = "Item has been added to cart.";
                            $_SESSION["cartQty"] += 1;//update cookie to display qty
                        }
                        
                        //item doesnot exists in cart so add new one
                        if(!$found && !$matched){
                            getItemNames($conn,$all_selected_options);
                            array_push($_SESSION["cart"],array($currID.",".$currSize=>array("id"=>$currID,"size"=>$currSize,
                            "ProductName"=>$row_item["ProductName"],"SizeName"=>$row_item["SizeName"],"qty"=>1,
                            "Price"=>$row_item["Price"],"PricePercentage"=>$row_item["PricePercentage"],"option"=>$all_selected_options)));
                            $_SESSION["itemAdded"] = "Item has been added to cart.";
                            $_SESSION["cartQty"] += 1;//update cookie to display qty
                        }
                        //array_push($_SESSION["options"], array($item_options));
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
    function addItem($conn,$user,$currID,$currSize,$all_selected_options){
        $query_insert_item = "INSERT INTO Cart (UserID, MasterSKU, SizeID, Quantity) VALUES (?,?,?,?);";
        $stmt = $conn->prepare($query_insert_item);
        $qty = 1;
        $stmt->bind_param("isii",$user,$currID,$currSize,$qty);
        $stmt->execute();
        $lastid = $stmt->insert_id;
        $stmt->close();
        $_SESSION["cartQty"] += 1;//update cookie to display qty
        $_SESSION["itemAdded"] = "Item has been added to cart.";
        if(count($all_selected_options) != 0){
            $query_insert_option = "INSERT INTO Cart_Options (OptionMasterSKU, Quantity, CartID) VALUES (?,?,?);";
            foreach($all_selected_options as $creamer){
                $stmt = $conn->prepare($query_insert_option);
                $stmt->bind_param("sii",$creamer["mastersku"],$creamer["pump"],$lastid);
                $stmt->execute();
                $stmt->close();
            }
        }
        // else{
        //     $query_insert_option = "INSERT INTO Cart_Options (CartID) VALUES (?);";
        //     $stmt = $conn->prepare($query_insert_option);
        //     $stmt->bind_param("i",$lastid);
        //     $stmt->execute();
        //     $stmt->close();
        // }

    }

    function getItemNames($conn,&$all_selected_options){
        $query_select_item = "SELECT ProductName, Catagory FROM AllItems WHERE MasterSKU = ?;";
        $stmt = $conn->prepare($query_select_item);
        for ($index = 0; $index < count($all_selected_options); $index++){
            $sku = $all_selected_options[$index]["mastersku"];
            $stmt->bind_param("s",$sku);
            $stmt->execute();
            $result = $stmt->get_result();
            while($row = $result->fetch_assoc()){
                $all_selected_options[$index]["ProductName"] = $row["ProductName"];
                $all_selected_options[$index]["Catagory"] = $row["Catagory"];          
            }
            
        } 
        $stmt->close();
    }
?>
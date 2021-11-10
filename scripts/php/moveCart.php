<?php

    if(isset($_SESSION["cart"]) && isset($_SESSION["UserID"])){

        //also update qty
        if($_SESSION["UserID"] != 0){
            foreach($_SESSION["cart"] as $key => $item){
                $uniquekey = array_keys($item)[0];
                $query = "INSERT INTO Cart (UserID, MasterSKU, SizeID, Quantity) VALUES (?,?,?,?);";
                $cart_stmt = $conn->prepare($query);
                $userID = $_SESSION["UserID"];
                $itemid = $item[$uniquekey]["id"];
                $sizeid = intval($item[$uniquekey]["size"]);
                $qty = intval($item[$uniquekey]["qty"]);
                $cart_stmt->bind_param("isii",$userID,$itemid,$sizeid,$qty);
                if($cart_stmt->execute()){
                    $last_id = $cart_stmt->insert_id;
                    foreach($item[$uniquekey]["option"] as $option){
                        $option_query = "INSERT INTO Cart_Options (OptionMasterSKU, CartID) VALUES (?,?);";
                        $option_stmt = $conn->prepare($option_query);
                        $option_stmt->bind_param("si",$option,$last_id);
                        $option_stmt->execute();
                    }
                    
                }
                else{
                    //
                }

            }
            $_SESSION["cart"] = array();
        }
        
    }
?>
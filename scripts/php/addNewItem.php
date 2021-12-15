<?php
    //this is needed to initiate cookie and session for all pages
    require "../../templates/sessions_and_cookies.php";
    
    require "sanitizeAndValidate.php";

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(isset($_POST["search"])){
            if(isset($_POST["searchValue"])){
                $searchKey = $_POST["searchValue"];
                require_once "../../connection/connection.php";
                if($conn->connect_error){
                    die("Connection Error");
                }

                $search_query = "SELECT MasterSKU, ProductName, Description, Department, Catagory, IsMenuItem, Quantity FROM Product_Item WHERE MasterSKU = ? OR ProductName =? LIMIT 1;";
                $stmt = $conn->prepare($search_query);
                $stmt->bind_param("ss",$searchKey,$searchKey);
                $stmt->execute();
                $search_result = $stmt->get_result();
                if($search_result->num_rows > 0){
                    //if entry is found set its values to session for filling the input fields
                    while($row = $search_result->fetch_assoc()){
                        $_SESSION["sku"] = $row["MasterSKU"];
                        $_SESSION["oldSku"] = $row["MasterSKU"];
                        $_SESSION["pname"] = $row["ProductName"];
                        $_SESSION["desc"] = $row["Description"];
                        $_SESSION["dept"] = $row["Department"];
                        $_SESSION["category"] = $row["Catagory"];
                        $_SESSION["quantity"] = $row["Quantity"];
                        $_SESSION["is-menu"] = $row["IsMenuItem"]  == 1 ? true : false;
                    }
                    //get price values for the searched item
                    $size_query = "SELECT SizeID, Price FROM Product_Price WHERE MasterSKU = ?;";
                    $size_stmt = $conn->prepare($size_query);
                    $size_stmt->bind_param("s",$_SESSION["sku"],);
                    $size_stmt->execute();
                    $size_result = $size_stmt->get_result();
                    $sizeName = array("small", "medium", "large", "regular");
                    if($size_result->num_rows > 0){
                        while($row = $size_result->fetch_assoc()){
                            $_SESSION[$sizeName[$row["SizeID"] - 1]] = $row["Price"];
                        }
                    }

                }
                else{
                    $_SESSION["errorMsg"] = "No data found!";
                }

                $stmt->close();
                $conn->close();
            }
        }
        // if task is not searching do the following
        else{
            $sku; $pname; $desc; $qty; $dept; $category;
            //validate and assign input data
            validateSKU($sku,"sku");
            validateNameWithSpace($pname, "pname");
            validateString($desc, "desc");
            validateNameWithSpace($dept, "dept");
            validateNameWithSpace($category, "category");
            validatePositiveNumber($qty, "quantity");
            $medium; $small; $large; $regular;
            validatePrice($medium, "medium");
            validatePrice($small, "small");
            validatePrice($large, "large");
            validatePrice($regular, "regular");
            //if inputs are all valid
            if(validateSKU($sku,"sku") && validateNameWithSpace($pname, "pname")
            && validateString($desc, "desc") && validateNameWithSpace($category, "category")
            && validateNameWithSpace($dept, "dept") && validatePositiveNumber($qty, "quantity")){
               
                if($_SESSION["err"] != "invalidPrice" && (validatePrice($medium, "medium") || validatePrice($small, "small")
                || validatePrice($large, "large") || validatePrice($regular, "regular"))){
                    require_once "../../connection/connection.php";
                    unset($_SESSION["errorMsg"]);
                    if($conn->connect_error){
                        die("Connection Error");
                    }
                    $is_menu = isset($_POST["ismenu"]) ? 1 : 0;
                    $_SESSION["is-menu"] = $is_menu;
                    $add_query = "INSERT INTO Product_Item (MasterSKU, ProductName, Description, Department, Catagory, IsMenuItem, Quantity) VALUES (?,?,?,?,?,?,?);";
                    $add_size_query = "INSERT INTO Product_Price (SizeID, MasterSKU, Price) VALUES (?, ?, ?);";
                    $update_query = "UPDATE Product_Item SET MasterSKU = ?, ProductName = ?, Description = ?, Department = ?, 
                                    Catagory = ?, IsMenuItem = ?, Quantity = ? WHERE MasterSKU = ?;";
                    $action = $_SESSION["action"];
                    $size = array($small, $medium, $large, $regular);
                    $stmt;
                    $success = "";
                    if(isset($_POST["add"])){
                        $success = "Item has been added successfully!";
                        $stmt = $conn->prepare($add_query);
                        $stmt->bind_param("sssssii",$sku, $pname, $desc, $dept, $category, $is_menu,$qty);
                    }
                    else{
                        $success = "Item has been updated successfully!";
                        $stmt = $conn->prepare($update_query);
                        $oldSKU = $_SESSION["oldSku"];
                        $stmt->bind_param("sssssiis",$sku, $pname, $desc, $dept, $category, $is_menu, $qty,$oldSKU);
                    }
                    if($stmt->execute()){
                        if(isset($_POST["add"])){
                            sizeAdd($conn, $add_size_query, $size, $sku);
                        }
                        else{
                            sizeUpdate($conn, $size, $sku);
                        }

                        $_SESSION["success"] = $success;
                        unset($_SESSION["sku"]);
                        unset($_SESSION["desc"]);
                        unset($_SESSION["pname"]);
                        unset($_SESSION["small"]);
                        unset($_SESSION["medium"]);
                        unset($_SESSION["large"]);
                        unset($_SESSION["regular"]);
                        unset($_SESSION["category"]);
                        unset($_SESSION["dept"]);
                        unset($_SESSION["quantity"]);
                        unset($_SESSION["is-menu"]);
                        
                    }
                    else{
                        if($conn->errno == 1062){
                            $err = preg_replace("/^[\s\S]*'Product_Item./","", $conn->error);
                            switch ($err) {
                                case "PRIMARY'":
                                    $_SESSION["errorMsg"] = "SKU already exists in the database.";
                                    break;
                                
                                case "Name_UNIQUE'":
                                    $_SESSION["errorMsg"] = "Product Name already exists in the database.";
                                    break;
                                default:
                                    $_SESSION["errorMsg"] = "Error while conecting to the server.";//"Connection error";
                            }
                        }
                    }
                    $stmt->close();
                    $conn->close();
                    }
                else{
                    if($_SESSION["err"] == "invalidPrice"){
                        $_SESSION["errorMsg"] = "Price can only be a decimal number e.g 12.25 or 12 or 12.2";
                    }
                    else{
                        $_SESSION["errorMsg"] = "Please set at least one price";
                    }
                }
            }
            else{
                switch ($_SESSION["err"]) {
                    case "sku":
                    case "pname":
                    case "desc":
                    case "quantity":
                        break;
                    case "skuCode":
                        $_SESSION["errorMsg"] = "SKU can only follow the pattern 00X XX, where 0 is for number and X is character.";
                        break;
                    case "invalidQuantity":
                        $_SESSION["errorMsg"] = "Quantity can only be a positive integer!";
                        break;
                }
                $_SESSION["succes"] = "fail";
            }
        }
        header("Location: ../../employee/maintenance.php");
        return;
    }

    //add item size price
    function sizeAdd($conn, $query, $size, $sku){
        $size_stmt = $conn->prepare($query);
        for($index = 0; $index < count($size); $index++){
            if($size[$index] != null){ 
                $itemSizeID = $index+1;
                $size_stmt->bind_param("isd",$itemSizeID,$sku,$size[$index]);
                $size_stmt->execute();
            }
        }
        $size_stmt->close();
    }
    //update item size price
    function sizeUpdate($conn, $size, $sku){
        $update = "UPDATE Product_Price SET Price = ? WHERE MasterSKU = ? AND SizeID = ?;";
        $insert = "INSERT INTO Product_Price (Price, MasterSKU, SizeID) VALUES (?, ?, ?);";
        $select_stmt = "SELECT * FROM Product_Price WHERE MasterSKU = ?;";
        $select_stmt = $conn->prepare($select_stmt);
        $select_stmt->bind_param("s", $sku);
        $select_stmt->execute();
        $select_result = $select_stmt->get_result();
        $select_stmt->close();

        $query = $insert;
        for($index = 0; $index < count($size); $index++){
            if($size[$index] != null){ 
                $itemSizeID = $index+1;
                foreach($select_result as $result){
                    if($result["SizeID"] == $itemSizeID){
                        $query = $update;
                        break;
                    }
                }
                $size_stmt = $conn->prepare($query);
                $size_stmt->bind_param("dsi",$size[$index],$sku,$itemSizeID);
                $size_stmt->execute();
                $size_stmt->close();
                $query = $insert;
            }
            else{
                $itemSizeID = $index+1;
                foreach($select_result as $result){
                    if($result["SizeID"] == $itemSizeID){
                        $delete = "DELETE FROM Product_Price WHERE SizeID = ? AND MasterSKU = ?;";
                        $delete_stmt = $conn->prepare($delete);
                        $delete_stmt->bind_param("is",$itemSizeID, $sku);
                        $delete_stmt->execute();
                        $delete_stmt->close();
                        break;
                    }
                }
            }
            
        }

    }
?>
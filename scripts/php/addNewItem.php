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

                $search_query = "SELECT MasterSKU, ProductName, Description, Price, Department, Catagory, IsMenuItem FROM Product_Item WHERE MasterSKU = ? OR ProductName =? LIMIT 1;";
                $stmt = $conn->prepare($search_query);
                $stmt->bind_param("ss",$searchKey,$searchKey);
                $stmt->execute();
                $search_result = $stmt->get_result();
                if($search_result->num_rows > 0){
                    while($row = $search_result->fetch_assoc()){
                        $_SESSION["sku"] = $row["MasterSKU"];
                        $_SESSION["oldSku"] = $row["MasterSKU"];
                        $_SESSION["pname"] = $row["ProductName"];
                        $_SESSION["desc"] = $row["Description"];
                        $_SESSION["price"] = $row["Price"];
                        $_SESSION["dept"] = $row["Department"];
                        $_SESSION["category"] = $row["Catagory"];
                        $_SESSION["is-menu"] = $row["IsMenuItem"]  == 1 ? true : false;
                    }

                }
                else{
                    $_SESSION["errMsg"] = "No data found!";
                }

                $stmt->close();
                $conn->close();
            }
        }
        else{
            $sku; $pname; $desc; $price;
            validateSKU($sku,"sku");
            validateNameWithSpace($pname, "pname");
            validateString($desc, "desc");
            validatePrice($price, "price");
            validateNameWithSpace($dept, "dept");
            validateNameWithSpace($category, "category");
            if(validateSKU($sku,"sku") && validateNameWithSpace($pname, "pname")
            && validateString($desc, "desc") && validatePrice($price, "price")
            && validateNameWithSpace($category, "category") && validateNameWithSpace($dept, "dept")){
                require_once "../../connection/connection.php";
                if($conn->connect_error){
                    die("Connection Error");
                }
                $is_menu = isset($_POST["ismenu"]) ? 1 : 0;
                $_SESSION["is-menu"] = $is_menu;
                $add_query = "INSERT INTO Product_Item (MasterSKU, ProductName, Description, Price, Department, Catagory, IsMenuItem) VALUES (?,?,?,?,?,?,?);";
                $update_query = "UPDATE Product_Item SET MasterSKU = ?, ProductName = ?, Description = ?, Price = ?, Department = ?, 
                                Catagory = ?, IsMenuItem = ? WHERE MasterSKU = ?;";
                $action = $_SESSION["action"];
                if(isset($_POST["add"])){
                    $stmt = $conn->prepare($add_query);
                    $stmt->bind_param("sssdssi",$sku, $pname, $desc, $price, $dept, $category, $is_menu);
                }
                else{
                    $stmt = $conn->prepare($update_query);
                    $oldSKU = $_SESSION["oldSku"];
                    $stmt->bind_param("sssdssis",$sku, $pname, $desc, $price, $dept, $category, $is_menu,$oldSKU);
                }
                if($stmt->execute()){
                    $_SESSION["success"] = "Item has been added successfully!";
                    unset($_SESSION["sku"]);
                    unset($_SESSION["desc"]);
                    unset($_SESSION["pname"]);
                    unset($_SESSION["price"]);
                    unset($_SESSION["category"]);
                    unset($_SESSION["dept"]);
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
                            $_SESSION["errorMsg"] = $err. "  ".$conn->errno;//"Connection error";
                        }
                    }
                }
                $stmt->close();
                $conn->close();

            }
            else{
                switch ($_SESSION["err"]) {
                    case "sku":
                    case "pname":
                    case "desc":
                    case "price":
                        break;
                    case "skuCode":
                        $_SESSION["errorMsg"] = "SKU can only follow the pattern 00X XX, where 0 is for number and X is character.";
                        break;
                    case "invalidPrice":
                        $_SESSION["errorMsg"] = "Price can only be a decimal number e.g 12.25 or 12 or 12.2";
                        break;
                }
                $_SESSION["succes"] = "fail";
            }
        }
        header("Location: ../../employee/maintenance.php");
        return;
    }
?>
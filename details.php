<?php
    require "scripts/php/menuPageLoad.php";
    $imgSrc = "";
    $msg = "";

    if(isset($_GET["itemid"])){
        $itemid = $_GET["itemid"];
        if(!preg_match("/^\d\d[a-zA-Z]\s[a-zA-Z]{2}$/", $itemid)){
            header ("Location: menu.php");
            return;
        }
        else{
            require "connection/connection.php";
            //check if item is menu item and if it is not kick user to menu page
            $detail_query = "SELECT ProductName, Description, ImgID, Department, Catagory, IsMenuItem, Type, Quantity FROM MenuItem WHERE MasterSKU = ? LIMIT 1;";
            $detail_stmt = $conn->prepare($detail_query);
            $detail_stmt->bind_param("s", $itemid);
            $detail_stmt->execute();
            $detail_result = $detail_stmt->get_result();
            
            $detail_stmt->close();
            $row;
            if($detail_result->num_rows > 0){
                $row = $detail_result->fetch_assoc();
                if($row["IsMenuItem"] != 1){
                    header ("Location: menu.php");
                    return;
                }
                if($row["Quantity"] == 0){
                    header ("Location: menu.php");
                    return;
                }

                //get Option names
                $option_name_query = "SELECT * FROM OptionPrice;";
                $option_name_stmt = $conn->prepare($option_name_query);
                $option_name_stmt->execute();
                $option_name_result = $option_name_stmt->get_result();
                $option_price = $option_name_result->fetch_all(MYSQLI_ASSOC);
                $option_name_stmt->close();


                //get the actual options available for each option catagory
                $option_result = array();
                if($row["Type"] === "Coffee"){
                    foreach($option_name_result as $product_option){
                        $option_query = "SELECT ProductName, MasterSKU, MaxAllowed, Quantity FROM Product_Item WHERE Department = ? AND Catagory = ?;";
                        $option_stmt = $conn->prepare($option_query);
                        $dept = "Options";
                        $cat = $product_option["Catagory"];
                        $option_stmt->bind_param("ss", $dept, $cat);
                        $option_stmt->execute();
                        $r = $option_stmt->get_result();
                        $option_result[$product_option["Catagory"]] = $r->fetch_all(MYSQLI_ASSOC);
                        $option_stmt->close();
                    }
                    
                }
                //Get Product Size
                $size_query = "SELECT * FROM PriceView WHERE MasterSKU = ?;";
                $size_stmt = $conn->prepare($size_query);
                $size_stmt->bind_param("s",$itemid);
                $size_stmt->execute();
                $product_size = $size_stmt->get_result();
                $size_stmt->close();
                //$product_size = $size_result->fetch_all(MYSQLI_ASSOC);

                //get Image
                $image_query = "SELECT * FROM ImageView WHERE ImgID = ?;";
                $image_stmt = $conn->prepare($image_query);
                $imgID = $row["ImgID"];
                $image_stmt->bind_param("i", $imgID);
                $image_stmt->execute();
                $image_result = $image_stmt->get_result();
                $image_stmt->close();
                if($image_result->num_rows > 0){
                    $img_row = $image_result->fetch_assoc();
                    $imgSrc =  "images"."/".$img_row["ImgLocation"] ."/". $img_row["ImgName"];
                }
                else{
                    $imgSrc = "images/uploads/placeHolder.png";
                }
            }
            else{
                header ("Location: menu.php");
                return;
            }
        }
    }
    else{
        header ("Location: menu.php");
        return;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php
        require "templates/head.php";
    ?>
    <link rel="stylesheet" href="styles/detail.css">
    <script type="text/javascript" src="scripts/add-to-cart.js" defer></script>
    <script type="text/javascript" src="scripts/detail.js" defer></script>
    <title>Detail</title>

</head>

  <body>

    <?php
      require "templates/navigation.php";
    ?>
    <div class="centerText">
        <?php
        
            if(isset($_SESSION["itemAdded"])){
                echo "<p>".$_SESSION["itemAdded"]."</p>";
                unset($_SESSION["itemAdded"]);
            }
            echo "<h1 id='".$itemid."'>".$row["ProductName"]."</h1>";
            echo "<img src='" .$imgSrc. "' width='300'> ";

            echo "<p id='desc'>".$row["Description"]."</p>";
            echo "<form action='scripts/php/addToCart.php' method='GET' class='form add-to-cart-form'>";
            ?>
            <?php
            //$option_result = ["Creamer"=>["soy","2%"], "Sweetener"=>["sugar","honey]]
            foreach ($option_result as $key => $value) {
                if(($row["Catagory"] != "Iced" && $row["Catagory"] != "Hot") && $key == "AddOns"){
                    continue;
                }
                echo "<div class='option-container'>";
                //$price_per_pump = $option_price[array_keys($option_price,$key)[0]]["Price"] == 0 ? "" : "(";
                echo "<h3 class='collapsible-option option-name'>".$key." <span class='count'></span><span class='arrow-head'></span></h3>";
                echo "<div class='creamer-options'>";
                foreach ($value as $option) {
                    if($option["Quantity"] > 0){
                        echo "<input type='hidden' class='option' name = '".strtolower($key)."[]' value = '".$option["MasterSKU"]."' id = '".$option["ProductName"]."'>";
                        echo "<input type='hidden' class='pump' name = 'pump-".strtolower($key)."[]' value = 0 min = 0 max=".min($option["Quantity"],$option["MaxAllowed"])." id = 'pump-".$option["ProductName"]."'>";
                        echo "<label for='".$option["ProductName"]."' class='option-item'><span class='remove opt-btn'></span><span id='".$option["ProductName"]."-count'></span>".$option["ProductName"]."<span class='add opt-btn'></span></label>";
                    }
                    else{
                        echo "<input type='hidden' class='option' name = '".strtolower($key)."[]' value = '".$option["MasterSKU"]."' id = '".$option["ProductName"]."'>";
                        echo "<input type='hidden' class='pump' name = 'pump-".strtolower($key)."[]' value = 0 min = 0 max=0 id = 'pump-".$option["ProductName"]."'>";
                    }
                    
                }
                echo "</div>";
                echo "</div>";
            }
            echo "<div class='item-size'>";
            echo "<input type='hidden' name='itemid' value='".$itemid."'>";
            $price = 0;$id=5;
                foreach ($product_size as $size){
                    if($id > $size['SizeID']){
                        $price = $size['Price'];
                        $id = $size['SizeID'];
                    }
                        echo "
                        <input class='radio radio-btn size' type='radio' name='size' id='". strtolower($size['SizeName'])."' value={$size['SizeID']}>
                        <label for='". strtolower($size['SizeName'])."' class='size-btn'>".substr($size['SizeName'],0,1)."</label>";
                }
                //echo "<input type='hidden' class='radio radio-btn size' name='size' id='regular' value='4'>";
 
            echo  "</div>";
       
             
                 
            echo "<p class='item-price'>Price: $ ".$price."</p>
            <input type='hidden' class='opt-price' id='opt-price' value='0'>
                <input type='submit' class='btn ad-to-cart'>
                </div>
                </form>";
                //<button class='btn add-to-cart'>Add to Cart</button>
        ?>        
    </div>
    
  </body>
</html>

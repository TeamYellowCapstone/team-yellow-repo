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
            $detail_query = "SELECT ProductName, Description, Price, ImgID, Department, Catagory, IsMenuItem, Type FROM Product_Item WHERE MasterSKU = ? LIMIT 1;";
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

                //get Option names
                $option_name_query = "SELECT DISTINCT Catagory FROM Product_Item WHERE Department = ?";
                $option_name_stmt = $conn->prepare($option_name_query);
                $dept = "Options";
                $option_name_stmt->bind_param("s", $dept);
                $option_name_stmt->execute();
                $option_name_result = $option_name_stmt->get_result();
                $option_name_stmt->close();


                //get the actual options available for each option catagory
                $option_result = array();
                if($row["Type"] === "Coffee"){
                    foreach($option_name_result as $product_option){
                        $option_query = "SELECT ProductName, MasterSKU, Price FROM Product_Item WHERE Department = ? AND Catagory = ?;";
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
                $size_query = "SELECT * FROM Product_Size;";
                $size_result = $conn->query($size_query);
                $product_size = $size_result->fetch_all(MYSQLI_ASSOC);

                // Get Price
                $price_query = "SELECT Price, PricePercentage FROM Product_Item, Product_Size WHERE MasterSKU = ? and SizeID = ?;";
                $price_stmt = $conn->prepare($price_query);
                $currID = "id";
                $sizeID = "size";
                $price_stmt->bind_param("si",$currID, $sizeID);
                $price_stmt->execute();
                $price_result = $price_stmt->get_result();
                $price_stmt->close();

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
            echo "<img src='" .$imgSrc. "' height='300' width='300'> ";

            echo "<p id='desc'>".$row["Description"]."</p>";
            

            
            echo "<form action='scripts/php/addToCart.php' method='GET' class='form add-to-cart-form'>";
            ?>
            <img src="images/creamer.png" alt="creamer" height='75' width='auto' >
            <?php
            //$option_result = ["Creamer"=>["soy","2%"], "Sweetener"=>["sugar","honey]]
            foreach ($option_result as $key => $value) {
                if(($row["Catagory"] != "Iced" && $row["Catagory"] != "Hot") && $key == "Add Ons"){
                    continue;
                }
                echo "<h3 class='collapsible-option option-name'>".$key." <span class='count'></span></h3>";
                echo "<div class='creamer-options'>";
                foreach ($value as $option) {
                    echo "<input type='hidden' class='option' name = '".strtolower($key)."[]' value = '".$option["MasterSKU"]."' id = '".$option["ProductName"]."'>";
                    echo "<input type='hidden' class='pump' name = 'pump-".strtolower($key)."[]' value = 0 min = 0 max=20 id = 'pump-".$option["ProductName"]."'>";
                    echo "<label for='".$option["ProductName"]."' class='option-item'>".$option["ProductName"]."<span class='remove opt-btn'>-</span><span class='add opt-btn'>+</span></label>";
                }
                echo "</div>";
                
            }
            echo "<div class='item-size'>";
            echo "<input type='hidden' name='itemid' value='".$itemid."'>";
            if($row["Department"] == "Drinks"){
                foreach ($product_size as $size){
                    if($size['SizeID'] != 4){
                        echo "
                        <input class='radio radio-btn size' type='radio' name='size' id='". strtolower($size['SizeName'])."' value={$size['SizeID']}>
                        <label for='". strtolower($size['SizeName'])."'>".substr($size['SizeName'],0,1)."</label>";
                    }                  
                }
            }
            else{
                echo "<input type='hidden' class='radio radio-btn size' name='size' id='regular' value='4'>";
            }
            echo  "</div>";
       
             
                 
            echo "<p class='item-price'>Price: $ ".$row["Price"]."</p>
            <input type='hidden' class='opt-price' id='opt-price' value='0'>
                <input type='submit' class='btn ad-to-cart'>
                </div>
                </form>";
                //<button class='btn add-to-cart'>Add to Cart</button>
        ?>        
    </div>
    
  </body>
</html>
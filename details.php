<?php
    require "templates/sessions_and_cookies.php";
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
            $detail_query = "SELECT ProductName, Description, Price, ImgID, IsMenuItem FROM Product_Item WHERE MasterSKU = ? LIMIT 1;";
            $detail_stmt = $conn->prepare($detail_query);
            $detail_stmt->bind_param("s", $itemid);
            $detail_stmt->execute();
            $detail_result = $detail_stmt->get_result();
            $detail_stmt->close();
            $row;
            //$query = "SELECT * FROM Product_Size;";
            //if($result = $conn->query($query)){
               // $productSize = $result->fetch_all(MYSQLI_ASSOC);
            //}
            if($detail_result->num_rows > 0){
                $row = $detail_result->fetch_assoc();
                if($row["IsMenuItem"] != 1){
                    header ("Location: menu.php");
                    return;
                }
                //get options
                $option_query = "SELECT ProductName, MasterSKU FROM Product_Item WHERE Department = ? AND Catagory = ?;";
                $option_stmt = $conn->prepare($option_query);
                $dept = "Options";
                $cat = "Creamer";
                $option_stmt->bind_param("ss", $dept, $cat);
                $option_stmt->execute();
                $option_result = $option_stmt->get_result();
                $option_stmt->close();


                //Get Product Size
                $size_query = "SELECT * FROM Product_Size;";
                $size_result = $conn->query($size_query);
                $product_size = $size_result->fetch_all(MYSQLI_ASSOC);

                // Get Size
                $price_query = "SELECT Price, PricePercentage FROM Product_Item, Product_Size WHERE MasterSKU = ? and SizeID = ?;";
                $price_stmt = $conn->prepare($price_query);
                $currID = "id";
                $sizeID = "size";
                $price_stmt->bind_param("si",$currID, $sizeID);
                $price_stmt->execute();
                $price_result = $price_stmt->get_result();
                $price_stmt->close();

                
  
                //$price_query = "SELECT * FROM Product_Size WHERE SizeID = ?;";
                //$price_query = $conn->prepare($price_query);
                //$imgID = $row["ImgID"];

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
                    $imgSrc = "uploads/placeHolder.png";
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
    <script type="text/javascript" src="scripts/faq.js" defer></script>
    <script type="text/javascript" src="scripts/add-to-cart.js" defer></script>
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
            echo "<p>Creamer Options:</p>";
            echo "<div class='creamer-options'>";
            foreach ($option_result as $option) {
                echo "<input type='checkbox' name = 'creamers[]' value = '".$option["MasterSKU"]."' id = '".$option["ProductName"]."'>";
                echo "<label for='".$option["ProductName"]."'>".$option["ProductName"]."</label>";
            }
            echo "</div>";
            echo "<div class='item-size'>";
            echo "<input type='hidden' name='itemid' value='".$itemid."'>";
            foreach ($product_size as $size){
                echo "
                    <input class='radio radio-btn size' type='radio' name='size' id='". strtolower($size['SizeName'])."' value={$size['SizeID']}>
                    <label for='". strtolower($size['SizeName'])."'>".substr($size['SizeName'],0,1)."</label>";
                                               
            }
            echo  "</div>";
       
             
                 
            echo "<p class='item-price'>Price: ".$row["Price"]."</p>
                </div>
                <input type='submit' class='btn ad-to-cart'>
                </div>
                </form>";
                //<button class='btn add-to-cart'>Add to Cart</button>
        ?>

        
    </div>
    
  </body>
</html>

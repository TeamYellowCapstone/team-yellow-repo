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
            if($detail_result->num_rows > 0){
                $row = $detail_result->fetch_assoc();
                if($row["IsMenuItem"] != 1){
                    header ("Location: menu.php");
                    return;
                }
                //get options
                $option_query = "SELECT ProductName FROM Product_Item WHERE Department = ? AND Catagory = ?;";
                $option_stmt = $conn->prepare($option_query);
                $dept = "Options";
                $cat = "Creamer";
                $option_stmt->bind_param("ss", $dept, $cat);
                $option_stmt->execute();
                $option_result = $option_stmt->get_result();
                $option_stmt->close();

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
                    $imgSrc = $img_row["ImgLocation"] ."/". $img_row["ImgName"];
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
    <title>FAQ</title>
</head>

  <body>

    <?php
      require "templates/navigation.php";
    ?>
    <h1 class="centerText">Product Detail</h1>
    <div>
        <?php
            echo "<h2>".$row["ProductName"]."</h2>";
            echo "<img src=".$imgSrc.">";
            echo "<br>";
            echo "<p>Creamer Options:</p>";
            foreach ($option_result as $option) {
                echo $option["ProductName"];
                echo "<br>";
            }
        ?>
        
    </div>
    
  </body>
</html>

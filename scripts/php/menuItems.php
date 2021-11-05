<?php
    $msg = "";

    if(isset($_GET["department"])){
        $dept = $_GET["department"];
        if(ctype_alpha($dept)){
            require_once "../../connection/connection.php";
            if($dept == "All"){
                $dept = "%";
            }
            //get catagories available for this department
            $catagory_query = "SELECT DISTINCT(Catagory) FROM Product_Item WHERE IsMenuItem = 1 AND Department like ?;";
            $cat_stmt = $conn->prepare($catagory_query);
            $cat_stmt->bind_param("s",$dept);
            $cat_stmt->execute();
            $cat_result = $cat_stmt->get_result();
            $cat_stmt->close();
            if($cat_result->num_rows > 0){
                //for each available catogories under this department
                foreach ($cat_result as $cat) {
                    $product_query = "SELECT MasterSKU, ProductName, Description, Price, ImgID FROM Product_Item WHERE IsMenuItem = 1 AND Department like ? AND Catagory = ?;";
                    $product_stmt = $conn->prepare($product_query);
                    $prod_cat = $cat["Catagory"];
                    $product_stmt->bind_param("ss",$dept,$prod_cat);
                    $product_stmt->execute();
                    $prod_result = $product_stmt->get_result();
                    $product_stmt->close();
                    if($prod_result->num_rows > 0){
                        //print catagory name and display/fetch items in the catagory
                        $msg = $msg. "<h2 class='catagory-name'>".$prod_cat."</h2><hr class='catagory-line'>";
                        while($row = $prod_result->fetch_assoc()){
                            $msg = $msg. "<a href='details.php?itemid=".$row['MasterSKU']."' class='menu-item-link'>
                                    <div class='menu-item' id='item".$row['MasterSKU']."'>
                                        <h2 class='item-name centerText'>".$row['ProductName']."</h2>
                                        <div><img src='images/".getImage($conn,$row["ImgID"])."' alt='' class='img menu-item-img'></div>
                                    </div>
                                    </a>";
                        }
                    }
                }
            }
            $conn->close();
        }
        else{
            $msg = "No Product found!";
        }
    }
    else{
        header("Location: menu.php");
        return;
    }
    echo $msg;
    //get image location and pass to image src
    function getImage($conn,$id){
        $img = "uploads/placeHolder.png";
        $img_query = "SELECT ImgName, ImgLocation FROM Image 
                        INNER JOIN Product_Item ON
                        Product_Item.ImgID = Image.ImgID
                        INNER JOIN Image_Location ON
                        Image.ImgLocationID = Image_Location.ImgLocationID
                        WHERE Image.ImgID =". $id.";";
        if($result = $conn->query($img_query)){
            while($row = $result->fetch_assoc()){
                $img = $row["ImgLocation"] ."/". $row["ImgName"];
            }
        }
        return $img;
    }
?>
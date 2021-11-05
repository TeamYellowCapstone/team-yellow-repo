<?php
    require "scripts/php/menuPageLoad.php";
    require "connection/connection.php";

    if($conn->connect_error){
        die("Connection Error");
    }
    $products = array();
    $productSize = array();
    $query = "SELECT * FROM Product_Item;";
    if($result = $conn->query($query)){
        $products = $result->fetch_all(MYSQLI_ASSOC);
    }
    $query = "SELECT * FROM Product_Size;";
    if($result = $conn->query($query)){
        $productSize = $result->fetch_all(MYSQLI_ASSOC);
    }
    $department = array();
    $department_query = "SELECT DISTINCT(Department) FROM Product_Item WHERE IsMenuItem = 1;";
    if($department_result = $conn->query($department_query)){
        while($row = $department_result->fetch_assoc()){
            array_push($department, $row["Department"]);
        }
    }
    //"SELECT MasterSKU, ProductName, Description, Price, ImgID, Department, Catagory FROM Product_Item WHERE IsMenuItem = 1;"
    if($catQuery = $conn->query($query)){
        $items = $result->fetch_all(MYSQLI_ASSOC);
    }
    //$items = array();
    $conn->close();
?>
<!Doctype html>
<html lang="en">
    <head>
    <?php
        require "templates/head.php";
    ?>
    <script type="text/javascript" src="scripts/add-to-cart.js" defer></script>
    <script type="text/javascript" src="scripts/load-product.js" defer></script>    
    <!-- <script type="text/javascript" src="scripts/clear-cart.js" defer></script>
    <script type="text/javascript" src="scripts/checkout.js" defer></script> -->
    <title>Menu</title>
    </head>
    <body>
        <?php
            require "templates/navigation.php";
        ?>
        <div class="content menu">
            <h1 class="centerText"><u>Our Menu</u></h1>
            <div class="dept-links">
                <a href='#all' class='dep-opt false-link btn'>All</a>
                <?php 
                    foreach($department as $dep){
                        echo "<a href='#".$dep."' class='dep-opt false-link btn'>".$dep."</a>";
                    }
                ?>
            </div>
            <div class="menu-item-container flex-box" id="menu">
                
            </div>
            <!-- <button class="btn" id="clear-cart">Clear Cart</button>
            <button class="btn" id="checkout-btn">Checkout</button> -->
        </div>
        <div class="background-wrap">
        
        </div>

    </body>
</html>
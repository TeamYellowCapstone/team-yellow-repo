<?php
    session_start();

    if(isset($_SESSION["role"]) && $_SESSION["role"] == 1){
        if(!isset($_SESSION["action"])){
            $_SESSION["action"] = "add";
        }
    }
    else{
        header("HTTP/1.1 403 Forbidden");
        header("Location: ../index.php");
        return;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php
        require ("../templates/head.php");
    ?> 
    <script src="scripts/js/switchTab.js" defer></script>
    <title>Maintenenance</title>
</head>

    <body>
        <?php
            require ("../templates/navigation.php");
        ?>
        <div class="maintenance">
            <label class="main-radio-lbl btn" for="add-item">Add Item</label>
            <input type="radio" value="add" id="add-item" name="add" class="main-radio-btn radio" <?php echo $_SESSION["action"] == "add" ? " checked" : ""?>>
            <label class="main-radio-lbl btn" for="update-item">Update Item</label>
            <input type="radio" value="update" id="update-item" name="add" class="main-radio-btn radio" <?php echo $_SESSION["action"] == "update" ? " checked" : ""?>>

        </div>
        <!-- Add item display begins here -->
       
        <!-- Add item display ends here -->

        <!-- Update item display begins here -->
        <div class="update-item-form form">
            <?php
                echo $_SESSION["action"] == "add" ? "<h1> Add Item </h1>" : "<h1> Update Item </h1>";
            ?>
            <p class="center-text">Please enter the required information below.</p>
            <form method="POST" action="../scripts/php/addNewItem.php">
                <?php
                    if($_SESSION["action"] == "update"){
                        echo "<input type='text' class='text search-box' name='searchValue' placeholder='Enter Product SKU or Name here to search'>
                        <input type='submit' class='btn' value='Search' name='search'>";
                    }
                //dipslay error if there is one
                    if(isset($_SESSION["errorMsg"])){
                        echo "<p class='error center-text'>".$_SESSION["errorMsg"]."</p>";
                        unset($_SESSION["errorMsg"]);
                    }
                    if(isset($_SESSION["success"])){
                        echo "<p class='success center-text'>".$_SESSION["success"]."</p>";
                        unset($_SESSION["success"]);
                    }
                ?>
                <div >
                    <label for="sku" > SKU: </label >
                    <input type="text" id="sku" name="sku" placeholder="SKU" <?php 
                        if(isset($_SESSION["sku"])){
                            echo "value='".$_SESSION["sku"]."'";
                            unset($_SESSION["sku"]);
                        }?>> <span class="ast" > *</span >
                </div >
                <div >
                    <label for="pname" > Product Name: </label >
                    <input type="text" id="pname" name="pname" placeholder="Product Name" <?php 
                        if(isset($_SESSION["pname"])){
                            echo "value='".$_SESSION["pname"]."'";
                            unset($_SESSION["pname"]);
                        }?>> <span class="ast" > *</span >
                </div >
                <div >
                    <label for="desc" > Description: </label >
                    <textarea class="txt-area main-txt"id="desc" name="desc" placeholder="Product Description" ><?php 
                        if(isset($_SESSION["desc"])){
                            echo $_SESSION["desc"];
                            unset($_SESSION["desc"]);
                        }?></textarea><span class="ast" > *</span >
                </div >
                <div >
                    <label for="price" > Price: </label >
                    <input type="text" id="price" name="price" placeholder="2.55" <?php 
                        if(isset($_SESSION["price"])){
                            echo "value=".$_SESSION["price"];
                            unset($_SESSION["price"]);
                        }?>> <span class="ast" > *</span >
                </div >
                <div >
                    <?php
                        if($_SESSION["action"] == "add"){
                            echo "<input class='submit btn' type='submit' name='add' value='Add' >";
                        }
                        else{
                            echo "<input class='submit btn' type='submit' name='update' value='Update' >";
                        }
                    ?>
                    
                    <a href="index.php" class="btn cancel">Cancel</a>
                </div >
                <div >
                    
                </div >
            </form >
        </div >
        <!-- Update item display ends here -->

        <div class="background-wrap">
            
        </div>
    </body>
</html>
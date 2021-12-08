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
            <a href="../signup.php" class="btn btn-link">Add Employee</a>
            <button class="stock-btn btn">Out of Stock</button>
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
                    if(isset($_SESSION["quantity"]) && $_SESSION["quantity"] == 0){
                        echo "<p class='error center-text'>This product is out of stock!</p>";
                    }
                ?>
                <div >
                    <label for="sku" class="input-lbl"> SKU: </label >
                    <input type="text" id="sku" name="sku" placeholder="SKU" <?php 
                        if(isset($_SESSION["sku"])){
                            echo "value='".$_SESSION["sku"]."'";
                            unset($_SESSION["sku"]);
                        }?>> <span class="ast" > *</span >
                </div >
                <div >
                    <label for="pname" class="input-lbl"> Product Name: </label >
                    <input type="text" id="pname" name="pname" placeholder="Product Name" <?php 
                        if(isset($_SESSION["pname"])){
                            echo "value='".$_SESSION["pname"]."'";
                            unset($_SESSION["pname"]);
                        }?>> <span class="ast" > *</span >
                </div >
                <div >
                    <label for="desc" class="input-lbl"> Description: </label >
                    <textarea class="txt-area main-txt"id="desc" name="desc" placeholder="Product Description" ><?php 
                        if(isset($_SESSION["desc"])){
                            echo $_SESSION["desc"];
                            unset($_SESSION["desc"]);
                        }?></textarea><span class="ast" > *</span >
                </div >
                <fieldset>
                    <legend class="field-title">Price (Set the price for one or more item size)</legend>
                    <div>
                        <label for="small" class="input-lbl">Small: </label >
                        <input type="text" id="small" name="small" placeholder="2.55" <?php 
                            if(isset($_SESSION["small"])){
                                echo "value=".$_SESSION["small"];
                                unset($_SESSION["small"]);
                            }?>>
                    </div>
                    <div>
                        <label for="medium" class="input-lbl">Medium: </label >
                        <input type="text" id="medium" name="medium" placeholder="2.55" <?php 
                            if(isset($_SESSION["medium"])){
                                echo "value=".$_SESSION["medium"];
                                unset($_SESSION["medium"]);
                            }?>>
                    </div>
                    <div>
                        <label for="p-large" class="input-lbl">Large: </label >
                        <input type="text" id="large" name="large" placeholder="2.55" <?php 
                            if(isset($_SESSION["large"])){
                                echo "value=".$_SESSION["large"];
                                unset($_SESSION["large"]);
                            }?>> 
                    </div>
                    <div>
                        <label for="p-regular" class="input-lbl">Regular(one-size): </label >
                        <input type="text" id="regular" name="regular" placeholder="2.55" <?php 
                            if(isset($_SESSION["regular"])){
                                echo "value=".$_SESSION["regular"];
                                unset($_SESSION["regular"]);
                            }?>> 
                    </div>
                </fieldset>
                <div >
                    <label for="dept" class="input-lbl"> Department: </label >
                    <input type="text" id="dept" name="dept" placeholder="Department Name" <?php 
                        if(isset($_SESSION["dept"])){
                            echo "value=".$_SESSION["dept"];
                            unset($_SESSION["dept"]);
                        }?>> <span class="ast" > *</span >
                </div >
                <div >
                    <label for="category" class="input-lbl"> Category: </label >
                    <input type="text" id="category" name="category" placeholder="Category" <?php 
                        if(isset($_SESSION["category"])){
                            echo "value=".$_SESSION["category"];
                            unset($_SESSION["category"]);
                        }?>> <span class="ast" > *</span >
                </div >
                <div >
                    <label for="quantity" class="input-lbl"> Quantity</label >
                    <input type="number" class="qty-btn" id="quantity" min=0 name="quantity" <?php 
                        if(isset($_SESSION["quantity"])){
                            echo "value=".$_SESSION["quantity"];
                            unset($_SESSION["quantity"]);
                        }?>>
                    <span class="ast" > *</span >
                </div >
                <div >
                    <label for="is-menu" >
                        <input type="checkbox" class="chk-btn" id="is-menu" name="ismenu" <?php 
                        if(isset($_SESSION["is-menu"])){
                            echo $_SESSION["is-menu"] == true ? "checked" : "";
                            unset($_SESSION["is-menu"]);
                        }?>> Check if this is a Menu Item.</label >
                    <span class="ast" > *</span >
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
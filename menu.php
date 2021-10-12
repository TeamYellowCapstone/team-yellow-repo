<?php
    require "scripts/php/menuPageLoad.php";
?>
<!Doctype html>
<html>
    <head>
    <?php
        require "templates/head.php";
    ?>
    <script type="text/javascript" src="scripts/add-to-cart.js" defer></script>
    <script type="text/javascript" src="scripts/clear-cart.js" defer></script>
    <script type="text/javascript" src="scripts/checkout.js" defer></script>
    <title>Menu</title>
    </head>
    <body>
        <?php
            require "templates/navigation.php";
        ?>
        <div class="content menu">
            <h1 class="centerText"><u>Our Menu</u></h1>
             <p><span class="centerText"><img src="images/loveyoualatte_logo.png" alt="" width="47" height="51" class="brandLogo" id="brand_logo" align="middle"/></span></p>
            <div class="menu-item-container flex-box">
                <div class="menu-item" id="item1">
                    <h2 class="item-name centerText">Brewed Coffe</h2>
                    <p class="item-desc">Lightly roasted coffee that's soft, mellow and flavorful. Easy-drinking on its own and delicious with milk, sugar or flavored with vanilla, caramel or hazelnut.</p>
                    <div class="item-size-container">
                        <div class="item-size">
                            <input class="radio radio-btn" type="radio" name="size1" id="small1" value=1 checked>
                            <label for="small1">S</label>
                        </div>
                        <div class="item-size">
                            <input class="radio radio-btn" type="radio" name="size1" id="medium1" value=2>
                            <label for="medium1">M</label>
                        </div>
                        <div class="item-size">
                            <input class="radio radio-btn" type="radio" name="size1" id="large1" value=3>
                            <label for="large1">L</label>   
                        </div>
                        <p class="item-price">Price: $<b>2</b>.<small>50</small></p>
                    </div>
                    <button class="btn add-to-cart">Add to Cart</button>
                </div>
                <div class="menu-item" id="item2">
                    <h2 class="item-name centerText">Cappucino</h2>
                    <p class="item-desc">Dark, rich espresso lies in wait under a smoothed and stretched layer of thick milk foam. An alchemy of barista artistry and craft.</p>
                    <div class="item-size-container">
                        <div class="item-size">
                            <input class="radio radio-btn" type="radio" name="size2" id="small2" value=1 checked>
                            <label for="small2">S</label>
                        </div>
                        <div class="item-size">
                            <input class="radio radio-btn" type="radio" name="size2" id="medium2" value=2>
                            <label for="medium2">M</label>
                        </div>
                        <div class="item-size">
                            <input class="radio radio-btn" type="radio" name="size2" id="large2" value=3>
                            <label for="large2">L</label>   
                        </div>
                        <p class="item-price">Price: $<b>3</b>.<small>50</small></p>
                    </div>
                    <button class="btn add-to-cart">Add to Cart</button>
                </div>
                <div class="menu-item" id="item3">
                    <h2 class="item-name centerText">Latte</h2>
                    <p class="item-desc">Our dark, rich espresso balanced with steamed milk and a light layer of foam. A perfect milk-forward warm-up.</p>
                    <div class="item-size-container">
                        <div class="item-size">
                            <input class="radio radio-btn" type="radio" name="size3" id="small3" value=1 checked>
                            <label for="small3">S</label>
                        </div>
                        <div class="item-size">
                            <input class="radio radio-btn" type="radio" name="size3" id="medium3" value=2>
                            <label for="medium3">M</label>
                        </div>
                        <div class="item-size">
                            <input class="radio radio-btn" type="radio" name="size3" id="large3" value=3>
                            <label for="large3">L</label>   
                        </div>
                        <p class="item-price">Price: $<b>2</b>.<small>99</small></p>
                    </div>
                    <button class="btn add-to-cart">Add to Cart</button>
                </div>
                <div class="menu-item" id="item4">
                    <h2 class="item-name centerText">Esspresso</h2>
                    <p class="item-desc">Our smooth signature Espresso Roast with rich flavor and caramelly sweetness is at the very heart of everything we do.</p>
                    <div class="item-size-container">
                        <div class="item-size">
                            <input class="radio radio-btn" type="radio" name="size4" id="small4" value=1 checked>
                            <label for="small4">S</label>
                        </div>
                        <div class="item-size">
                            <input class="radio radio-btn" type="radio" name="size4" id="medium4" value=2>
                            <label for="medium4">M</label>
                        </div>
                        <div class="item-size">
                            <input class="radio radio-btn" type="radio" name="size4" id="large4" value=3>
                            <label for="large4">L</label>   
                        </div>
                        <p class="item-price">Price: $<b>3</b>.<small>99</small></p>
                    </div>
                    <button class="btn add-to-cart">Add to Cart</button>
                </div>
            </div>
            <button class="btn" id="clear-cart">Clear Cart</button>
            <button class="btn" id="checkout-btn">Checkout</button>
        </div>
        <div class="overlay">
            <div id="display-cont">
                <div id="close"><span>&cross;</span></div>
                <div id="display"></div>
                <button class='btn close-btn' id='close-btn'>Close</button>

            </div>
        </div>

    </body>
</html>
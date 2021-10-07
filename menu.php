<!Doctype html>
<html>
    <head>
    <?php
        require "templates/head.php";
    ?> 
    <title>Menu</title>
    </head>
    <body>
        <?php
            require "templates/navigation.php";
        ?>
        <div class="content menu">
            <h1 class="centerText"><u>Our Menu</u></h1>
            <div class="menu-item-container flex-box">
                <div class="menu-item">
                    <h2 class="item-name centerText">Brewed Coffe</h2>
                    <p class="item-desc">Lightly roasted coffee that's soft, mellow and flavorful. Easy-drinking on its own and delicious with milk, sugar or flavored with vanilla, caramel or hazelnut.</p>
                    <div class="item-size-container">
                        <div class="item-size">
                            <input class="radio radio-btn" type="radio" name="size1" id="small1" value="small" checked>
                            <label for="small1">S</label>
                        </div>
                        <div class="item-size">
                            <input class="radio radio-btn" type="radio" name="size1" id="medium1" value="medium">
                            <label for="medium1">M</label>
                        </div>
                        <div class="item-size">
                            <input class="radio radio-btn" type="radio" name="size1" id="large1" value="large">
                            <label for="large1">L</label>   
                        </div>
                        <p class="item-price">Price: $<b>2</b>.<small>50</small></p>
                    </div>
                    <button class="btn add-to-cart">Add to Cart</button>
                </div>
                <div class="menu-item">
                    <h2 class="item-name centerText">Cappucino</h2>
                    <p class="item-desc">Dark, rich espresso lies in wait under a smoothed and stretched layer of thick milk foam. An alchemy of barista artistry and craft.</p>
                    <div class="item-size-container">
                        <div class="item-size">
                            <input class="radio radio-btn" type="radio" name="size2" id="small2" value="small" checked>
                            <label for="small2">S</label>
                        </div>
                        <div class="item-size">
                            <input class="radio radio-btn" type="radio" name="size2" id="medium2" value="medium">
                            <label for="medium2">M</label>
                        </div>
                        <div class="item-size">
                            <input class="radio radio-btn" type="radio" name="size2" id="large2" value="large">
                            <label for="large2">L</label>   
                        </div>
                        <p class="item-price">Price: $<b>3</b>.<small>50</small></p>
                    </div>
                    <button class="btn add-to-cart">Add to Cart</button>
                </div>
                <div class="menu-item">
                    <h2 class="item-name centerText">Latte</h2>
                    <p class="item-desc">Our dark, rich espresso balanced with steamed milk and a light layer of foam. A perfect milk-forward warm-up.</p>
                    <div class="item-size-container">
                        <div class="item-size">
                            <input class="radio radio-btn" type="radio" name="size3" id="small3" value="small" checked>
                            <label for="small3">S</label>
                        </div>
                        <div class="item-size">
                            <input class="radio radio-btn" type="radio" name="size3" id="medium3" value="medium">
                            <label for="medium3">M</label>
                        </div>
                        <div class="item-size">
                            <input class="radio radio-btn" type="radio" name="size3" id="large3" value="large">
                            <label for="large3">L</label>   
                        </div>
                        <p class="item-price">Price: $<b>2</b>.<small>99</small></p>
                    </div>
                    <button class="btn add-to-cart">Add to Cart</button>
                </div>
                <div class="menu-item">
                    <h2 class="item-name centerText">Esspresso</h2>
                    <p class="item-desc">Our smooth signature Espresso Roast with rich flavor and caramelly sweetness is at the very heart of everything we do.</p>
                    <div class="item-size-container">
                        <div class="item-size">
                            <input class="radio radio-btn" type="radio" name="size4" id="small4" value="small" checked>
                            <label for="small4">S</label>
                        </div>
                        <div class="item-size">
                            <input class="radio radio-btn" type="radio" name="size4" id="medium4" value="medium">
                            <label for="medium4">M</label>
                        </div>
                        <div class="item-size">
                            <input class="radio radio-btn" type="radio" name="size4" id="large4" value="large">
                            <label for="large4">L</label>   
                        </div>
                        <p class="item-price">Price: $<b>3</b>.<small>99</small></p>
                    </div>
                    <button class="btn add-to-cart">Add to Cart</button>
                </div>
            </div>
        </div>

    </body>
</html>
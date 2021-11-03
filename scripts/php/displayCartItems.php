<?php
    if(isset($_SESSION["cartQty"])){
        if($_SESSION["cartQty"] == 0){
            echo "<p class='centerText'>Your cart is empty!</p>";
        }
        else{
            $cartQty = 0;
            $cartTotal = 0;
            echo "<table class='tbl'> 
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Size</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>";
                    $row = 0;
            foreach($cart_result as $item){
                echo "<tr>
                        <td>".$item["ProductName"]."</td>
                        <td>".$item["SizeName"]."</td>
                        <td>".$item["Price"] + ($item["Price"]*$item["PricePercentage"]/100)."</td>
                        <td>".$item["Quantity"]."</td>
                        <td>".($item["Price"] + ($item["Price"]*$item["PricePercentage"]/100)) * $item["Quantity"]."</td>
                        <td><a href='cart.php?item=".$item["ID"]."' class='btn ico-btn delete-item-btn' id=".$item["ID"]."><span class='material-icons'>
                        delete
                        </span>
                        </button></td>
                    <tr>";
                    $cartQty += $item["Quantity"];
                    $cartTotal += ($item["Price"] + ($item["Price"]*$item["PricePercentage"]/100)) * $item["Quantity"];
            }
            echo "<tr>
                    <td colspan='3'></td>
                    <td>".$cartQty."</td>
                    <td>".$cartTotal."</td>
                </tr>
                </tbody>
                </table>";
        }
    }
    else{
            echo "<p class='centerText'>Your cart is empty!</p>";
    }

?>
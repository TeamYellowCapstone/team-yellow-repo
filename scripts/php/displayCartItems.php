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
                            <td>Cart Detail</td>
                            <td>Total Price</td>
                            <td></td>
                        </tr>
                    </thead>
                    <tbody>";
                    $row = 0;
            foreach($cart_result as $item){
                echo "<tr>
                        <td>".$item["Quantity"]." X ".$item["SizeName"]." ".$item["ProductName"]." (".$item["Price"]."/item)";
                        $optPrice = 0;
                        if($user == 0){
                            $option = $item["Options"];
                        }
                        else{
                            $option = array();
                            foreach($cart_option_result as $item_option){
                                if($item_option["ID"] == $item["ID"]){
                                    array_push($option, array("ProductName" => $item_option["ProductName"],"Catagory" => 
                                    $item_option["Catagory"], "pump" => $item_option["Quantity"]));
                                }
                            }

                        }
                        $e =0; $s=0; $c=0; $p=0;
                        $espersso = "Add-Ons ($ 1.5/shot): ";
                        $syrup = "Syrup ($ 0.25/pump): ";
                        $sweetener = "Sweetener: ";
                        $creamer = "Creamer: ";
                        foreach ($option as $key) {
                            switch ($key["Catagory"]){
                                case "AddOns":
                                    $espersso .= $key["pump"] . " X " . $key["ProductName"]. " ";
                                    $optPrice += 1.5 * $key["pump"];
                                    $e++;
                                    break;
                                case "Syrup":
                                    $syrup .= $key["pump"] . " X " . $key["ProductName"]. " ";
                                    $optPrice += 0.25 * $key["pump"];
                                    $p++;
                                    break;
                                case "Sweetener":
                                    $sweetener .= $key["pump"] . " X " . $key["ProductName"]. " ";
                                    $s++;
                                    break;
                                case "Creamer":
                                    $c++;
                                    $creamer .= $key["pump"] . " X " . $key["ProductName"]. " ";
                                    break;
                            }
                        }

                        if($e != 0){
                            echo "<br>&emsp;&emsp;" .$espersso;
                        }
                        if($p != 0){
                            echo "<br>&emsp;&emsp;" .$syrup;
                        }
                        if($s != 0){
                            echo "<br>&emsp;&emsp;" .$sweetener;
                        }
                        if($c != 0){
                            echo "<br>&emsp;&emsp;" .$creamer;
                        }
                        echo "</td><td>".$item["Quantity"]*$item["Price"] + $optPrice."</td>";
                        if($user != 0){
                            echo "<td><a href='cart.php?item=".$item["ID"]."' class='btn ico-btn delete-item-btn' id=".$item["ID"]."><span class='material-icons'>
                            delete
                            </span>
                            </button></td>";
                        }
                        else{
                             echo "<td><a href='cart.php?item=".array_keys($cart_result,$item)[0]."' class='btn ico-btn delete-item-btn' id=".array_keys($cart_result,$item)[0]."><span class='material-icons'>
                        delete
                        </span>
                        </button></td>";
                        }
                       
                    echo "</tr>";
                    $cartQty += $item["Quantity"];
                    $cartTotal += $item["Quantity"]*$item["Price"] + $optPrice;
            }
            echo "<tr>
                    <td></td>
                    <td>Total Qty ".$cartQty."</td>
                    <td>Sub Total $".$cartTotal."</td>
                </tr>
                </tbody>
                </table>";
        }
    }
    else{
            echo "<p class='centerText'>Your cart is empty!</p>";
    }

?>
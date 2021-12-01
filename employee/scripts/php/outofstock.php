<?php
    require "../../../templates/sessions_and_cookies.php";
    require_once "../../../connection/connection.php";
    if($conn->error){
        die("Connection Error");
    }
    $msg = "";
    $query = "SELECT * FROM Product_Item WHERE Quantity = 0;";
    $stmt = $conn->prepare($query);
    if($stmt->execute()){
        $result = $stmt->get_result();
        if($result->num_rows > 0){
            $msg .="<table class='tbl'>
                        <thead>
                            <tr>
                                <th>SKU</th>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Category</th>
                            </tr>
                        </thead>
                        <tbody>";
            foreach($result as $item){
                $msg .= "<tr>
                            <td>".$item["MasterSKU"]."</td>
                            <td>".$item["ProductName"]."</td>
                            <td>".$item["Department"]."</td>
                            <td>".$item["Catagory"]."</td>
                        </tr>";
            }
            $msg .= "</tbody></table>";
        }
        else{
            $msg = "No item is Out-Of-Stock yet!";
        }
        
    }
    echo $msg;


?>
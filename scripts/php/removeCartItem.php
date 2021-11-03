<?php
    $msg = "Fail";
    if($_SERVER["REQUEST_METHOD"] == "GET"){
        if(isset($_GET["id"])){
            $id = $_GET["id"];
            require "../../connection/connection.php";

            $query = "DELETE FROM Cart WHERE CartID = ?;";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i",$id);
            $stmt->execute();
            $stmt->close();
            $msg = "<?php require_once 'scripts/php/displayCartItems.php';?>";
            $conn->close();

        }
        
    }
    echo $msg;
?>
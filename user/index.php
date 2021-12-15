<?php
    require "../templates/sessions_and_cookies.php";
    
    $user = isset($_SESSION["UserID"]) ? $_SESSION["UserID"] : 0;

    if(isset($_SESSION["role"]) && $_SESSION["role"] == 2){
        require "../connection/connection.php";
        if($conn->connect_error) {
            die("Connection Error");
        }
        $query = "SELECT FirstName, LastName FROM User WHERE UserID = ? LIMIT 1;";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i",$user);
        $stmt->execute();
        $result = $stmt->get_result();
        $u = $result->fetch_assoc();
        $stmt->close();
        $conn->close();

    }
    else{
        header("HTTP/1.1 403 Forbidden");
        header("Location: ../index.php");
        return;
    }
    $currentLocation = "../";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php
        require ("../templates/head.php");
    ?>
    <title>Profile</title>
</head>

    <body>
        <?php
            require ("../templates/navigation.php");
        ?>
        <div class="profile-cont">
            <img class="usr-img img" src="../images/ico/logo512.png">
            <p class="u-name">Name: <?php echo $u["FirstName"]. " " .$u["LastName"]; ?></p>
            <p class="u-id">UserName: <?php echo $_SESSION["UserName"]; ?></p>
        </div>

        <div class="background-wrap">
            
        </div>
    </body>
</html>
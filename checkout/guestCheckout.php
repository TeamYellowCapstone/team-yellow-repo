<?php
    require "../templates/sessions_and_cookies.php";
    $currentLocation = "../";
    require "../scripts/php/sanitizeAndValidate.php";

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(isset($_POST["create"])){
            $fname;
            $lname;
            $phn;
            $email;
            validateName($fname, "fname");
            validateName($lname, "lname");
            validatePhone($phn,"phno");
            validateEmail($email, "email");
            //validate all inputs
            if(validateName($fname, "fname") && validateName($lname, "lname")
             && validateEmail($email, "email") && validatePhone($phn,"phno")){
                require "../connection/connection.php";
                if($conn->connect_error){
                    die("connection failed");
                }
                $query = "INSERT INTO User (FirstName, LastName, Phone, Email) VALUES (?,?,?,?);";
                $stmt = $conn->prepare($query);
                $phn = $phn == "" ? NULL : $phn;
                $stmt->bind_param("ssss",$fname,$lname,$phn,$email);
                
                if($stmt->execute()){
                    $lastid = $stmt->insert_id;
                    $_SESSION["FirstName"] = $_SESSION["fname"];
                    $_SESSION["UserID"] = $lastid;
                    $_SESSION["role"] = 3;
                    include_once "../scripts/php/moveCart.php";
                    //remove all session keys
                    unset($_SESSION["fname"]);
                    unset($_SESSION["lname"]);
                    unset($_SESSION["email"]);
                    unset($_SESSION["phn"]);
                    //assign session for success to be used on the confisrmation page
                    $_SESSION["acct"] = "successful";
                    $stmt->close();
                    $conn->close();
                    //move to new page to show confirmation
                    header("Location: ../checkout.php");
                    return;
                }
                else{
                    //error 1062 is for duplicate entry
                    if($conn->errno == 1062){
                        //get the name of duplicated column
                        $err = preg_replace("/^[\s\S]*'User./","", $conn->error);
                        //for each column store a corressponding error msg
                        switch ($err) {
                            case "Email_UNIQUE'":
                                $_SESSION["errorMsg"] = "The email address ".$_SESSION["email"]." is already registered";
                                break;
                            case "Phone_UNIQUE'":
                                $_SESSION["errorMsg"] = "The phone number ".$_SESSION["phn"]." is already registered";
                                break;
                            default:
                                $_SESSION["errorMsg"] = "Connection error!";
                                break;
                        }
                    }
                }
                $stmt->close();
                $conn->close();
            }
            //if one of the input is invalid store the error in session key errMsg
            else{
                switch ($_SESSION["err"]) {
                    //fname, lname, email, pwrd, and uname keys can have same error msg and is already stored in the isEmpty function
                    case "fname":
                    case "lname":
                    case "email":
                        break;
                    case "alphanum":
                        $_SESSION["errorMsg"] = "Username can only contain numbers and letters.";
                        break;
                    case "invalidemail":
                        $_SESSION["errorMsg"] = "Email address format is invalid!";
                        break;
                    case "phn":
                        $_SESSION["errorMsg"] = "Phone number can only contain numbers.";
                        break;

                }
                //to prevent the user from reloading or sending the same data twice redirect the user back to the same page with GET request
                header("Location: guestCheckout.php");
                return;
            }

        }
    }
?>

<html lang="en">
    <head>
        <?php
            require "../templates/head.php";
        ?>
        <title>Guest Checkout</title>
    </head>
    <body>
        <?php
            require "../templates/navigation.php";
        ?>
        <div class="signup-form form">
            <h1> Guest Checkout Information </h1>
            <p class="center-text">Please enter the required information below.</p>
            <form method="POST" action="guestCheckout.php">
                <?php
                //dipslay error if there is one
                    if(isset($_SESSION["errorMsg"])){
                        echo "<p class='error center-text'>".$_SESSION["errorMsg"]."</p>";
                        unset($_SESSION["errorMsg"]);
                    }
                ?>
                <div >
                    <label for="fname1" > First Name: </label >
                    <input type="text" id="fname1" name="fname" placeholder="First Name" <?php 
                        if(isset($_SESSION["fname"])){
                            echo "value=".$_SESSION["fname"];
                            unset($_SESSION["fname"]);
                        }?>> <span class="ast" > *</span >
                </div >
                <div >
                    <label for="lname" > Last Name: </label >
                    <input type="text" id="lname" name="lname" placeholder="Last Name" <?php 
                        if(isset($_SESSION["lname"])){
                            echo "value=".$_SESSION["lname"];
                            unset($_SESSION["lname"]);
                        }?>> <span class="ast" > *</span >
                </div >
                <div >
                    <label for="email" > Email: </label >
                    <input type="text" id="email" name="email" placeholder="someone@example.com" <?php 
                        if(isset($_SESSION["email"])){
                            echo "value=".$_SESSION["email"];
                            unset($_SESSION["email"]);
                        }?>> <span class="ast" > *</span >
                </div >
                <div >
                    <label for="phno" > Phone number: </label >
                    <input type="text" id="phno" name="phno" placeholder="Enter your phone number" <?php 
                        if(isset($_SESSION["phno"])){
                            echo "value=".$_SESSION["phno"];
                            unset($_SESSION["phno"]);
                        }?>>
                </div >
                <div >
                    <input class="submit btn" type="submit" name="create" value="Create" >
                    <a href="index.php" class="btn cancel">Cancel</a>
                </div >
                <div >
                    
                </div >
            </form >
        </div >
        <div class="background-wrap">
            
        </div>

    </body >

</html >

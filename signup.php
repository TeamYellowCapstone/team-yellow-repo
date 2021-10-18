<?php
    session_start();

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(isset($_POST["create"])){
            $fname;
            $lname;
            $uname;
            $pwrd;
            $pwrd2;
            $phn;
            $email;
            //validate all inputs
            if(getValidData($fname, $lname, $uname, $email, $pwrd, $pwrd2, $phn)){
                include_once "connection/connection.php";
                if($conn->connect_error){
                    die("connection failed");
                }
                $query = "INSERT INTO User (FirstName, LastName, UserName, Password, Phone, Email) VALUES (?,?,?,?,?,?);";
                $stmt = $conn->prepare($query);
                // hash the password using the defualt algorithm
                $hashed_pwrd = password_hash($pwrd,PASSWORD_DEFAULT);
                $phn = $phn == "" ? NULL : $phn;
                $stmt->bind_param("ssssss",$fname,$lname,$uname,$hashed_pwrd,$phn,$email);
                
                if($stmt->execute()){
                    //remove all session keys
                    unset($_SESSION["fname"]);
                    unset($_SESSION["lname"]);
                    unset($_SESSION["uname"]);
                    unset($_SESSION["email"]);
                    unset($_SESSION["pwrd"]);
                    unset($_SESSION["pwrd2"]);
                    unset($_SESSION["phn"]);
                    //assign session for success to be used on the confisrmation page
                    $_SESSION["acct"] = "successful";
                    $stmt->close();
                    $conn->close();
                    //move to new page to show confirmation
                    header("Location: confirmation.php");
                    return;
                }
                else{
                    //error 1062 is for duplicate entry
                    if($conn->errno == 1062){
                        //get the name of duplicated column
                        $err = preg_replace("/^[\s\S]*'User./","", $conn->error);
                        //for each column store a corressponding error msg
                        switch ($err) {
                            case "UserName_UNIQUE'":
                                $_SESSION["errorMsg"] = "User name ".$_SESSION["uname"]." already exists.";
                                break;
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
                    case "pwrd":
                    case "uname":
                        break;
                    case "alphanum":
                        $_SESSION["errorMsg"] = "Username can only contain numbers and letters.";
                        break;
                    case "invalidemail":
                        $_SESSION["errorMsg"] = "Email address format is invalid!";
                        break;
                    case "nomatch":
                        $_SESSION["errorMsg"] = "The password don't match";
                        break;
                    case "notstrong":
                        $_SESSION["errorMsg"] = "Password should contain at least";
                        $_SESSION["pwrd_msg"] = "<ul class='error login-error'>
                            <li>One UPPER CASE letter</li>
                            <li>One lower case letter</li>
                            <li>One of these characters ! # _ $ characters and</li>
                            <li>Should be at least 8 characters long.</li>
                        </ul>";
                        break;
                    case "phn":
                        $_SESSION["errorMsg"] = "Phone number can only contain numbers.";
                        break;

                }
                //to prevent the user from reloading or sending the same data twice redirect the user back to the same page with GET request
                header("Location: signup.php");
                return;
            }

        }
    }
    // check wether the given input element is empty and returns boolean
    function isEmpty($elementName){
        $empty = FALSE;
        if(isset($_POST[$elementName])){
            if(strlen($_POST[$elementName]) == 0 || preg_match("/^[\s]+$/",$_POST[$elementName])){
                $_SESSION["errorMsg"] = "<span class='ast'>* </span>is a required field!";
                $empty = TRUE;
            }
        }
        return $empty;
    }
    //name should only contain letters from a-z only if not return false
    function alphaOnly($elementName){
        $is_alpha = TRUE;
        $pattern = "/^[A-Za-z]*$/";
        if(!preg_match($pattern,$_POST[$elementName])){
            $_SESSION["errorMsg"] = "Name field can only contain the letters from A-Z!";
            $is_alpha = FALSE;
        }
        return $is_alpha;
    }

    //validates password to make sure it is more than 8 char long and it is combination of uppercase and lowercase letters
    // and it has numbers and !#_$ chars
    function isValidPassword($password){
        $is_valid;
        $pattern1 = "/[^A-Za-z0-9!#_$]+/"; //only these are allowed
        $pattern2 = "/[A-Z]+/"; //atleast 1 uppercase
        $pattern3 = "/[0-9]+/"; //atleast 1 number 
        $pattern4 = "/[!#_$]+/"; // one of these chars
        $pattern5 = "/[a-z]+/"; // one lowercase
        if( !preg_match($pattern1,$password) &&
            preg_match($pattern2,$password) &&
            preg_match($pattern3,$password) &&
            preg_match($pattern4,$password) &&
            preg_match($pattern5,$password) &&
            strlen($password) >= 8){
                $is_valid = TRUE;
        }
        else{
            $is_valid = FALSE;
        }
        return $is_valid;
    }

    //will return true if all input data are valid and sanitized
    //if one of the input is wrong we will store error code based on the error
    //error codes: fname, lname, uname, alphanum, email, invalidemail, nomatch, notstrong, pwrd,and phn
    function getValidData(&$fname, &$lname, &$uname, &$email, &$pwrd, &$pwrd2, &$phn){
        $valid = TRUE;
        unset($_SESSION["err"]);
        //first name
        if(!isEmpty("fname") && alphaOnly("fname")){
            $fname = trim($_POST["fname"]);
            $_SESSION["fname"] = $fname;
        }
        else{
            $_SESSION["err"] = "fname";
            $valid = FALSE;
        }
        //last name
        if(!isEmpty("lname") && alphaOnly("lname")){
            $lname = trim($_POST["lname"]);
            $_SESSION["lname"] = $lname;
        }
        else{
            $_SESSION["err"] = !isset($_SESSION["err"])? "lname" : $_SESSION["err"];
            $valid = FALSE;
        }
        //user name
        if(!isEmpty("uname")){
            $uname = trim($_POST["uname"]);
            $_SESSION["uname"] = $uname;
            //if not alphnumeric
            if(!ctype_alnum($uname)){
                $_SESSION["err"] = !isset($_SESSION["err"])? "alphanum" : $_SESSION["err"];
                $valid = FALSE;
            }
        }
        else{
            $_SESSION["err"] = !isset($_SESSION["err"])? "uname" : $_SESSION["err"];
            $valid = FALSE;
        }
        //email
        if(!isEmpty("email")){
            //use php built-in function to sanitize and validate email
            $email = filter_var($_POST["email"],FILTER_SANITIZE_EMAIL);
            if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
                $_SESSION["err"] = !isset($_SESSION["err"])? "invalidemail" : $_SESSION["err"];
                $valid = FALSE;
            }
            $_SESSION["email"] = $email;
        }
        else{
            $_SESSION["err"] = !isset($_SESSION["err"])? "email" : $_SESSION["err"];
            $valid = FALSE;
        }
        //password
        if(!isEmpty("pwrd") && !isEmpty("pwrd2")){
            $pwrd = $_POST["pwrd"];
            $pwrd2 = $_POST["pwrd2"];
            $_SESSION["pwrd"] = $pwrd;
            $_SESSION["pwrd2"] = $pwrd2;
            if(isValidPassword($pwrd)){
                if($pwrd !== $pwrd2){
                    $_SESSION["err"] = !isset($_SESSION["err"])? "nomatch" : $_SESSION["err"];
                    $valid = FALSE;
                }
            }
            else{
                $_SESSION["err"] = !isset($_SESSION["err"])? "notstrong" : $_SESSION["err"];
                $valid = FALSE;
            }
        }
        else{
            $_SESSION["err"] = !isset($_SESSION["err"])? "pwrd" : $_SESSION["err"];
            $valid = FALSE;
        }
        //phone numbers
        if(ctype_digit($_POST["phno"]) || isEmpty("phno")){
            $phn = trim($_POST["phno"]);
            $_SESSION["phn"] = $phn;
        }
        else{
            $_SESSION["err"] = !isset($_SESSION["err"])? "phn" : $_SESSION["err"];
            $valid = FALSE;
        }
        return $valid;
    }
?>

<html>
    <head>
        <?php
            require "templates/head.php";
        ?>
        <title>Sign Up</title>
    </head>
    <body>
        <?php
            require "templates/navigation.php";
        ?>
        <div class="signup-form form">
            <h1> Sign Up </h1>
            <p>Please enter the required information below.</p>
            <form method="POST" action="signup.php">
                <?php
                //dipslay error if there is one
                    if(isset($_SESSION["errorMsg"])){
                        echo "<p class='error login-error'>".$_SESSION["errorMsg"]."</p>";
                        if(isset($_SESSION["pwrd_msg"])){
                            echo $_SESSION["pwrd_msg"];
                            unset($_SESSION["pwrd_msg"]);
                        }
                        unset($_SESSION["errorMsg"]);
                    }
                ?>
                <div >
                    <label for="fname" > First Name: </label >
                    <input type="text" id="fname" name="fname" placeholder="First Name" <?php 
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
                    <label for="uname" > Username: </label >
                    <input type="text" id="uname" name="uname" placeholder="Create a user name" <?php 
                        if(isset($_SESSION["uname"])){
                            echo "value=".$_SESSION["uname"];
                            unset($_SESSION["uname"]);
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
                    <label for="pwrd" > Password: </label >
                    <input type="password" id="pwrd" name="pwrd" placeholder="New Password" <?php 
                        if(isset($_SESSION["pwrd"])){
                            echo "value=".$_SESSION["pwrd"];
                            unset($_SESSION["pwrd"]);
                        }?>> <span class="ast" > *</span >

                </div >
                <div >
                    <label for="pwrd2" > Confirm Password: </label >
                    <input type="password" id="pwrd2" name="pwrd2" placeholder="Confirm Password" <?php 
                        if(isset($_SESSION["pwrd2"])){
                            echo "value=".$_SESSION["pwrd2"];
                            unset($_SESSION["pwrd2"]);
                        }?>> <span class="ast" > *</span >

                </div >
                <div >
                    <label for="phno" > Phone number: </label >
                    <input type="text" id="phno" name="phno" placeholder="Enter your phone number" <?php 
                        if(isset($_SESSION["phn"])){
                            echo "value=".$_SESSION["phn"];
                            unset($_SESSION["phn"]);
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

    </body >

</html >

<?php
    require "scripts/php/menuPageLoad.php";
    require "scripts/php/sanitizeAndValidate.php";
    require "scripts/php/action/loginAction.php";

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(isset($_POST["create"])){
            $fname;
            $lname;
            $uname;
            $pwrd;
            $pwrd2 = $_POST["pwrd2"];
            $phn;
            $email;
            validateName($fname, "fname");
            validateName($lname, "lname");
            validateUsername($uname, "uname");
            validateEmail($email, "email");
            validatePassword($pwrd,"pwrd");
            validatePhone($phn,"phno");
            passwordMatch($pwrd, $pwrd2);
            //validate all inputs
            if(validateName($fname, "fname") && validateName($lname, "lname")
             && validateUsername($uname, "uname") && validateEmail($email, "email")
              && validatePassword($pwrd,"pwrd") && validatePhone($phn,"phno")
              && passwordMatch($pwrd, $pwrd2)){
                require "connection/connection.php";
                if($conn->connect_error){
                    die("connection failed");
                }
                $query = "INSERT INTO User (FirstName, LastName, UserName, Password, Phone, Email) VALUES (?,?,?,?,?,?);";
                $stmt = $conn->prepare($query);
                //hash the password using the defualt algorithm
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
                        $_SESSION["pwrd_msg"] = "<ul class='error'>
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
?>

<html lang="en">
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
        <div>
            <div>
                <?php require "templates/loginTemplate.php"; ?>
            </div>
            <div>

            </div>
        </div>
        <div class="background-wrap">
            
        </div>

    </body >

</html >

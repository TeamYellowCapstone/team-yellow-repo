<?php
    require "scripts/php/menuPageLoad.php";
    require "scripts/php/sanitizeAndValidate.php";

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

                // For Credential table that stores the UserName and Password
                $query = "INSERT INTO Credential (UserName, Password) VALUES (?,?);";
                $stmt = $conn->prepare($query);
                // hash the password using the defualt algorithm
                $hashed_pwrd = password_hash($pwrd,PASSWORD_DEFAULT);
                $stmt->bind_param("ss",$uname,$hashed_pwrd);
                if($stmt->execute()){
                    $lastid = $stmt->insert_id;
                    $stmt->close();
                }else{
                    if($conn->errno == 1062){
                        //get the name of duplicated column
                        $err = preg_replace("/^[\s\S]*'Credential./","", $conn->error);
                        if($err=="UserName_UNIQUE'"){
                            $_SESSION["errorMsg"] = "User name ".$_SESSION["uname"]." already exists.";
                        }    
                    }
                }
              

                // Uses user table to get FirstName, LastName, Phone, Email, CredentialID as well as use the query above
                $query = "INSERT INTO User (FirstName, LastName, Phone, Email, CredentialID) VALUES (?,?,?,?,?);";
                $stmt = $conn->prepare($query);
                $phn = $phn == "" ? NULL : $phn;
                $stmt->bind_param("ssssi",$fname,$lname,$phn,$email,$lastid);
                
                
                if($stmt->execute() && !isset($_SESSION["errorMsg"])){
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
                            case "Email_UNIQUE'":
                                $_SESSION["errorMsg"] = "The email address ".$_SESSION["email"]." is already registered";
                                break;
                            case "Phone_UNIQUE'":
                                $_SESSION["errorMsg"] = "The phone number ".$_SESSION["phn"]." is already registered";
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
                    case "alphalower":
                        $_SESSION["errorMsg"] = "Username can only contain lowercase letters";
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
        <div class="signup-form form">
            <h1> Sign Up </h1>
            <p class="center-text">Please enter the required information below.</p>
            <form method="POST" action="signup.php">
                <?php
                //dipslay error if there is one
                    if(isset($_SESSION["errorMsg"])){
                        echo "<p class='error center-text'>".$_SESSION["errorMsg"]."</p>";
                        if(isset($_SESSION["pwrd_msg"])){
                            echo $_SESSION["pwrd_msg"];
                            unset($_SESSION["pwrd_msg"]);
                        }
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

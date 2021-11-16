<?php
//load neccessary files
    require "scripts/php/menuPageLoad.php";
    require "scripts/php/sanitizeAndValidate.php";

    //if page loaded with post data perform an action that data
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        //if account button is pressed get the post data and validate it, and if everything is valid
        //and the user is new create new account using the post data
        if(isset($_POST["create"])){
            $fname;
            $lname;
            $uname;
            $pwrd;
            $pwrd2 = $_POST["pwrd2"];
            $phn;
            $email;
            //these method called here to initiate
            validateName($fname, "fname");
            validateName($lname, "lname");
            validateUsername($uname, "uname");
            validateEmail($email, "email");
            validatePassword($pwrd,"pwrd");
            validatePhone($phn,"phno");
            passwordMatch($pwrd, $pwrd2);
            //call the above methods again to validate all inputs
            if(validateName($fname, "fname") && validateName($lname, "lname")
             && validateUsername($uname, "uname") && validateEmail($email, "email")
              && validatePassword($pwrd,"pwrd") && validatePhone($phn,"phno")
              && passwordMatch($pwrd, $pwrd2)){
                require "connection/connection.php";
                if($conn->connect_error){
                    die("connection failed");
                }
                // hash the password using the defualt algorithm
                $hashed_pwrd = password_hash($pwrd,PASSWORD_DEFAULT);
                //if phone field is empty set it to null (needed for db)
                $phn = $phn == "" ? NULL : $phn;
                $_SESSION["phn"] = $phn;
                //check if the username, phone, or email address exists in db using userview
                $user_exists_query = "SELECT UserName, Phone, Email FROM UserView WHERE UserName = ? OR Phone = ? OR Email = ?;";
                $user_exists_stmt = $conn->prepare($user_exists_query);
                $user_exists_stmt->bind_param("sss",$uname,$phn,$email);
                $user_exists_stmt->execute();
                $user_exists_result = $user_exists_stmt->get_result();
                if($user_exists_result->num_rows > 0){//user exist
                    $user_exists_stmt->close();
                    $row = $user_exists_result->fetch_assoc();
                    //assign the appropriate error msg
                    if($row["UserName"] == $uname){
                        $_SESSION["errorMsg"] = "User name ".$_SESSION["uname"]." already exists.";
                    }
                    elseif($row["Email"] == $email){
                        $_SESSION["errorMsg"] = "The Email address ".$_SESSION["email"]." already exists.";
                    }
                    else{
                        $_SESSION["errorMsg"] = "The Phone number ".$_SESSION["phn"]." already exists.";
                    }
                }
                else{//user does not exists
                        $user_exists_stmt->close();
                        // For Credential table that stores the UserName and Password
                        $query = "INSERT INTO Credential (UserName, Password) VALUES (?,?);";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param("ss",$uname,$hashed_pwrd);
                        if($stmt->execute()){
                            $lastid = $stmt->insert_id;
                            $stmt->close();                    

                        // Uses user table to get FirstName, LastName, Phone, Email, CredentialID as well as use the query above
                        $query = "INSERT INTO User (FirstName, LastName, Phone, Email, CredentialID, RoleID) VALUES (?,?,?,?,?,?);";
                        $stmt = $conn->prepare($query);
                        //set the user role to appropriate privilege
                        $roleid = $_SESSION["role"]  == 3 ? 2 : $_SESSION["role"];
                        $stmt->bind_param("ssssii",$fname,$lname,$phn,$email,$lastid,$roleid);
                        
                        
                        if($stmt->execute()){
                            //remove all session keys related to the post data
                            unset($_SESSION["fname"]);
                            unset($_SESSION["lname"]);
                            unset($_SESSION["uname"]);
                            unset($_SESSION["email"]);
                            unset($_SESSION["pwrd"]);
                            unset($_SESSION["pwrd2"]);
                            unset($_SESSION["phn"]);
                            unset($_SESSION["errorMsg"]);
                            //assign session for success to be used on the confirmation page
                            $_SESSION["acct"] = "successful";
                            $stmt->close();
                            $conn->close();
                            //move to new page to show confirmation
                            header("Location: confirmation.php");
                            return;

                        }
                        $stmt->close();
                        $conn->close();
                    }
                }
                
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
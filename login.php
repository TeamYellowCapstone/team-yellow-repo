<?php
    require "scripts/php/menuPageLoad.php";

    if($_SERVER["REQUEST_METHOD"] = "POST"){
        if(isset($_POST["login"])){
            $uname;
            if(isset($_POST["uname"])){
                //needed for re-entry
                $_SESSION["uname"] = $_POST["uname"];
                //check for valid character combination
                if(ctype_alnum($_POST["uname"])){
                    $uname = trim($_POST["uname"]);
                }
                else{
                    $_SESSION["loginErr"] = !isset($_SESSION["loginErr"]) ? "Invalid characters in username" : $_SESSION["loginErr"];
                }
            }

            $pwrd;
            if(isset($_POST["pwrd"])){
                $_SESSION["pwrd"] = $_POST["pwrd"];
                //check for valid password character combination
                if(!preg_match("/[^A-Za-z0-9!#_$]+/",$_POST["pwrd"])){
                    $pwrd = $_POST["pwrd"];
                }
                else{
                    $_SESSION["loginErr"] = !isset($_SESSION["loginErr"]) ? "Invalid characters in password" : $_SESSION["loginErr"];
                }
                
            }

            if(!isset($_SESSION["loginErr"])){
                include "connection/connection.php";

                if($conn->connect_error){
                    die("Connection error");
                }
                $IP = $_SERVER["REMOTE_ADDR"];
                //flag for user with three failed login
                $blocked = FALSE;
                //check if IP address is valid for both IPV6 and IPV4
                if(filter_var($IP,FILTER_VALIDATE_IP,FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6)){
                    //check wether this ip exists in the failed db table
                    $attempt_query = "SELECT NumberOfAttempts, LastAttemptTime FROM Failed_Attempts WHERE UserIP = ?;";
                    $attempt_result = queryResult($conn,$IP,$attempt_query);
                    //if ip exists check how many attempts and minutes left for the next try
                    if($attempt_result->num_rows > 0){
                        while($row = $attempt_result->fetch_assoc()){
                            $lastTime = new DateTime($row["LastAttemptTime"]);
                            $currentTime =  time();//unix timestamp
                            $elapsedMinute = ($currentTime - $lastTime->getTimeStamp())/60 ; //difference in minutes
                            //if the user is already on the list and it has 3 attempts within 15 minutes block the user
                            if($row["NumberOfAttempts"] == 3 && $elapsedMinute < 15){
                                $blocked = TRUE;
                                //error to be displayed
                                $_SESSION["wait"] = "Please wait at least ".round((15 -$elapsedMinute))." minutes.";
                            }
                            else if($row["NumberOfAttempts"] == 3 && $elapsedMinute >= 15){
                                $blocked = FALSE;
                                $delete_query = "DELETE FROM Failed_Attempts WHERE UserIP = ?;";
                                $delete_result = queryResult($conn,$IP,$delete_query);
                                unset($_SESSION["wait"]);
                            }
                        }
                    }

                }
                //do this if the ip is not blocked
                if(!$blocked){
                    //unset the error about 15 minute
                    unset($_SESSION["wait"]);
                    //get matching username from the db
                    $query = "SELECT User.UserID, FirstName, Password, RoleID FROM Credential
                                Inner JOIN User ON
                                Credential.CredentialID = User.CredentialID 
                                WHERE UserName = ?;";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("s",$uname);
                    if($stmt->execute()){
                        $result = $stmt->get_result();
                        $stmt->close();
                        //if user exists check the password with php verify function
                        if($result->num_rows > 0){
                            while($row = $result->fetch_assoc()){
                                //if password matches get the first name and store it in session and redirect the user to new page
                                if(password_verify($pwrd,$row["Password"])){
                                    $fname = $row["FirstName"];
                                    $_SESSION["FirstName"] = $fname;
                                    $_SESSION["UserID"] = $row["UserID"];
                                    $_SESSION["role"] = $row["RoleID"];
                                    $_SESSION["UserName"] = $uname;
                                    include_once "scripts/php/moveCart.php";
                                    unset($_SESSION["uname"]);
                                    unset($_SESSION["pwrd"]);
                                    $blocked = FALSE;
                                    $delete_query = "DELETE FROM Failed_Attempts WHERE UserIP = ?;";
                                    $delete_result = queryResult($conn,$IP,$delete_query);
                                    unset($_SESSION["wait"]);
                                    $conn->close();
                                    header("Location: index.php");
                                    return;
                                }
                                //if password don't match log the failed attempt
                                else{
                                    $_SESSION["loginErr"] = "Wrong Username and Password";
                                    if(filter_var($IP,FILTER_VALIDATE_IP,FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6)){
                                        $attempt_query = "SELECT NumberOfAttempts, LastAttemptTime FROM Failed_Attempts WHERE UserIP = ?;";
                                        $attempt_result = queryResult($conn,$IP,$attempt_query);
                                        //if ip exists in db increase no. of attempt or check 15 minute time gap has been met
                                        if($attempt_result->num_rows > 0){
                                            while($row = $attempt_result->fetch_assoc()){
                                                $attempt = $row["NumberOfAttempts"];
                                                //if no. of attempt is less than three increament it by one
                                                if($attempt < 3){
                                                    $update_query = "UPDATE Failed_Attempts SET NumberOfAttempts = NumberOfAttempts + 1, LastAttemptTime = CURTIME() WHERE UserIP = ?;";
                                                    $update_result = queryResult($conn,$IP,$update_query);
                                                    $_SESSION["wait"] = "You have " . (2 - $attempt). " attempts left.";
                                                }
                                                else{
                                                    $lastTime = new DateTime($row["LastAttemptTime"]);
                                                    $currentTime =  time();
                                                    $elapsedMinute = ($currentTime - $lastTime->getTimeStamp())/60 ;
                                                    if($elapsedMinute <= 15){
                                                        $_SESSION["wait"] ="Please wait atleast 15 minutes.";
                                                        header("Location: login.php");
                                                        return;
                                                    }
                                                }
                                            }
                                            
                                        }
                                        //if ip doesn't exists add it to failed attempt table
                                        else{
                                            $insert_query = "INSERT INTO Failed_Attempts (UserIP,NumberOfAttempts) VALUES (?, 1);";
                                            $insert_result = queryResult($conn,$IP,$insert_query);
                                            $_SESSION["wait"] = "You have " . (2). " attempts left.";
                                        }

                                    }
                                    
                                }
                            }
                        }
                        //if username does not exist in db display error
                        else{
                            $_SESSION["loginErr"] = "Wrong Username and Password";
                        }
                    }
                    else{
                        $_SESSION["loginErr"] = "Connection Error";
                    }
                }
                $conn->close();

            }
            
        }

    }
    //excute single query with one parameter and return result
    function queryResult($conn,$value,$query){
        $attempt_stmt = $conn->prepare($query);
        $attempt_stmt->bind_param("s",$value);
        $attempt_stmt->execute();
        $attempt_result = $attempt_stmt->get_result();
        $attempt_stmt->close();
        return $attempt_result;
    }

?>

<html lang="en">
    <head>
        <?php
            require "templates/head.php";
        ?>
        <title>Login</title>
    </head>
    <body>
        <?php
            require "templates/navigation.php";
        ?>
        <div class="login-form form">
        <h1> Login </h1>
        <p class="center-text">Please enter your information below to login.</p>
            <form method="POST" action="login.php">
                <?php
                    if(isset($_SESSION["loginErr"])){
                        echo "<p class='error center-text'>".$_SESSION["loginErr"]."</p>";
                        unset($_SESSION["loginErr"]);
                    }
                    if(isset($_SESSION["wait"])){
                        echo "<p class='error center-text'>".$_SESSION["wait"]."</p>";
                        unset($_SESSION["wait"]);
                    }
                ?>
                <div>
                    <label for="username" class="input-lbl">User Name: </label>
                    <input name="uname" type="text" id="username" placeholder="Username" 
                    <?php
                        if(isset($_SESSION["uname"])){
                            echo "value=".$_SESSION["uname"];
                            unset($_SESSION["uname"]);
                        }
                    ?>>
                </div>
                <div>
                    <label for="pwrd" class="input-lbl">Password: </label>
                    <input name="pwrd" type="password" id="pwrd" placeholder="Password" 
                    <?php
                        if(isset($_SESSION["pwrd"])){
                            echo "value=".$_SESSION["pwrd"];
                            unset($_SESSION["pwrd"]);
                        }
                    ?>>
                </div>
                <div>
                    <label><input type="checkbox" checked="checked" name="remember" class="chk-btn"> Remember me</label>
                        <input type="submit" value="Login" name="login" class="btn">
                </div>
                <div>
                    <p>Don't Have account? <a href="signup.php">Sign Up</a></p>
                </div>
            </form>
        </div>
        <div class="background-wrap">
            
        </div>

    </body>

</html>

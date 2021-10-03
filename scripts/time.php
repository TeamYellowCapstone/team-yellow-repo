<?php
  if(!isset($_COOKIE["id"])){
    setcookie("id", 0);
  }

  $message = "";
  //connection credientials

  $conn = new mysqli($_SERVER['RDS_HOSTNAME'], $_SERVER['RDS_USERNAME'], $_SERVER['RDS_PASSWORD'], $_SERVER['RDS_DB_NAME'], $_SERVER['RDS_PORT']);
  // Check connection
  if ($conn->connect_error) {
    die("Connection failed: " . mysqli_connect_error());

  }
  //process request
  if($_SERVER['REQUEST_METHOD'] == "GET"){
    if(isset($_GET["v"])){
        //if the request is to retrieve the time
        if($_GET["v"] == "retrieve"){
            $userID = $_COOKIE['id'];
            $sql = "SELECT * FROM `LogTime`.`ButtonTime` WHERE TimeID = ? LIMIT 1";
            //prepare and bind the query with it's parameters
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i",$userID);

            $stmt->execute();
            $result = $stmt->get_result(); //svae the results

            $time = 0;
            if ($result->num_rows > 0){
                while ($row = $result->fetch_assoc()){
                $time = $row["idButtonTime"];
                }
                $message = "Last Log was at: ". $time;
            }
            else{
                $message = "No log found!";
            }
        }
        //if the request is to log the time where the value v is not equal retrieve
        else{
            // Insert Time and date to mysql
            $time = $_GET["v"];
            //make sure the value is the correct format YYYY-MM-DD hh:mm:ss
            if (preg_match("/^\d{4}[-]\d\d[-]\d\d\s\d\d[:]\d\d[:]\d\d$/",$time)){
                $sql = "INSERT INTO `LogTime`.`ButtonTime`
                        (`idButtonTime`)
                        VALUES
                        (?);";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s",$time);

                if ($stmt->execute() === True){
                    $last = $conn->insert_id;
                    setcookie("id",$last);
                    $message = "Current time has been logged successfully!";
                }
                else{
                    $message = "An error has occured. Please try again.";
                }
            }
            else{
                $message = "An error has occured. Please try again.";
            }
        }
    }
    else{
        //if the value of v is not set return to the page
        echo "<h1> the url can't be found</h1>";
        header("Location: ../retrieve-time.php");
        return;
    }
  }
  $conn->close();
  //this is the return value to the requesting page
  echo $message;
?>
<!DOCTYPE html>
<html>
  <head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Hello World</title>
  <?php
    $message = "";
    //connection credientials
    $conn = new mysqli($_SERVER['RDS_HOSTNAME'], $_SERVER['RDS_USERNAME'], $_SERVER['RDS_PASSWORD'], $_SERVER['RDS_DB_NAME'], $_SERVER['RDS_PORT']);
    //$conn = new mysqli($servername, $username, $password, $database);

    // Check connection
    if ($conn->connect_error) {
      die("Connection failed: " . mysqli_connect_error());
      echo "died";
    }
    //process request
    if($_SERVER['REQUEST_METHOD'] == "POST"){
      if(isset($_POST["log"])){

        // Insert Time and date to mysql
        $sql = "INSERT INTO `LogTime`.`ButtonTime`
                (`idButtonTime`)
                VALUES
                (current_time());";

        if ($conn->query($sql) === True){
          $message = "Current time has been logged successfully!";
        }
        else{
          $message = "An error has occured. Please try again.";
        }

      }
      if(isset($_POST["retrieve"])){
        //echo "Retrieve Time";
        $message = "Time retrieved";
      }
    }
    $conn->close();
  ?>
  <style>
    body{
      background-color: #6bb0b5;
    }
    .center{
      padding: 1em;
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
    }
    .center-text{
      text-align: center;
    }
    .center-div{
      width: fit-content;
      margin: auto;
    }
    input{
      cursor: pointer;
    }
    h1{
      font-size: 5em;
      margin: 0.25em;
      text-shadow: 9px 8px 7px gray;
    }
  </style>
  </head>
  <body >
    <div class="center">
      <h1 class="center-text">Hello World</h1>
      <p class="center-text">Please press the "Log Time" button to log your current time.</p>
      <form method="POST">
        <div class="center-div">
          <input type="submit" id="log" name="log" value="Log Time">
          <!-- <input type="submit" id="retrieve" name="retrieve" value="Retrieve Time"> -->
        </div>
      </form>
      <?php
        echo "<p class='center-text'> $message </p>";
      ?>
    </div>
  </body>
</html>
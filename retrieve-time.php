<!DOCTYPE html>
<html>
  <head>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Hello World</title>
  
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
    <div class="center-div">
        <button id="log" onclick="log()">Log Time</button>
        <button id="retrieve" onclick="retrieve()">Retrieve Time</button>
    </div>
        <p class='center-text' id="status"></p>
    </div>

    <script>
        function retrieve(){
            ajax_request("scripts/time.php?v=retrieve"); //value = retrieve tells the server we are trying to retrieve
        }
        function log( ){
            //create date object
            var localeTime = new Date();

            //convert date object into usable format
            var year = localeTime.getFullYear(); //YYYY
            var month = ("0" + (localeTime.getMonth() + 1)).slice(-2); //MM
            var day = ("0" + localeTime.getDate()).slice(-2); //DD
            var hour = localeTime.getHours(); // 0-23
            hour = hour % 12 //change time format to 0-11
            hour = (hour == 0) ? 12 : hour; // if hour is 0 set it to 12
            hour = ("0" + hour).slice(-2); //hh
            var minute = ("0" + localeTime.getMinutes()).slice(-2); //mm
            var second = ("0" + localeTime.getSeconds()).slice(-2);//ss
            var formattedTime = "" + year + "-" + month + "-" + day + " " + hour + ":" + minute + ":" + second; //YYYY-MM-DD hh:mm:ss
            
            ajax_request("scripts/time.php?v="+formattedTime); //send the local time to the server
        }
        function ajax_request(url){
            var requestObject = new XMLHttpRequest(); //create XMLHttpRequest object
            
            //if there is state change in our request and the response is ready and ok display the message
            requestObject.onreadystatechange = function() {
                if(this.readyState == 4 && this.status == 200){
                    document.getElementById("status").innerHTML = requestObject.responseText;
                }
            };
            requestObject.open("GET", url); //open the request channel or connection
            requestObject.send(); //send the request
        }

    </script>
  </body>
</html>
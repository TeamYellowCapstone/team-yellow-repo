<h1> Login </h1>
<p class="center-text">Please enter your information below to login.</p>
<form method="POST" id="login-form">
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
            <input type="submit" class="btn login-btn" value="Log In">
    </div>
    <div>
        <p>Don't Have account? <a href="signup.php">Sign Up</a></p>
    </div>
    
</form>
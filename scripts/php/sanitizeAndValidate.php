<?php
    if(isset($_SESSION["err"])){
        unset($_SESSION["err"]);
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
    function alphaOnly($elementName, $patt = "/^[A-Za-z]*$/"){
        $is_alpha = TRUE;
        $pattern = $patt;
        if(!preg_match($pattern,$_POST[$elementName])){
            $_SESSION["errorMsg"] = "Field can only contain the letters from A-Z!";
            $is_alpha = FALSE;
        }
        return $is_alpha;
    }
    //

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

    function passwordMatch($p1, $p2){
        if($p1 !== $p2){
            $_SESSION["err"] = !isset($_SESSION["err"])? "nomatch" : $_SESSION["err"];
            return FALSE;
        }
        return TRUE;
    }

    //if provided name is valid get the name
    function validateName(&$name, $inputName){
        if(!isEmpty($inputName) && alphaOnly($inputName)){
            $name = trim($_POST[$inputName]);
            $_SESSION[$inputName] = $name;
            return TRUE;
        }
        else{
            $_SESSION["err"] = !isset($_SESSION["err"])? $inputName : $_SESSION["err"];
            return FALSE;
        }
    }

    function validateNameWithSpace(&$name,$inputName){
        if(!isEmpty($inputName) && alphaOnly($inputName, "/^([A-Za-z]+\s?)*$/")){
            $name = $_POST[$inputName];
            $_SESSION[$inputName] = $name;
            return TRUE;
        }
        else{
            $_SESSION["err"] = !isset($_SESSION["err"])? $inputName : $_SESSION["err"];
            return FALSE;
        }
    }

    function validateString(&$str,$inputName){
        if(!isEmpty($inputName)){
            $str = filter_var($_POST[$inputName], FILTER_SANITIZE_STRING);
            $_SESSION[$inputName] = $str;
            return TRUE;
        }
        else{
            $_SESSION["err"] = !isset($_SESSION["err"])? $inputName : $_SESSION["err"];
            return FALSE;
        }
    }

    //SKU is in the form 00X XX eg. 01D HC or 01D CC etc..
    function validateSKU(&$SKU,$inputName){
        if(!isEmpty($inputName)){
            $SKU = $_POST[$inputName];
            $_SESSION[$inputName] = $SKU;
            if(!preg_match("/^\d\d[a-zA-Z]\s[a-zA-Z]{2}$/", $SKU)){
                $_SESSION["err"] = !isset($_SESSION["err"])? "skuCode" : $_SESSION["err"];
                return FALSE;
            }
            return TRUE;

        }
        else{
            $_SESSION["err"] = !isset($_SESSION["err"])? $inputName : $_SESSION["err"];
            return FALSE;
        }
    }

    function validateUserName(&$name,$inputName){
        if(!isEmpty($inputName)){
            $name = trim($_POST[$inputName]);
            $_SESSION[$inputName] = $name;
            //if not alphnumeric
            if(!ctype_alnum($name)){
                $_SESSION["err"] = !isset($_SESSION["err"])? "alphanum" : $_SESSION["err"];
                return FALSE;
            }
            return TRUE;
        }
        else{
            $_SESSION["err"] = !isset($_SESSION["err"])? $inputName : $_SESSION["err"];
            return FALSE;
        }
    }

    function validateEmail(&$email,$inputName){
        if(!isEmpty($inputName)){
            //use php built-in function to sanitize and validate email
            $email = filter_var($_POST[$inputName],FILTER_SANITIZE_EMAIL);
            $_SESSION[$inputName] = $email;
            if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
                $_SESSION["err"] = !isset($_SESSION["err"])? "invalidemail" : $_SESSION["err"];
               return FALSE;
            }
            return TRUE;
        }
        else{
            $_SESSION["err"] = !isset($_SESSION["err"])? $inputName : $_SESSION["err"];
            return FALSE;
        }
    }

    function validatePassword(&$pwrd,$inputName){
        if(!isEmpty($inputName)){
            $pwrd = $_POST[$inputName];
            $_SESSION[$inputName] = $pwrd;
            if(isValidPassword($pwrd)){
                return TRUE;
            }
            else{
                $_SESSION["err"] = !isset($_SESSION["err"])? "notstrong" : $_SESSION["err"];
                return FALSE;
            }
        }
        else{
            $_SESSION["err"] = !isset($_SESSION["err"])? $inputName : $_SESSION["err"];
            return FALSE;
        }
    }

    function validatePhone(&$phn,$inputName){
        if(ctype_digit($_POST[$inputName]) || isEmpty($inputName)){
            $phn = trim($_POST[$inputName]);
            $_SESSION[$inputName] = $phn;
            return TRUE;
        }
        else{
            $_SESSION["err"] = !isset($_SESSION["err"])? $inputName : $_SESSION["err"];
            return FALSE;
        }
    }
    //validate price to the form xx.xx or xx or xxxxx.xx never xx.xxx or xx.
    function validatePrice(&$price, $inputName){
        if(!isEmpty($inputName)){
            $price = trim($_POST[$inputName]);
            $_SESSION[$inputName] = $price;
            //if not numeric
            if(!preg_match("/^[\d]+$|^[\d]+\.\d$|^[\d]+\.\d\d$/",$_POST[$inputName])){
                $_SESSION["err"] = !isset($_SESSION["err"])? "invalidPrice" : $_SESSION["err"];
                return FALSE;
            }
            return TRUE;
        }
        else{
            $_SESSION["err"] = !isset($_SESSION["err"])? $inputName : $_SESSION["err"];
            return FALSE;
        }
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
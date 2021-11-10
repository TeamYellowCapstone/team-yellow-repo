//assign event for add item btn
let login_btn = document.getElementById("login-form");
if(login_btn != null){
    login_btn.addEventListener("submit",function(evt){
        evt.preventDefault();
        login()
    });
}

let uname = document.getElementById("username");
let pwrd = document.getElementById("pwrd");

//add item function get the parent of the button, item ID, and use that id and size pass it to GET request
function login(){
    uname = uname.value;
    pwrd = pwrd.value;
    let url = "scripts/php/action/loginAction.php";
    ajax_login_request(url);
}

//ajax used to add item
function ajax_login_request(url){
    let xHttp = new XMLHttpRequest();
    xHttp.onreadystatechange = function(){
        if(xHttp.status == 200){
            if(xHttp.readyState == 4){
                if(xHttp.responseText != "Error"){
                    window.location.reload(true);
                    //document.getElementById("display-cont").style.display = "none";
                }
            }
            else{
                // let display=document.getElementById("display-cont");
                // display.style.display = "block";
                // display.innerHTML = '<img src="images/loading.gif" class="img loading-img" alt="page is loading">';
            }
        }
    };

    xHttp.open("POST", url);
    xHttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xHttp.send("uname="+uname+"&pwrd="+pwrd);
}

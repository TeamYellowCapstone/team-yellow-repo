let dispCont = document.getElementsByClassName("overlay")[0];
let disp = document.getElementById("display");

let chkoutBtn = document.getElementById("checkout-btn");
chkoutBtn.addEventListener("click", function(){
    ajax_checkout("scripts/php/checkout.php?log="+log());//pass local time as GET parameter

});

//create current local time
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
    return formattedTime;
}

let closeBtnCross = document.getElementById("close");
closeBtnCross.addEventListener("click",function(){
    disp.innerHTML = "";
    dispCont.style.display = "none";
});
let closeBtn = document.getElementById("close-btn");
closeBtn.addEventListener("click",function(){
    disp.innerHTML = "";
    dispCont.style.display = "none";
});
function ajax_checkout(url){
    let xHttp = new XMLHttpRequest();

    xHttp.onreadystatechange = function(){
        if(xHttp.readyState == 4 && xHttp.status == 200){
            if(xHttp.responseText != "Error"){
                disp.innerHTML = xHttp.response;
                dispCont.style.display = "block";
                cartQtyDisplay.innerHTML = 0;
            }
            else if(cartQtyDisplay.innerHTML == 0){
                alert("Cart is empty!");
            }
            else{
                alert(xHttp.responseText);
            }
        }
    }

    xHttp.open("GET", url);
    xHttp.send();
}
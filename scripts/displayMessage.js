let msgBox = document.getElementsByClassName("msg-box")[0];
let msgBody = document.getElementById("msg");
function showMsg(message){
    msgBox.style.display = "block";
    msgBody.innerHTML = message;
    setTimeout(function(){
        msgBox.style.display = "none";
        msgBody.innerHTML = "";
    },2000);
    
    
}

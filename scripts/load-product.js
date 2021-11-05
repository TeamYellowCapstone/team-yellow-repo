let link = document.getElementsByClassName("dep-opt false-link");
let menuDisplay = document.getElementById("menu");
for(let x=0; x<link.length; x++){
    link[x].addEventListener("click",getItems);
}

link.onload = getItems("click");

function getItems(evt){
    let dept = this.innerHTML != undefined ? this.innerHTML : "All";
    console.log(evt);
    menuRequest = new XMLHttpRequest();
    menuRequest.onreadystatechange = function(){
        if(menuRequest.status == 200 && menuRequest.readyState == 4){
            menuDisplay.innerHTML = menuRequest.response;
        }
    }
    menuRequest.open("GET", "scripts/php/menuItems.php?department="+dept);
    menuRequest.send();
    
}
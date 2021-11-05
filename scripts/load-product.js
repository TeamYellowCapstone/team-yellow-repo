let link = document.getElementsByClassName("dep-opt false-link");
let menuDisplay = document.getElementById("menu");
for(let x=0; x<link.length; x++){
    link[x].addEventListener("click",getItems);
}

function getItems(evt){
    
    menuRequest = new XMLHttpRequest();
    menuRequest.onreadystatechange = function(){
        if(menuRequest.status == 200 && menuRequest.readyState == 4){
            console.log(menuRequest.responseText);
            menuDisplay.innerHTML = menuRequest.response;
        }
    }
    menuRequest.open("GET", "scripts/php/menuItems.php?department="+this.innerHTML);
    menuRequest.send();
    
}
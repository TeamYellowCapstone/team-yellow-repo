let tab = document.getElementsByClassName("main-radio-btn");
for(i=0; i<tab.length;i++){
    tab[i].addEventListener("change",function(){
        xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function(){
            if(xhttp.readyState == 4 && xhttp.status == 200){
                if(xhttp.responseText = "good"){
                    window.location.reload(true);
                }
            }
        }
        xhttp.open("GET","scripts/php/changeTab.php");
        xhttp.send();
    });
}


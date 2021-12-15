let tab = document.getElementsByClassName("main-radio-btn");
for(i=0; i<tab.length;i++){
    tab[i].addEventListener("change",function(){
        xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function(){
            if(xhttp.readyState == 4 && xhttp.status == 200){
                if(xhttp.responseText = "success"){
                    window.location.reload(true);
                }
            }
        }
        xhttp.open("GET","scripts/php/changeTab.php");
        xhttp.send();
    });
}

let outOfStockBtn = document.getElementsByClassName("stock-btn")[0];
outOfStockBtn.addEventListener("click",function(){
    let body = document.getElementsByClassName("update-item-form")[0];
    xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function(){
        if(xhttp.readyState == 4 && xhttp.status == 200){
            body.innerHTML = xhttp.responseText;

        }
    }
    xhttp.open("GET","scripts/php/outofstock.php");
    xhttp.send();
});



let btn = document.getElementById("clear-cart");
btn.addEventListener("click",function(){
    ajax_request_delete();
});

function ajax_request_delete(){
    let xHttp = new XMLHttpRequest();
    xHttp.onreadystatechange = function(){
        if(xHttp.status == 200 && xHttp.readyState == 4){
            alert(xHttp.responseText);
            if(xHttp.responseText == "Your cart has been cleared!"){
                cartQtyDisplay.innerHTML = 0;
            }
            
        }
    };

    xHttp.open("GET", "scripts/php/clearCart.php");
    xHttp.send();
}

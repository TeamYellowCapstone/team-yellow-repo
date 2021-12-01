let btn = document.getElementById("clear-cart");
let cartQtyDisplay =  document.getElementById("qty");
if(btn != null){
    btn.addEventListener("click",function(){
        ajax_request_delete();
    });
}


function ajax_request_delete(){
    let xHttp = new XMLHttpRequest();
    xHttp.onreadystatechange = function(){
        if(xHttp.status == 200 && xHttp.readyState == 4){
            //alert(xHttp.responseText);
            showMsg("Cart is cleared!");
            if(xHttp.responseText == "Your cart has been cleared!"){
                cartQtyDisplay.innerHTML = 0;
                let tbl = document.getElementsByClassName("tbl")[0];
                if(tbl != null){
                    tbl.remove();
                }
                
            }
            
        }
    };

    xHttp.open("GET", "scripts/php/clearCart.php");
    xHttp.send();
}

// let removeBtns = document.getElementsByClassName("delete-item-btn");
// let cartQtyDisplay =  document.getElementById("qty");
// for (let i=0; i<removeBtns.length; i++){
//     removeBtns[i].addEventListener("click",function(){
//         ajaxRemoveItem("scripts/php/removeCartItem.php?id="+removeBtns[i].getAttribute("id"));
//     });
// }

// let displayDiv = document.getElementById("cart-display");


// function ajaxRemoveItem(url){
//     let http = new XMLHttpRequest();
//     http.onreadystatechange = function(){
//         if(http.readyState == 4 && http.status == 200){
//             showMsg("Item is deleted!");
//             displayDiv.innerHTML = http.response;
//             cartQtyDisplay.innerHTML = +cartQtyDisplay.innerHTML - 1;
//         }
//     };

//     http.open("GET", url);
//     http.send();
// }
showMsg("Item is deleted!");
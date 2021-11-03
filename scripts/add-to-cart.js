let cartQtyDisplay =  document.getElementById("qty");
//assign event for add item btn
let add_btn = document.getElementsByClassName("add-to-cart");
for (i=0; i<add_btn.length; i++){
    add_btn[i].addEventListener("click",addItem);
}

//assign event for updating price when item size is selected
let size_btn = document.getElementsByClassName("radio-btn size");
console.log(size_btn);
for (i=0; i<size_btn.length; i++){
    size_btn[i].addEventListener("change",update_price);
    
}

//add item function get the parent of the button, item ID, and use that id and size pass it to GET request
function addItem(){
    let parent = this.parentNode;
    let currentItemId = parent.getAttribute("id");
    currentItemId = currentItemId.substring(4);
    let currentSize = parent.querySelectorAll('.radio:checked')[0].value;
    let url = "scripts/php/addToCart.php?id=" + currentItemId + "&size=" + currentSize;
    ajax_request(url);
}

//This function will fetch the the sizeid and itemid to determine price of the item
function update_price(){
    let parent = this.parentNode;
    parent = parent.parentNode;
    let price = parent.querySelector(".item-price:last-child");
    let currentItemId = parent.parentNode.getAttribute("id");
    currentItemId = currentItemId.substring(4);
    let currSize = this.value;
    this.setAttribute("selected",true);
    //console.log(currSize);
    ajax_price(price,"scripts/php/getPrice.php?id="+currentItemId+"&size="+currSize);
}
//ajax used to add item
function ajax_request(url){
    let xHttp = new XMLHttpRequest();
    xHttp.onreadystatechange = function(){
        if(xHttp.status == 200 && xHttp.readyState == 4){
            alert(xHttp.responseText);
            if(xHttp.responseText != "Error Adding Item"){
                cartQtyDisplay.innerHTML = +cartQtyDisplay.innerHTML + 1;
            }
        }
    };

    xHttp.open("GET", url);
    xHttp.send();
}
//ajax used to update price
function ajax_price(el,url){
    let xHttp = new XMLHttpRequest();

    xHttp.onreadystatechange = function(){
        if(xHttp.status == 200 && xHttp.readyState == 4){
            if(xHttp.responseText != "Fail"){
                el.innerHTML = "Price: $ " + xHttp.responseText;
            }
        }
    };
    xHttp.open("GET", url);
    xHttp.send();

}

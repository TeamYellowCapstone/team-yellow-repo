let cartQtyDisplay =  document.getElementById("qty");
let optionTotalPrice = document.getElementById("opt-price");
var msg = parseInt(document.getElementsByClassName("item-price")[0].innerHTML);
//assign event for add item btn
let add_btn = document.getElementsByClassName("add-to-cart");
for (i=0; i<add_btn.length; i++){
    // add_btn[i].addEventListener("click",addItem);
}

//
let pump_btn = document.getElementsByClassName("opt-btn");
for (let index = 0; index < pump_btn.length; index++) {
    pump_btn[index].addEventListener("click", update_price_with_pump);
    
}

//assign event for updating price when item size is selected
let size_btn = document.getElementsByClassName("radio-btn size");
size_btn[0].checked = true;

for (i=0; i<size_btn.length; i++){
    size_btn[i].addEventListener("change",update_price_with_size);
}

//add item function get the parent of the button, item ID, and use that id and size pass it to GET request
function addItem(){
    let currentItemId = document.getElementsByTagName("h1")[0];
    currentItemId = currentItemId.getAttribute("id");
    let currentSize = document.getElementsByClassName("item-size")[0];
    currentSize = currentSize.querySelectorAll('.radio:checked')[0].value;
    let url = "scripts/php/addToCart.php?id=" + currentItemId + "&size=" + currentSize;
    ajax_request(url);
}

//This function will fetch the the sizeid and itemid to determine price of the item
function update_price_with_size(){
    //let parent = document.getElementsByClassName("item-price")[0];
    let price = document.getElementsByClassName("item-price")[0];
    let currentItemId = document.getElementsByTagName("h1")[0];
    currentItemId = currentItemId.getAttribute("id");
    let currSize = this.value;
    this.setAttribute("selected",true);
    ajax_price(price,"scripts/php/getPrice.php?id="+currentItemId+"&size="+currSize);
    //console.log(ajax_price("scripts/php/getPrice.php?id="+currentItemId+"&size="+currSize));
    //price.innerHTML = "Price: $ " + (msg + optionTotalPrice.value);
    //optionPrices();
}
//
function update_price_with_pump(){
    let valueToAdd;
    let productName = this.parentNode.getAttribute("for");
    let pumpCount = document.getElementById("pump-"+productName);
    if(this.classList.contains("add")){
        valueToAdd = 1;
    }
    else{
        if (pumpCount.value != "0"){
            valueToAdd = -1;
        }
        else{
            valueToAdd = 0;
        }
    }
    
    pumpCount.value = +pumpCount.value + valueToAdd;
    if(pumpCount.value < 0){
        pumpCount.value = 0;
    }
    let price = document.getElementsByClassName("item-price")[0];
    let addPrice = 0;
    if((pumpCount.getAttribute("name")).substring(5) == "syrup[]" && pumpCount.value >= 0){
        addPrice = 0.25;
    }
    //price.innerHTML ="Price: $" + (+(price.innerHTML.substr(9)) + (addPrice * pumpCount.value));
    optionTotalPrice.value = +(optionTotalPrice.value) + (valueToAdd * addPrice);
    price.innerHTML ="Price: $ " + (+(price.innerHTML.substr(9)) + (valueToAdd * addPrice));
    //console.log(item.value);
    //console.log(valueToAdd);
    //ajax_price(price,"scripts/php/getPrice.php?id="+currentItemId+"&size="+currSize);

}
//ajax used to add item
function ajax_request(url){
    let xHttp = new XMLHttpRequest();
    xHttp.onreadystatechange = function(){
        if(xHttp.status == 200 && xHttp.readyState == 4){
            //alert(xHttp.responseText);
            if(xHttp.responseText != "Error Adding Item"){
                cartQtyDisplay.innerHTML = +cartQtyDisplay.innerHTML + 1;
            }
        }
    };
    xHttp.open("GET", url);
    xHttp.send();
}
//ajax used to update price

function ajax_price(price,url){
    let xHttp = new XMLHttpRequest();

    xHttp.onreadystatechange = function(){
        if(xHttp.status == 200 && xHttp.readyState == 4){
            if(xHttp.responseText != "Fail"){
                price.innerHTML = "Price: $ "+ (+(xHttp.responseText) +(+optionTotalPrice.value));
                //setPrice(a);
            }
        }
    };
    xHttp.open("GET", url);
    xHttp.send();

}

function optionPrices(){
    let options = document.getElementsByClassName("option");
    let totalPrice = 0;
    for (let index = 0; index < options.length; index++) {
        ajax_pump_price("scripts/php/getPrice.php?pump="+options[index].value);
        totalPrice += p * document.getElementById("pump-"+options[index].getAttribute("id")).value;
        
    }
    console.log(totalPrice);
}
var p=0;
function setPrice(x){
    msg = x
}
function ajax_pump_price(url){
    let x = new XMLHttpRequest();
    x.onreadystatechange = function(){
        if(x.status == 200 && x.readyState == 4){
            if(x.responseText != "Fail"){
                p = x.responseText;
            }
        }
    };
    x.open("GET", url);
    x.send();

}

let menu_bar = document.getElementById("menu-bar");
let nav = document.getElementsByClassName("navigation-item-container")[0];
if(menu_bar != null){
    menu_bar.addEventListener("click", function(){
            menu_bar.classList.toggle("on");
            nav.classList.toggle("show");
        }
    );
}
// let b = document.getElementsByTagName("body")[0];
// b.addEventListener("click", function(e){
//    if(!e.target.classList.contains("show")){
//        nav.classList.remove("show");
//    }
// })
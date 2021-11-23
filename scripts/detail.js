document.getElementsByTagName("body")[0].onload = function(){
    let collapsible_opt = document.getElementsByClassName("collapsible-option");
    for (let index = 0; index < collapsible_opt.length; index++) {
        console.log(collapsible_opt[index]);
        collapsible_opt[index].addEventListener("click", function(){
            removeClass(collapsible_opt);
            collapsible_opt[index].classList.toggle("collapse-active");
        });
        
    }
};
function removeClass(elements){
    for (let index = 0; index < elements.length; index++) {
        elements[index].classList.remove("collapse-active");
    }
}
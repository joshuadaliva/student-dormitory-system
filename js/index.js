


document.addEventListener("DOMContentLoaded", () =>{
    const openNav = document.querySelector(".hamburger");
    const scrollableNav = document.querySelector(".scrollable-nav");
    
    openNav.addEventListener("click", () => {
        scrollableNav.classList.toggle("toogle-nav");
    })

})
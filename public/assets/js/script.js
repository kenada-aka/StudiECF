
// Menu navbar

let active = 0;

$("#navbar a").each(function( index ) {
    if(window.location.href == this.href)
    {
        this.classList.add("active");
        active++;
    }
    else this.classList.remove("active");
});

if(active == 0 && window.location.pathname == "/")
{
    $("#navbar a")[0].classList.add("active");
}

$("#navbar a").click(function( event ) {
    $("#navbar a").each(function( index ) {
        this.classList.remove("active");
    });
    this.classList.toggle("active");
});

// Event carousel Hero

$("#hero a").click(function( event ) {
    let parentNode = event.target.parentNode.parentNode;
    parentNode.children[0].classList.toggle("active");
    parentNode.children[1].classList.toggle("active");
    event.preventDefault();
});

// Event formulaire contact à la page d'accueil

$("#home-contact").submit(function( event ) {
    alert("Formulaire temporairement désactivé en attendant le nom de domaine...");
    event.preventDefault();
});
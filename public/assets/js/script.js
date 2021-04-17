
// Menu navbar

let active = 0;

$("#navbar a").each(function(index) {
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

$("#navbar a").click(function(event) {
    $("#navbar a").each(function(index) {
        this.classList.remove("active");
    });
    this.classList.toggle("active");
});

// Event carousel Hero

$("#hero a").click(function(event) {
    let parentNode = event.target.parentNode.parentNode;
    parentNode.children[0].classList.toggle("active");
    parentNode.children[1].classList.toggle("active");
    event.preventDefault();
});

// Event formulaire contact à la page d'accueil

$("#home-contact").submit(function(event) {
    alert("Formulaire temporairement désactivé en attendant le nom de domaine...");
    event.preventDefault();
});

// Carousel des photos des annonces

$('div.carousel.slide').each(function(index) {
    this.children[0].children[0].classList.add("active");
    var carousel = new bootstrap.Carousel(this);
});

// Fix form subscribe

$("#subscribe").submit(function(event) {
    if($("#subscribe select").val() != "CB")
    {
        alert("Le moyen de paiement que vous souhaitez utiliser est en cours de developpement, merci pour votre patience et procéder à un paiement par CB, ou bien d'envoyer les informations par mail.");
        event.preventDefault();
    }
});

// Tabs messageries espace membre


$('.myTab button').each(function(index) {
    var tabTrigger = new bootstrap.Tab(this)
    this.addEventListener('click', function(event) {
        event.preventDefault();
        tabTrigger.show();
        $(event.target.parentNode.parentNode.dataset.bsTarget +" div").each(function(index) {
            this.classList.remove("active");
            this.classList.remove("show");
            if(this.id == event.target.dataset.bsTarget)
            {
                this.classList.add("show");
                this.classList.add("active");
            }
        });
    });
});



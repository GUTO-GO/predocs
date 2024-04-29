const $this = this;
const popUp = this.shadowRoot.querySelector('.popup');

this.style.display = "none";

const observer = new IntersectionObserver(function(entries) {
    if (entries[0].isIntersecting === true) {
        popUp.classList.add("slide-in");
        setTimeout(() => {
            popUp.classList.remove("slide-in");
            popUp.classList.add("slide-out");
            setTimeout(() => {
                $this.style.display = "none";
                popUp.classList.remove("slide-out");
            }, 1000);
        }, 3000);
    }
}, { threshold: [0] });

observer.observe(popUp);

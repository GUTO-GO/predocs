const hamburger = this.shadowRoot.querySelector(".hamburger");
const nav = this.shadowRoot.querySelector(".nav");

hamburger.addEventListener("click", () => {
    nav.classList.toggle("active");
});

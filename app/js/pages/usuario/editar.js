const elemInputUserName = document.getElementById("input-name-user");

window.addEventListener('resize', function () {
    ajustarDimensoes(elemInputUserName);
});

var predocs = new Predocs(
    () => {
        elemInputUserName.textContent = "Felipe dos Santos Cavalca";
    },
    () => {
        setTimeout(() => {
            ajustarDimensoes(elemInputUserName);
        }, 100);
    }
);

function ajustarDimensoes(textarea) {
    textarea.style.height = "auto";
    textarea.style.height = (textarea.scrollHeight) + "px";
}
function impedirQuebraDeLinha(event) {
    if (event.key === "Enter" || event.keyCode === 13) {
        event.preventDefault();
    }
}

const elemInputUserName = document.getElementById("input-name-user");

window.addEventListener('resize', function () {
    ajustarDimensoes(elemInputUserName);
});

var predocs = new Predocs(
    (docs) => {
        let id = docs.getParamUrl("id");
        let request = docs.requestGet("/server/usuario/listar", { id: id });
        request = JSON.parse(request);
        if (request.status) {
            setDataInView(request.data);
        }
    },
    (predocs) => {
        setTimeout(() => {
            ajustarDimensoes(elemInputUserName);
        }, 100);

        predocs.form("#form-edit",
            () => {

            },
            (response) => {
                console.log(response);
            }
        )
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

function setDataInView(dataUser) {
    elemInputUserName.value = dataUser.name;
    document.querySelector("input[name='email']").value = dataUser.email;
}

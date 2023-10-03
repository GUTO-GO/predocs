function listarUsuarios() {
    let docs = new Predocs();

    let usuarios = JSON.parse(docs.get("/server/usuario/listar", {}));

    usuarios.forEach(usuario => {
        criarLinhaUsuario(usuario);
    });
}

function criarLinhaUsuario(usuario) {
    let tr = document.createElement("tr");
    
    let campos = [
        "nome",
        "email",
        "tipo",
        "status",
        "modificar",
    ];

    campos.forEach(campo => {
        let td = document.createElement("td");

        switch (campo) {
            case "status":
                let span = document.createElement("span");
                span.classList.add("badge");

                switch (usuario[campo]) {
                    case "ativo":
                        span.classList.add("bg-success");
                        break;
                    case "inativo":
                        span.classList.add("bg-warning");
                        break;
                    case "banido":
                        span.classList.add("bg-danger");
                        break;
                }

                span.innerText = primeiraLetraMaiuscula(usuario[campo]);
                td.prepend(span);
                break;
            case "modificar":
                let a = document.createElement("a");
                a.setAttribute("href", "editar.html?id="+usuario["id"]);
                a.setAttribute("class", "btn btn-primary btn-sm");
                a.appendChild(document.createTextNode("Editar"));
                td.prepend(a);
                break;
            default:
                td.innerText = primeiraLetraMaiuscula(usuario[campo]);
        }

        tr.append(td);
    });

    document.querySelector("#usuarios tbody").append(tr);
}

function primeiraLetraMaiuscula(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}
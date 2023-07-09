function listarUsuarios() {
    let docs = new Predocs();

    let usuarios = JSON.parse(docs.get("/server/usuario/listar", {}, false));

    usuarios.forEach(usuario => {
        criarLinhaUsuario(usuario);
    });

    var usersTable;
    usersTable = $("#users-list-datatable").DataTable({
        responsive: true,
        'columnDefs': [
            {
                "orderable": false,
                "targets": [4]
            }]
    });

    $("[data-filtro]").on("change", function () {
        let valores = "";
        $("[data-filtro]").each(function () {
            valores += ` ${$(this).val()}`;
        });
        usersTable.search(valores).draw();
    });

    $(".users-list-clear").on("click", function () {
        usersTable.search("").draw();
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
                        span.classList.add("badge-light-success");
                        break;
                    case "inativo":
                        span.classList.add("badge-light-warning");
                        break;
                    case "banido":
                        span.classList.add("badge-light-danger");
                        break;
                }

                span.innerText = usuario[campo];
                td.prepend(span);
                break;
            case "modificar":
                let a = document.createElement("a");
                a.setAttribute("href", "#");
                let i = document.createElement("i");
                i.classList.add("bx");
                i.classList.add("bx-edit-alt");
                a.prepend(i);
                td.prepend(a);
                break;
            default:
                td.innerText = usuario[campo];
        }

        tr.append(td);
    });

    document.querySelector("#users-list-datatable tbody").append(tr);


    console.log(tr);
}
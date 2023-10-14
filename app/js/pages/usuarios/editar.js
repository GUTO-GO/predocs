const before = () => {
    docs.replaceTextInView("body", docs.getConfig("app"));
    let id = docs.getParamUrl("id");
    let elementForm = document.querySelector("#edit-form");
    elementForm.action = elementForm.action + id;

    let response = JSON.parse(docs.get("/server/usuario/listar/" + id));
    if (response.status) {
        let usuario = response.data.usuario;
        let fields = ["nome", "email"];

        fields.forEach(campo => {
            document.querySelector("input[name='" + campo + "']").value = usuario[campo];
        });

        docs.montaSelect("[name='tipo']", [
            { value: "usuario", text: "Usuário", selected: usuario.tipo == "usuario" },
            { value: "admin", text: "Administrador", selected: usuario.tipo == "admin" },
        ]);
        docs.montaSelect("[name='status']", [
            { value: "ativo", text: "Ativo", selected: usuario.status == "ativo" },
            { value: "inativo", text: "Inativo", selected: usuario.status == "inativo" },
            { value: "banido", text: "Banido", selected: usuario.status == "banido" },
        ]);
    }
};

const after = () => {
    docs.form("#edit-form",
        () => {

        },
        (resp) => {
            let cor;
            if (resp.status) {
                cor = "#d1e7dd";
            } else {
                cor = "#f8d7da";
            }
            return [
                resp.description,
                cor
            ]
        });
};

const docs = new Predocs();
docs.init(before, after);
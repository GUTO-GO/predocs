var predocs = new Predocs(
    (docs) => {
        let users = JSON.parse(docs.requestGet("/server/usuario/listar"));

        if (users.status) {
            for (let user of users.data) {
                let tr = document.createElement("tr");
                tr.innerHTML = `
                    <td>${user.id}</td>
                    <td>${user.name}</td>
                    <td>${user.email}</td>
                    <td>${new Date(user.created).toLocaleString('pt-BR')}</td>
                    <td>
                        <button class="btn btn-primary">Editar</button>
                        <button class="btn btn-danger">Excluir</button>
                    </td>
                `;
                document.querySelector("table").appendChild(tr);
            }
        }

    },
    (docs) => {
        // Pesquisa itens na tabela.
        var input = document.querySelector("#search-user");
        var table = document.querySelector("table");
        var rows = table.getElementsByTagName("tr");

        input.addEventListener("input", function () {
            var filter = input.value.toUpperCase();

            for (var i = 0; i < rows.length; i++) {
                var td = rows[i].getElementsByTagName("td")[0];
                if (td) {
                    var textValue = td.textContent || td.innerText;
                    if (textValue.toUpperCase().indexOf(filter) > -1) {
                        rows[i].style.display = "";
                    } else {
                        rows[i].style.display = "none";
                    }
                }
            }
        });

        // Cria animação quando algo aparece na tela
        let observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('show-element');
                } else {
                    entry.target.classList.remove('show-element');
                }
            });
        });
        document.querySelectorAll('tr').forEach(element => {
            observer.observe(element);
        });
    }
);

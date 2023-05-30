function exibeIcones(pastas) {
    let divPastas = document.querySelector("#pastas");

    pastas.forEach(pasta => {
        divPastas.appendChild(criaHtmlPastas(pasta));
    });

    aplicaEventos();
}

function criaHtmlPastas(dados) {

    let img = document.createElement("img");
    img.setAttribute("class", "icone");
    img.setAttribute("src", docs.getUrl(dados.icone));

    let texto = document.createElement("a");
    texto.innerText = dados.nome;

    let div = document.createElement("div");
    div.setAttribute("class", "col-4 col-sm-3 col-md-2 col-lg-1");
    if (dados.url)
        div.setAttribute("data-link", dados.url)

    div.appendChild(img);
    div.appendChild(texto);

    return div;
}

function aplicaEventos() {
    $(document).on('click', '[data-link]', function () {
        window.location.href = docs.getUrl($(this).attr('data-link'));
    });
}
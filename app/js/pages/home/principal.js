function exibePastas(pastas) {
    let divPastas = document.querySelector("#pastas");

    pastas.forEach(pasta => {
        divPastas.appendChild(criaHtmlPastas(pasta));
    });

}

function criaHtmlPastas(dados) {

    let img = document.createElement("img");
    img.setAttribute("class", "icone");
    img.setAttribute("src", getIcone(dados.tipo));

    let texto = document.createElement("p");
    texto.innerText = dados.nome;

    let div = document.createElement("div");
    div.setAttribute("class", "col-4 col-sm-3 col-md-2 col-lg-1");
    div.appendChild(img);
    div.appendChild(texto);

    return div;
}

function getIcone(tipo) {
    let tipos = {
        pasta: "https://cdn-icons-png.flaticon.com/512/3767/3767084.png",
        arquivo: "https://cdn-icons-png.flaticon.com/512/6802/6802306.png"
    }

    if (tipos[tipo]) {
        return tipos[tipo];
    }

    return "https://cdn-icons-png.flaticon.com/512/12/12734.png"
}
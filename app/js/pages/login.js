document.addEventListener("DOMContentLoaded", function () {
    const appConfig = {
        componentesBloqueados: ["nav"]
    };

    // Função executada antes da inicialização
    const before = () => {
        // Substitui o texto no elemento 'body' com a configuração do documento
        docs.replaceTextInView("body", docs.getConfig("app"));
    };

    // Função executada após a inicialização
    const after = () => {
        // Configura o formulário com o seletor '#form-login'
        docs.form("#form-login",
            () => {
                // Função de callback executada antes do envio do formulário
            },
            (resp) => {
                let cor;
                if (resp.status) {
                    cor = "#d1e7dd";
                    // Redireciona para a página 'home.html' em caso de sucesso
                    window.location.href = "home.html";
                } else {
                    cor = "#f8d7da";
                }
                // Retorna um array com a descrição da resposta e a cor correspondente
                return [
                    resp.description,
                    cor
                ];
            }
        );
    };

    // Instância da classe Predocs com a configuração do aplicativo
    const docs = new Predocs(appConfig);
    // Inicializa a instância com as funções 'before' e 'after'
    docs.init(before, after);
});


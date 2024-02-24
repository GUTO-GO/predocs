const before = (predocs) => {
    predocs.replaceTextInElement("body", predocs.configApp);
}
const after = (predocs) => {
    const beforeSubmit = () => {
        return true;
    }
    const afterSubmit = (response) => {
        response = JSON.parse(response);
        console.log(response);
        if (response.status) {
            $("c-popup_s").show();
            setTimeout(() => {
                window.location.href = "home.html";
            }, 1000);
        } else {
            $("c-popup_e").text(response.mensagem);
            $("c-popup_e").show();
        }
    }
    predocs.form("#form-login", beforeSubmit, afterSubmit)
}
let predocs = new Predocs(before, after);

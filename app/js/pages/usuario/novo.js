const before = (predocs) => {

}
const after = (predocs) => {
    const beforeSubmit = () => {
        return true;
    }
    const afterSubmit = (response) => {
        response = JSON.parse(response);
        if (response.status) {
            $("c-popup_s").show();
        } else {
            $("c-popup_e").text(response.mensagem);
            $("c-popup_e").show();
        }
    }
    predocs.form("#form-cadastro", beforeSubmit, afterSubmit)
}
let predocs = new Predocs(before, after);

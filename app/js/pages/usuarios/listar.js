$(document).ready(function () {

    // variable declaration
    var usersTable;
    var usersDataArray = [];
    // datatable initialization
    if ($("#users-list-datatable").length > 0) {
        usersTable = $("#users-list-datatable").DataTable({
            responsive: true,
            'columnDefs': [
                {
                    "orderable": false,
                    "targets": [4]
                }]
        });
    };


    // Filtros
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
});

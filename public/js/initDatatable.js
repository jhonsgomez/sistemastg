function initializeDataTable(idTable, ajaxUrl, columnsConfig) {
    $('thead').hide();
    $(idTable).DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: true,
        ajax: {
            url: ajaxUrl,
            data: function (d) {
                d.search.value = $('input[type="search"]').val();

                if ($('#filtroRoles').length) {
                    var selectedFilter = $('#filtroRoles').val();

                    if (selectedFilter) {
                        d[selectedFilter] = 'true';
                    }
                }
            }
        },
        pageLength: 5,
        language: {
            loadingRecords: "",
            lengthMenu: "Mostrar " +
                `<select class='border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow focus:ring-uts-500 focus:border-uts-500' style='cursor: pointer !important;'>
                            <option value = '5'>5</option>
                            <option value = '10'>10</option>
                            <option value = '20'>20</option>
                        </select>` +
                " registros por página",
            zeroRecords: "No hay registros disponibles",
            info: "Mostrando la página _PAGE_ de _PAGES_",
            infoEmpty: "No hay registros disponibles",
            emptyTable: "No hay registros disponibles",
            infoFiltered: "(filtrado de _MAX_ registros)",
            search: "Buscar: "
        },
        layout: {
            topStart: 'pageLength',
            topEnd: {
                'search': {
                    placeholder: 'Ingrese una clave.'
                }
            },
            bottomStart: 'info',
            bottomEnd: 'paging'
        },
        columns: columnsConfig,
        order: [[0, 'desc']],
        initComplete: function () {
            $('thead').show();
        }
    });

    if ($('#filtroRoles').length) {
        $('#filtroRoles').on('change', function () {
            $(idTable).DataTable().ajax.reload();
        });
    }
}
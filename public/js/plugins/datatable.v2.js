$(document).ready(function () {
    // Debounce genérico
    function debounce(fn, ms = 600) {
        let t;
        return function () {
            const ctx = this, args = arguments;
            clearTimeout(t);
            t = setTimeout(() => fn.apply(ctx, args), ms);
        };
    }
    /**
     * Inicializa los filtros por columna en la tabla especificada.
     * - Garantiza que exista una segunda fila en el thead (se clona si no está).
     * - Inserta inputs en las celdas de esa fila, excepto en columnas de acciones o vacías.
     * - Configura búsqueda por columna, disparada únicamente al presionar Enter.
     */
    function initColumnFilters(table, tableClass) {
        // Crear segunda fila en thead si aún no existe
        if ($(tableClass + ' thead tr').length < 2) {
            $(tableClass + ' thead tr').clone().appendTo(tableClass + ' thead');
        }

        // Generar inputs en cada celda de la segunda fila
        $(tableClass + ' thead tr:eq(1) th').each(function (i) {
            var title = $(this).text().trim();

            // Verificar si la columna es searchable según config.columns
            var config = window.tableConfigs[tableClass];
            var columnConfig = config?.columns?.[i];
            var searchable = columnConfig?.searchable !== false;

            // Omitir columnas sin título o de acciones
            if (!searchable || !title || /accion(es)?/i.test(title)) {
                $(this).html(''); // sin input
                return;
            }

            $(this).html('<input type="text" placeholder="Buscar ' + title + '" style="width:100%"/>');
        });

        // Asociar eventos a los inputs (filtra solo al presionar Enter)
        // $(tableClass + ' thead tr:eq(1) th input')
        //     .off('keyup') // eliminar posibles bindings previos
        //     .on('keyup', function (e) {
        //         if (e.key === 'Enter') {
        //             var colIndex = $(this).parent().index();
        //             var val = this.value;

        //             table
        //                 .column(colIndex)
        //                 .search(val)
        //                 .draw();
        //         }
        //     });
        $(tableClass + ' thead tr:eq(1) th input')
            .off('.' + tableClass)
            .on('input.colFilter', debounce(function () {
                const colIndex = $(this).parent().index();
                const val = this.value.trim();

                // opcional: mínimo 2 chars, pero permitir vacío para limpiar
                if (val.length === 0 || val.length >= 2) {
                    table
                        .column(colIndex)
                        .search(val)
                        .draw();
                }
            }, 600));

    }

    function showExportLoading() {
        if (window.Swal) {
            Swal.fire({
                title: 'Generando Excel…',
                html: 'Cargando todos los registros y exportando.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => Swal.showLoading()
            });
        }
    }
    function hideExportLoading() {
        if (window.Swal) Swal.close();
    }

    function exportAllExcelAction(e, dt, button, config) {
        const self = this;
        const $btn = $(button.node);
        const oldStart = dt.settings()[0]._iDisplayStart;
        const oldLen = dt.page.len();

        $btn.prop('disabled', true).addClass('disabled');
        showExportLoading();

        // 1) Forzar request con TODO
        dt.one('preXhr.dt', function (e, s, data) {
            data.start = 0;
            data.length = 10000; // > 3443
        });

        // 2) Recargar y exportar CUANDO YA ESTÁ DIBUJADO
        dt.ajax.reload(function () {
            // 👇 aquí ya existe en dt el dataset grande del draw actual
            $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config);

            // 3) Restaurar paginación normal (esto es la “2da petición”)
            dt.one('preXhr.dt', function (e, s, data) {
                data.start = oldStart;
                data.length = oldLen;
            });

            dt.ajax.reload(function () {
                hideExportLoading();
                $btn.prop('disabled', false).removeClass('disabled');
            }, false);

        }, false);
    }

    /**
     * Inicialización de todas las DataTables definidas en window.tableConfigs.
     * Cada entrada se identifica por un selector (ej: '.table') y un objeto de configuración.
     */
    $.each(window.tableConfigs || {}, function (selector, config) {
        var table = $(selector).DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: config.ajaxUrl,
                type: 'GET'
            },
            columns: config.columns,
            responsive: true,
            searching: true,
            paging: true,
            info: false,

            // EXPORTACIÓN GLOBAL A EXCEL
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Excel',
                    className: 'btn btn-success btn-sm mb-2',
                    action: exportAllExcelAction,
                    exportOptions: {
                        modifier: { page: 'all', search: 'applied', order: 'applied' },
                        columns: function (idx) {
                            const col = config.columns[idx];
                            if (!col) return false;
                            const title = (col.title || "").toLowerCase();
                            if (title.includes("acción") || title.includes("acciones")) return false;
                            return true;
                        }
                    }
                }
            ],

            dom: 'Bfrtip',
            pageLength: window.defaultPageLength || 10,
            rowsGroup: config.rowsGroup || [], // si se necesita agrupamiento de filas
            language: config.language || 'lang/es_datatable.json',    // bloque de idioma opcional
            createdRow: function (row, data, dataIndex) {
                // Ejemplo: añadir tooltip en la columna 21 con el nombre del admin
                var adminCell = $(row).find('td:eq(20)');
                var adminName = adminCell.text();
                adminCell.attr('title', 'Administrador: ' + adminName);
            }
        });

        table.buttons().container().appendTo(
            $(selector).closest('.dataTables_wrapper').find('.col-md-6:eq(0)')
        );

        // Configurar filtros por columna
        initColumnFilters(table, selector);

        // Ocultar buscador global de DataTables, ya que usamos filtros por columna
        $(selector).closest('.dataTables_wrapper').find('.dataTables_filter').hide();

        // Centrar la paginación
        const wrapper = $(selector).closest('.dataTables_wrapper');
        wrapper.find('.dataTables_paginate').addClass('d-flex justify-content-center mt-3');
        wrapper.find('.dataTables_info').addClass('text-center w-100');
    });
});

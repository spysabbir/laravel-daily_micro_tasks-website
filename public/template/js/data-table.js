$.extend(true, $.fn.dataTable.defaults, {
    language: {
        searchPlaceholder: "Search records",
    },
    aLengthMenu: [
        [10, 25, 50, 100, -1],
        [10, 25, 50, 100, "All"]
    ],
    dom: '<"top"Bf>rt<"bottom"lip>',
    stateSave: true,
    buttons: [
        {
            extend: 'colvis',
            text: 'Column Visibility',
        },
        {
            extend: 'excelHtml5',
            text: 'Export to Excel',
            exportOptions: {
                columns: ':visible'
            },
            footer: true,
            autoFilter: true,
            messageTop: function() {
                // This will dynamically fetch the message when printing/exporting
                return window.dynamicMessageTop || '';
            },
            messageBottom: 'This report was generated by the system. Generated on ' + new Date().toLocaleString(),
        },
        {
            extend: 'print',
            text: 'Print',
            exportOptions: {
                columns: ':visible'
            },
            footer: true,
            messageTop: function() {
                // This will dynamically fetch the message when printing/exporting
                return window.dynamicMessageTop || 'Thank you for using our system. We hope you find it useful.';
            },
            messageBottom: 'This report was generated by the system. Generated on ' + new Date().toLocaleString(),
            customize: function (win) {
                $(win.document.body).find('table').css('font-size', 'inherit');
                $(win.document.body).find('table').css('width', '100%');
                $(win.document.body).find('table').css('margin', '0');
                $(win.document.body).find('table').css('padding', '0');
                $(win.document.body).find('table thead, table tfoot').css('display', 'table-header-group');
            },
            orientation: 'landscape',
            pageSize: 'A4',
        }
    ]
});

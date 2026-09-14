"use strict";

document.addEventListener("DOMContentLoaded", function () {
  const $tableEl = $('table[data-display="datatables"]');
  const ajaxUrl = $tableEl.data("ajax");

  const options = {
    responsive: true,
    columnDefs: [
      {
        targets: [0],
        visible: false,
        searchable: false,
      },
      {
        targets: [1, 2],
        orderable: false,
        hidden: true,
      },
    ],
  };

  if (ajaxUrl) {
    options.serverSide = true;
    options.processing = true;
    options.order = [[0, "desc"]];
    options.ajax = { url: ajaxUrl, type: "GET" };
  } else {
    options.order = [];
  }

  const table = $tableEl.DataTable(options);

  if(table.context.length == 0) return;

  $.fn.dataTable.Buttons.defaults.dom.button.className =
    "btn btn-outline-primary";

  new $.fn.dataTable.Buttons(table, {
    buttons: [
      {
        extend: "collection",
        text: "Import",
        buttons: [
          {
            text: "Excel",
            action: function () {
              $("#importExcel").modal("show");
            },
          },
        ],
      },
      {
        extend: "collection",
        text: "Export",
        buttons: [
          {
            text: "Excel",
            action: function (param) {
              let ids = "";
              const data = table.rows({ filter: "applied" }).data();

              data.map((e) => {
                ids += e[0] + ",";
              });

              const tableName = $("#tableName").val();

              window.location.replace(tableName + "/export/excel?ids=" + ids);
            },
          },
          // {
          //   text: "PDF",
          //   action: function (param) {
          //     let ids = "";
          //     const data = table.rows({ filter: "applied" }).data();

          //     data.map((e) => {
          //       ids += e[0] + ",";
          //     });

          //     const tableName = $("#tableName").val();

          //     window.location.replace(tableName + "/export/pdf?ids=" + ids);
          //   },
          // },
          // {
          //   extend: "pdfHtml5",
          //   exportOptions: {
          //     columns: [":visible"],
          //   },
          // },
        ],
      },
    ],
  });

  table.buttons(0, null).containers().appendTo("#actionContainer");
});

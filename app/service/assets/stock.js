$(document).ready(function () {
  GetAssetsInStockList();
});

function GetAssetsInStockList() {
  $.ajax({
    type: "POST",
    url: BASE_URL + "Assets/GetListAssetsInstock",
    data: $("#searchForm").serialize(),
    success: function (response) {
      const Header = $("#assetStockBody");
      let body = "";

      if (response.status == 200) {
        if ($.fn.DataTable.isDataTable("#assetStockTable")) {
          $("#assetStockTable").DataTable().destroy();
        }

        Header.empty();

        response.result.forEach((element, index) => {
          let badge = StatusHandler(element.statusCode);

          body += ` <tr>
                        <td class="fw-semibold">${index + 1}</td>
                        <td>${element.assetName}</td>
                        <td>${element.serialNumber}</td>
                        <td>${element.categoryName ?? "-"}</td>
                        <td>
                          <label class='status-badge ${badge}'>
                            ${element.statusName}
                          </label>
                        </td>
                        <td style="white-space: nowrap;">
                          <a href="${BASE_URL}assets/assign?assetId=${element.assetUnitId}"
                             class="btn btn-outline-primary btn-sm">
                             Assign
                              <i data-feather="arrow-right"></i>
                          </a>
                        </td>
                    </tr>`;
        });

        Header.append(body);
        feather.replace();
        $("#assetStockTable").DataTable({ ordering: false });
      }

      console.log(response);
    },
    error: function (err) {
      alert("Error loading asset stock data");
    },
  });
}

function ResetFilter() {
  document.getElementById("searchForm").reset();
  GetAssetsInStockList();
}

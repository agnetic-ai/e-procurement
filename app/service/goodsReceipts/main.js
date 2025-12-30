$(document).ready(function () {
  GetGoodsReceiptsList();
});
function GetGoodsReceiptsList() {
  $.ajax({
    type: "POST",
    url: BASE_URL + "goodsReceipts/GetGoodsReciptsList",
    data: $("#searchForm").serialize(),
    success: function (response) {
      const Header = $("#goodsTableBody");
      let body = "";
      if (response.status == 200) {
        if ($.fn.DataTable.isDataTable("#goodsTable")) {
          $("#goodsTable").DataTable().destroy();
        }
        Header.empty();
        response.result.forEach((element) => {
          let badge = StatusHandler(element.statusCode);
          body += ` <tr>
                        <td class="fw-semibold">${element.grNumber}</td>
                        <td>${element.prNumber}</td>
                        <td>${element.department}</td>
                        <td><label class='status-badge ${badge}'>${
            element.statusName
          }</label></td>
                        <td>${
                          element.receiptDate == null
                            ? "-"
                            : element.receiptDate
                        }</td>
                        <td>${
                          element.receivedBy == null ? "-" : element.receivedBy
                        }</td>
                        <td style="white-space: nowrap;">
                        <div class="button">
                        
                           <a href="${BASE_URL}goodsReceipts/GetGoodsReciptsDetail?grNumber=${
            element.grNumber
          }" class="btn btn-outline-primary btn-sm">
                              <i data-feather="edit"></i>
                          </a>
                          </div>
                        </td>
                    </tr>`;
        });

        Header.append(body);
        feather.replace();
        $("#goodsTable").DataTable({ ordering: false });
      }
      console.log(response);
    },
    error: function (err) {
      alert("Error loading data");
    },
  });
}

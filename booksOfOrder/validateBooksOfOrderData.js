function ValidateBooksOfOrder(operation) {
    var errorText = "";

    if (operation !== 'delete') {
        var data = Number.parseInt(document.getElementsByName("new0")[0].value);
        if (isNaN(data) || data < 0) {
            errorText += "Поле \"№ заказа\" должно быть положительным числом\n";
		}

        data = document.getElementsByName("new1")[0].value;
        var isbnPattern = /^\d{3}-\d{1}-\d{3}-\d{5}$/;
        if (!isbnPattern.test(data)) {
            errorText += "Поле \"ISBN\" должно соответствовать паттерну 999-9-999-99999\n";
        }
    }

    if (errorText !== "") {
        showError(errorText, '#F00');
    } else {
        var setData = new FormData(document.getElementById("fieldsForm"));
        setData.append("choose_but", operation);
        getData("booksOfOrder/table.php", setData, setTable);
    }
}
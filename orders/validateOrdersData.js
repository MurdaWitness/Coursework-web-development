function ValidateOrders(operation) {
    var errorText = "";

    if (operation !== 'delete') {
        var data = Number.parseInt(document.getElementsByName("new0")[0].value);
        if (isNaN(data) || data < 0) {
            errorText += "Поле \"№ заказа\" должно быть положительным числом\n";
		}

        data = document.getElementsByName("new1")[0].value;
        var adressPattern = /^[0-9а-яА-Я\s.,]{1,50}$/u;
        if (!adressPattern.test(data)) {
            errorText += "Поле \"Адрес доставки\" должно содержать только русские буквы, цифры, пробелы, знаки пунктуации и быть длиной от 1 до 50 символов\n";
        }

        data = document.getElementsByName("new2")[0].value;
        var datePattern = /^(0[1-9]|[12][0-9]|3[01])\.(0[1-9]|1[0-2])\.\d{4}$/;
        if (!datePattern.test(data)) {
            errorText += "Поле \"Дата заказа\" должно соответствовать паттерну 99.99.9999\n";
        }

        data = document.getElementsByName("new3")[0].value;
        var datePattern = /^(0[1-9]|[12][0-9]|3[01])\.(0[1-9]|1[0-2])\.\d{4}$/;
        if (!datePattern.test(data)) {
            errorText += "Поле \"Дата выполнения заказа\" должно соответствовать паттерну 99.99.9999\n";
        }
    }

    if (errorText !== "") {
        showError(errorText, '#F00');
    } else {
        var setData = new FormData(document.getElementById("fieldsForm"));
        setData.append("choose_but", operation);
        getData("orders/table.php", setData, setTable);
    }
}
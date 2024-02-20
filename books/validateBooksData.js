function ValidateBooks(operation) {
    var errorText = "";

    if (operation !== 'delete') {
        var data = document.getElementsByName("new0")[0].value;
        var isbnPattern = /^\d{3}-\d{1}-\d{3}-\d{5}$/;
        if (!isbnPattern.test(data)) {
            errorText += "Поле \"ISBN\" должно соответствовать паттерну 999-9-999-99999\n";
        }

        data = document.getElementsByName("new1")[0].value;
        var fioPattern = /^[а-яА-ЯёЁ\s]{1,50}$/u;
        if (!fioPattern.test(data) || data.length < 1 || data.length > 50) {
            errorText += "Поле \"ФИО автора\" должно содержать только русские буквы, пробелы, и быть длиной от 1 до 50 символов\n";
        }

        data = document.getElementsByName("new2")[0].value;
        var titlePattern = /^[0-9a-zA-Zа-яА-Я\s]{1,30}$/u;
        if (!titlePattern.test(data) || data.length < 1 || data.length > 30) {
            errorText += "Поле \"Название книги\" должно содержать только буквы, цифры, пробелы, и быть длиной от 1 до 30 символов\n";
        }

        data = Number.parseInt(document.getElementsByName("new3")[0].value);
        if (isNaN(data) || data < 0) {
            errorText += "Поле \"Год издания\" должно быть неотрицательным числом\n";
        }

        data = Number.parseInt(document.getElementsByName("new4")[0].value);
        if (isNaN(data) || data < 0) {
            errorText += "Поле \"Цена, руб.\" должно быть неотрицательным числом\n";
        }
    }

    if (errorText !== "") {
        showError(errorText, '#F00');
    } else {
        var setData = new FormData(document.getElementById("fieldsForm"));
        setData.append("choose_but", operation);
        getData("books/table.php", setData, setTable);
    }
}
function ValidateReport(operation){

    var errorText = "";
    if(operation == "reportOrderNum")
    {
        var data = Number.parseInt(document.getElementsByName("new0")[0].value);
        if (isNaN(data) || data < 0) {
            errorText += "Поле \"№ заказа\" должно быть положительным числом\n";
		}
    }
   
   
    if (errorText != "") { // Вывод сообщения об ошибках
		showError(errorText, '#F00');
	}
    else
    {
        var setData = new FormData(document.getElementById("fieldsForm"));
        setData.append("choose_but", operation);
        getData("report/table.php", setData, setTable);
    }
}
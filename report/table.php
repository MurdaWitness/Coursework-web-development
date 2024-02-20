<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<title> Сводная таблица </title>
	<?php
	function ValidateData($new0)
	{
		$errorText = "";

		$data = intval($new0);
		if (empty($data) || $data < 0) {
			$errorText .= "Поле \"№ заказа\" должно быть положительным числом". "<br>";
		}

		return $errorText;
	}
	?>
</head>

<body>
	<?php
	require_once "../login.php";
	$conn = new mysqli($server, $user, $password, $dbname);
	if ($conn->connect_error)
		echo "<p>Невозможно подключиться к серверу или открыть БД: $conn->connect_error </p>\n";
	else {

		if (isset($_POST["choose_but"])) {
			$button = $_POST["choose_but"];
		} else {
			$button = 'report';
		}
		switch ($button) {
			case 'report':
				$query = "SELECT 
						`Книги`.`ФИО автора`,
						`Книги`.`Название книги`,
						`Книги`.`Год издания`,
						`Книги`.`Цена, руб.`,
						`Заказы`.`Адрес доставки`,
						`Заказы`.`Дата заказа`,
						`Заказы`.`Дата выполнения заказа`,
						`Заказы`.`№ заказа`,
						`Книги`.`ISBN`
					FROM `Книги` 
					INNER JOIN `Книги заказа` ON `Книги`.`ISBN` = `Книги заказа`.`ISBN`
					INNER JOIN `Заказы` ON `Книги заказа`.`№ заказа` = `Заказы`.`№ заказа`
					ORDER BY `Заказы`.`№ заказа`, `Книги`.`ISBN`;";

				$result = $conn->query($query);
				$arraySelect = $result->fetch_all();

				$query = "SELECT COUNT(`№ заказа`) FROM `Книги заказа` GROUP BY `№ заказа`;";
				$result = $conn->query($query);
				$arrayCountedBooksInOrder = $result->fetch_all();

				$query = "SELECT `№ заказа` FROM `Книги заказа` GROUP BY `№ заказа`;";
				$result = $conn->query($query);
				$arrayOrderNumbers = $result->fetch_all();


				//Реализация отчета с помощью SQL-запросов
				echo ("<table border = \"3\">");
				echo ("<caption>" . "Сводная таблица" . "</caption>");
				echo ("<tr>");
				echo ("<th colspan = \"4\">" . "Книги" . "</th> <th colspan = \"3\">" . "Заказы" . "</th> <th colspan = \"2\">" . "Книги заказа" . "</th>");
				echo ("</tr>");
				echo ("<tbody> <tr>");
				echo ("<th>" . "ФИО автора" . "</th> <th>" . "Название книги" . "</th> <th>" . "Год издания" . "</th> <th>" . "Цена, руб" . "</th> <th>" . "Адрес доставки." . "</th> <th>" . "Дата заказа" . "</th> <th>" . "Дата выполнения заказа" . "</th> <th>" . "№ заказа" . "</th> <th>" . "ISBN" . "</th>");
				echo ("</tr>");
				$general_index = 0;
				$general_sum = 0;
				$private_sum = 0;
				for ($k = 0; $k < count($arrayCountedBooksInOrder); $k++) {
					for ($i = $general_index; $i < $general_index + $arrayCountedBooksInOrder[$k][0]; $i++) {
						echo ("<tr>");
						for ($j = 0; $j < count($arraySelect[$i]); $j++) {
							echo ("<td>" . $arraySelect[$i][$j] . "</td>");
						}
						$private_sum++;
						echo ("</tr>");
					}
					$general_index += $arrayCountedBooksInOrder[$k][0];
					$general_sum += $private_sum;
					echo ("<tr> <th colspan = \"7\" style = \"background-color:#777777;\">" . "Количество книг в заказе " . $arrayOrderNumbers[$k][0] .
						"</th> <th colspan = \"2\" style = \"background-color:#555555;\">" . $private_sum . "</th> </tr>");
					$private_sum = 0;
				}
				echo ("<tr> <th colspan = \"7\" style = \"background-color:#777777;\">" . "Суммарное количество книг в заказах" .
					"</th> <th colspan = \"2\" style = \"background-color:#555555;\">" . $general_sum . "</th> </tr>");
				echo ("</tbody>");
				echo ("</table>");

				break;


			case 'reportOrderNum':

				if (isset($_POST['new0']) and isset($_POST['new1']) and isset($_POST['new2'])) {
					$new0 = $_POST["new0"];
					$new1 = $_POST["new1"];
					$new2 = $_POST["new2"];
					$err = "";
					$err = ValidateData($new0);
					if ($err != "")
						echo ($err);
					else {
						$query = "SELECT 
								`Книги заказа`.`№ заказа`,
								`Книги заказа`.`ISBN`,
								`Книги`.`Название книги`,
								`Книги`.`Год издания`,
								`Книги`.`Цена, руб.`,
								`Заказы`.`Дата заказа`,
								`Заказы`.`Дата выполнения заказа`
								FROM `Книги заказа` 
								INNER JOIN `Книги` ON `Книги заказа`.`ISBN` = `Книги`.`ISBN`
								INNER JOIN `Заказы` ON `Книги заказа`.`№ заказа` = `Заказы`.`№ заказа`
								WHERE `Книги заказа`.`№ заказа` REGEXP '$new0'
								AND `Книги`.`Название книги` REGEXP '$new1'
								AND `Книги`.`Год издания` REGEXP '$new2';";
						$result = $conn->query($query);

						$arraySelect = $result->fetch_all();

						$sum = 0;

						echo ("<table border = \"3\">");
						echo ("<caption>" . "Фильтрация по введённым данным" . "</caption>");
						echo ("<tbody> <tr>");
						echo ("<th>" . "№ заказа" . "</th> <th>" . "ISBN" . "</th> <th>" . "Название книги". "</th> <th>" . "Год издания". "</th> <th>" . "Цена, руб.". "</th> <th>" . "Дата заказа" . "</th> <th>" . "Дата выполнения заказа" . "</th>");
						echo ("</tr>");
						for ($i = 0; $i < count($arraySelect); $i++) {
							echo ("<tr>");
							for ($j = 0; $j < count($arraySelect[$i]); $j++) {
								echo ("<td id = " . "txtC>" . $arraySelect[$i][$j] . "</td>");
							}
							$sum += 1;
							echo ("</tr>");
						}
						echo ("<tr> <th colspan = \"6\" style = \"background-color: #777777;\">" . "Суммарное количество книг" .
							"</th> <th colspan = \"1\" style = \"background-color: #555555;\">" . $sum . "</th> </tr>");
						echo ("</tbody>");
						echo ("</table>");
					}
				}
				break;
		}
	}
	$conn->close();
	?>
</body>

</html>
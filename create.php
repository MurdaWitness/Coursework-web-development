<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<title> Создание базы данных </title>

	<?php
        function DataAdded($n){
			// правильное склонение слов "добавлена запись"
			$n1 = $n % 10;  // последняя цифра
			$n2 = $n % 100; // последние 2-е цифры
			if ($n2 >= 5 && $n2 <= 20 || $n1 == 0 || $n1 >= 5 && $n1 <= 9)
				$s = "добавлено " . $n . " записей";
			elseif ($n1 == 1)
				$s = "добавлена " . $n . " запись";
			else
				$s = "добавлено " . $n . " записи";
			echo "<p>В таблицу " . $s . "</p>\n";
        }
    ?>

</head>

<body>
	<?php
	require_once "login.php";
	$conn = new mysqli($server, $user, $password, $dbname);

	if ($conn->connect_error)
		echo "<p>Невозможно подключиться к серверу или открыть БД:<br>\n" . $conn->connect_error . "</p>\n";
	else {

		$query = "DROP TABLE IF EXISTS `Книги заказа`;";
		$result = $conn->query($query);
		if (!$result)
			echo "<p>Невозможно удалить таблицу \"Книги заказа\":<br>\n" . $conn->error . "</p>\n";
		else {

			$query = "DROP TABLE IF EXISTS `Книги`;";
			$result = $conn->query($query);
			if (!$result)
				echo "<p>Невозможно удалить таблицу \"Книги\":<br>\n" . $conn->error . "</p>\n";
			else {

				$query = "DROP TABLE IF EXISTS `Заказы`;";
				$result = $conn->query($query);
				if (!$result)
					echo "<p>Невозможно удалить таблицу \"Заказы\":<br>\n" . $conn->error . "</p>\n";
				else {

					//Создание и заполнение таблицы Книги
					$query = "CREATE TABLE `Книги`(" .
						"`ISBN` VARCHAR(15) PRIMARY KEY NOT NULL," .
						"`ФИО автора` VARCHAR(50) NOT NULL," .
						"`Название книги` VARCHAR(30) NOT NULL," .
						"`Год издания` INT(4) NOT NULL," .
						"`Цена, руб.` INT(10) NOT NULL);";
					$result = $conn->query($query);
					if (!$result)
						echo "<p>Невозможно создать таблицу \"Книги\":<br>\n" . $conn->error . "</p>\n";
					else {
						echo "<p>Таблица \"Книги\" создана\n";
						$query = "INSERT IGNORE INTO `Книги`(`ISBN`, `ФИО автора`, `Название книги`, `Год издания`, `Цена, руб.`)" .
							"values ('978-5-388-00003','Иванов Сергей Степанович','Самоучитель JAVA',2019,300)," .
							"('978-5-699-58103','Сидорова Ольга Юрьевна','JAVA за 21 день',2020,600)," .
							"('758-3-004-87105','Петров Иван Петрович','Сопромат',2019,350)," .
							"('758-3-057-37854','Иванов Сергей Степанович','Механика',2018,780)," .
							"('675-3-423-00375', 'Петров Иван Петрович','Физика',2020,450)";
						$result = $conn->query($query);
						if (!$result)
							echo "<p>Невозможно добавить записи в таблицу \"Книги\":<br>\n" . $conn->error . "</p>\n";
						else {
							DataAdded($conn->affected_rows);
						}
					}
					
					//Создание и заполнение таблицы Заказы
					$query = "CREATE TABLE `Заказы`(" .
						"`№ заказа` INT(6) PRIMARY KEY NOT NULL," .
						"`Адрес доставки` VARCHAR(50) NOT NULL," .
						"`Дата заказа` VARCHAR(10) NOT NULL," .
						"`Дата выполнения заказа` VARCHAR(10) NOT NULL);";
					$result = $conn->query($query);
					if (!$result)
						echo "<p>Невозможно создать таблицу \"Заказы\":<br>\n" . $conn->error . "</p>\n";
					else {
						echo "<p>Таблица \"Заказы\" создана\n";
						$query = "INSERT IGNORE INTO `Заказы`(`№ заказа`, `Адрес доставки`, `Дата заказа`, `Дата выполнения заказа`)" .
							"values (123456, 'Малая Арнаутская ул., д.9,кВ.16 Иванов Игорь','20.12.2019','22.12.2019')," .
							"(222334, 'Курчатов бульвар, д.33,кВ.9 Петрова Светлана','21.02.2020','-')," .
							"(432152, 'Нахимовский проспект, д.12,кВ.89 Васин Иван','11.01.2020','23.01.2020')";
						$result = $conn->query($query);
						if (!$result)
							echo "<p>Невозможно добавить записи в таблицу \"Заказы\":<br>\n" . $conn->error . "</p>\n";
						else {
							DataAdded($conn->affected_rows);
						}
					}

					//Создание и заполнение таблицы Книги заказа
					$query = "CREATE TABLE `Книги заказа`(" .
						"`№ заказа` INT(6) NOT NULL," .
						"`ISBN` VARCHAR(15) NOT NULL," .
						"FOREIGN KEY(`№ заказа`) REFERENCES `Заказы` (`№ заказа`) ON DELETE CASCADE ON UPDATE CASCADE," .
						"FOREIGN KEY(`ISBN`) REFERENCES `Книги` (`ISBN`) ON DELETE CASCADE ON UPDATE CASCADE);";
					$result = $conn->query($query);
					if (!$result)
						echo "<p>Невозможно создать таблицу \"Книги заказа\":<br>\n" . $conn->error . "</p>\n";
					else {
						echo "<p>Таблица \"Книги заказа\" создана\n";
						$query = "INSERT IGNORE INTO `Книги заказа`(`№ заказа`, `ISBN`)".
							"values (123456, '978-5-388-00003'),".
							"(123456, '978-5-699-58103'),".
							"(432152, '978-5-388-00003'),".
							"(222334, '978-5-388-00003'),".
							"(222334, '675-3-423-00375')";
							
						$result = $conn->query($query);
						if (!$result)
							echo "<p>Невозможно добавить записи в таблицу \"Книги заказа\":<br>\n" . $conn->error . "</p>\n";
						else {
							DataAdded($conn->affected_rows);
						}
					}

				}
			}
		}
		$conn->close();
	}
	?>
</body>

</html>
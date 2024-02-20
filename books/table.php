<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
    <title> Таблица "Книги"</title>
    <?php
        function ValidateData($new0, $new1, $new2, $new3, $new4){
            $errorText = "";
			
			$data = $new0;
			$isbnPattern = '/^\d{3}-\d{1}-\d{3}-\d{5}$/'; 
			if (!preg_match($isbnPattern, $data)) {
				$errorText .= "Поле \"ISBN\" должно соответствовать паттерну 999-9-999-99999". "<br>";
			}

			$data =  $new1;
			$fioPattern = '/^[а-яА-ЯёЁ\s]{1,50}$/u';
			if (!preg_match($fioPattern, $data) || empty($data) || mb_strlen($data) > 50) {
				$errorText .= "Поле \"ФИО автора\" должно содержать только русские буквы, пробелы, и быть длиной от 1 до 50 символов". "<br>";
			}

			$data = $new2;
			$titlePattern = '/^[0-9a-zA-Zа-яА-Я\s]{1,30}$/u';
			if (!preg_match($titlePattern, $data) || empty($data) || mb_strlen($data) > 30) {
				$errorText .= "Поле \"Название книги\" должно содержать только буквы, цифры, пробелы, и быть длиной от 1 до 30 символов". "<br>";
			}

			$data = intval($new3);
			if (empty($data) || $data < 0) {
				$errorText .= "Поле \"Год издания\" должно быть неотрицательным числом". "<br>";
			}

			$data = intval($new4);
			if (empty($data) || $data < 0) {
				$errorText .= "Поле \"Цена, руб.\" должно быть неотрицательным числом". "<br>";
			}
            return $errorText;
        }
    ?>
</head>
<body>
    <?php
        require_once "../login.php";
        $conn = new mysqli($server, $user, $password, $dbname);
        if($conn ->connect_error) 
			echo "<p>Невозможно подключиться к серверу или открыть БД: $conn->connect_error </p>\n";
		else {
			if(isset($_POST["choose_but"]) and isset($_POST["new0"]) and isset($_POST["new1"]) and isset($_POST["new2"])and isset($_POST["new3"])and isset($_POST["new4"]))
			{
				$button = $_POST["choose_but"];
				$new0 = $_POST["new0"];
				$new1 = $_POST["new1"];
				$new2 = $_POST["new2"];
				$new3 = $_POST["new3"];
				$new4 = $_POST["new4"];
				$err = "";
				$err = ValidateData($new0, $new1, $new2, $new3, $new4);
				if($err != "") echo($err);
				else{
					switch ($button)
					{
						case 'insert':
							try{
								$query= "INSERT INTO `Книги`(`ISBN`, `ФИО автора`, `Название книги`, `Год издания`, `Цена, руб.`)"."values ('$new0', '$new1', '$new2', '$new3', '$new4')";	
								$result=$conn->query($query);

								if (!$result)
									echo "<p>Невозможно добавить записи в таблицу \"Книги\" $conn->error </p>\n";
								break;
							}
							catch(Exception $e){
								if($e->getCode() == 1062){
									echo("Попытка добавить дубликат имеющейся записи со значением ключевого поля " . "\"$new0\"" . "<br>");
								}
							}
						break;
						
						case 'update':
							if(isset($_POST["old0"]) and isset($_POST["old1"]) and isset($_POST["old2"]) and isset($_POST["old3"])and isset($_POST["old4"]))
							{
								$old0 = $_POST["old0"];
								$old1 = $_POST["old1"];
								$old2 = $_POST["old2"];
								$old3 = $_POST["old3"];
								$old4 = $_POST["old4"];
								
								try{
									$query= "UPDATE `Книги`".
									"SET `ISBN` = '$new0', `ФИО автора` = '$new1', `Название книги` = '$new2',`Год издания` = '$new3',`Цена, руб.` = '$new4'".
									"WHERE `ISBN` = '$old0' AND `ФИО автора` = '$old1' AND `Название книги` = '$old2' AND `Год издания` = '$old3' AND `Цена, руб.` = '$old4'";

									$result=$conn->query($query);
									
									if (!$result)
										echo "<p>Невозможно обновить записи в таблицы \"Книги\" $conn->error </p>\n";
									break;
								}
								catch(Exception $e){
									if($e->getCode() == 1062 or $e->getCode() == 1761){
										echo("Попытка сделать дубликат уже имеющиейся записи" . "<br>");
									}
								}
							}
						break;
						
						case 'delete':
							try{
								$query="DELETE FROM `Книги` ".
								"WHERE `ISBN` = '$new0' AND `ФИО автора` = '$new1' AND `Название книги` = '$new2' AND `Год издания` = '$new3' AND `Цена, руб.` = '$new4'";   
								
								$result=$conn->query($query);
								if (!$result)
								echo "<p>Невозможно удалить записи из таблицы \"Книги\": $conn->error </p>\n";
							break;
							}
							catch(Exception $e){
								if($e->getCode() == 1451){
									echo("Нельзя удалить запись, так как часть ее данных находится в дочерних таблицах" . "<br>");
								}
							}
						break;				
					}
				}
			}
			
			$query = "SELECT `ISBN`, `ФИО автора`, `Название книги`, `Год издания`, `Цена, руб.` FROM `Книги`";
			$result=$conn->query($query);
			if (!$result)
				echo "<p>Невозможно просмотреть записи из таблицы \"Книги\":<br>\n".$conn->error."</p>\n";
			else {
				echo "<table cellspacing=0 cellpadding=8 border=1>\n";
				echo "\t<caption>Книги</caption>\n";
				echo "\t<tbody style='text-align:center'>\n";	 
				echo "\t\t<tr><th>ISBN</th><th>ФИО автора</th><th>Название книги</th><th>Год издания</th><th>Цена, руб.</th></tr>\n";
				while ($row=$result->fetch_array(MYSQLI_NUM)) 
				{
					echo "\t\t<tr><td>$row[0]</td><td class='left'>$row[1]</td><td>$row[2]</td><td>$row[3]</td><td>$row[4]</td></tr>\n";
				}
				echo "\t</tbody>\n";
				echo "</table>\n";
			}
		}
        $conn->close();
    ?>
</body>
</html>
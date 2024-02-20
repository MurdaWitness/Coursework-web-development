<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
    <title> Таблица "Заказы"</title>
    <?php
        function ValidateData($new0, $new1, $new2, $new3){
            $errorText = "";
			
			$data = intval($new0);
			if (empty($data) || $data < 0) {
				$errorText .= "Поле \"№ заказа\" должно быть положительным числом". "<br>";
			}
			
			$data = $new1;
			$adressPattern = '/^[0-9а-яА-Я\s.,]{1,50}$/u';
			if (!preg_match($adressPattern, $data)) {
				$errorText .= "Поле \"Адрес доставки\" должно содержать только русские буквы, цифры, пробелы, знаки пунктуации и быть длиной от 1 до 50 символов". "<br>";
			}

			$data =  $new2;
			$datePattern = '/^(0[1-9]|[12][0-9]|3[01])\.(0[1-9]|1[0-2])\.\d{4}$/';
			if (!preg_match($datePattern, $data)) {
				$errorText .= "Поле \"Дата заказа\" должно соответствовать паттерну 99.99.9999". "<br>";
			}

			$data =  $new3;
			$datePattern = '/^(0[1-9]|[12][0-9]|3[01])\.(0[1-9]|1[0-2])\.\d{4}$/';
			if (!preg_match($datePattern, $data)) {
				$errorText .= "Поле \"Дата выполнения заказа\" должно соответствовать паттерну 99.99.9999". "<br>";
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
			if(isset($_POST["choose_but"]) and isset($_POST["new0"]) and isset($_POST["new1"]) and isset($_POST["new2"])and isset($_POST["new3"]))
			{
				$button = $_POST["choose_but"];
				$new0 = $_POST["new0"];
				$new1 = $_POST["new1"];
				$new2 = $_POST["new2"];
				$new3 = $_POST["new3"];
				$err = "";
				$err = ValidateData($new0, $new1, $new2, $new3);
				if($err != "") echo($err);
				else{
					switch ($button)
					{
						case 'insert':
							try{
								$query= "INSERT INTO `Заказы`(`№ заказа`, `Адрес доставки`, `Дата заказа`, `Дата выполнения заказа`)"."values ('$new0', '$new1', '$new2', '$new3')";	
								$result=$conn->query($query);

								if (!$result)
									echo "<p>Невозможно добавить записи в таблицу \"Заказы\" $conn->error </p>\n";
								break;
							}
							catch(Exception $e){
								if($e->getCode() == 1062){
									echo("Попытка добавить дубликат имеющейся записи со значением ключевого поля " . "\"$new0\"" . "<br>");
								}
							}
						break;
						
						case 'update':
							if(isset($_POST["old0"]) and isset($_POST["old1"]) and isset($_POST["old2"]) and isset($_POST["old3"]))
							{
								$old0 = $_POST["old0"];
								$old1 = $_POST["old1"];
								$old2 = $_POST["old2"];
								$old3 = $_POST["old3"];
								
								try{
									$query= "UPDATE `Заказы`".
									"SET `№ заказа` = '$new0', `Адрес доставки` = '$new1', `Дата заказа` = '$new2',`Дата выполнения заказа` = '$new3'".
									"WHERE `№ заказа` = '$old0' AND `Адрес доставки` = '$old1' AND `Дата заказа` = '$old2' AND `Дата выполнения заказа` = '$old3'";

									$result=$conn->query($query);
									
									if (!$result)
										echo "<p>Невозможно обновить записи в таблицы \"Заказы\" $conn->error </p>\n";
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
								$query="DELETE FROM `Заказы` ".
								"WHERE `№ заказа` = '$new0' AND `Адрес доставки` = '$new1' AND `Дата заказа` = '$new2' AND `Дата выполнения заказа` = '$new3'";   
								
								$result=$conn->query($query);
								if (!$result)
								echo "<p>Невозможно удалить записи из таблицы \"Заказы\": $conn->error </p>\n";
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
			
			$query = "SELECT `№ заказа`, `Адрес доставки`, `Дата заказа`, `Дата выполнения заказа` FROM `Заказы`";
			$result=$conn->query($query);
			if (!$result)
				echo "<p>Невозможно просмотреть записи из таблицы \"Заказы\":<br>\n".$conn->error."</p>\n";
			else {
				echo "<table cellspacing=0 cellpadding=8 border=1>\n";
				echo "\t<caption>Заказы</caption>\n";
				echo "\t<tbody style='text-align:center'>\n";	 
				echo "\t\t<tr><th>№ заказа</th><th>Адрес доставки</th><th>Дата заказа</th><th>Дата выполнения заказа</th></tr>\n";
				while ($row=$result->fetch_array(MYSQLI_NUM)) 
				{
					echo "\t\t<tr><td>$row[0]</td><td class='left'>$row[1]</td><td>$row[2]</td><td>$row[3]</td></tr>\n";
				}
				echo "\t</tbody>\n";
				echo "</table>\n";
			}
		}
        $conn->close();
    ?>
</body>
</html>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
    <title> Таблица "Книги заказа"</title>
    <?php
        function ValidateData($new0, $new1){
            $errorText = "";
			
			$data = intval($new0);
			if (empty($data) || $data < 0) {
				$errorText .= "Поле \"№ заказа\" должно быть положительным числом". "<br>";
			}
			
			$data = $new1;
			$isbnPattern = '/^\d{3}-\d{1}-\d{3}-\d{5}$/'; 
			if (!preg_match($isbnPattern, $data)) {
				$errorText .= "Поле \"ISBN\" должно соответствовать паттерну 999-9-999-99999". "<br>";
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
			if(isset($_POST["choose_but"]) and isset($_POST["new0"]) and isset($_POST["new1"]))
			{
				$button = $_POST["choose_but"];
				$new0 = $_POST["new0"];
				$new1 = $_POST["new1"];
				$err = "";
				$err = ValidateData($new0, $new1);
				if($err != "") echo($err);
				else{
					switch ($button)
					{
						case 'insert':
							try{
								$query= "INSERT INTO `Книги заказа`(`№ заказа`, `ISBN`)"."values ('$new0', '$new1')";	
								$result=$conn->query($query);

								if (!$result)
									echo "<p>Невозможно добавить записи в таблицу \"Книги заказа\" $conn->error </p>\n";
								break;
							}
							catch(Exception $e){
								if($e->getCode() == 1062){
									echo("Попытка добавить дубликат имеющейся записи со значением ключевого поля " . "\"$new0\"" . "<br>");
								}
							}
						break;
						
						case 'update':
							if(isset($_POST["old0"]) and isset($_POST["old1"]))
							{
								$old0 = $_POST["old0"];
								$old1 = $_POST["old1"];
								
								try{
									$query= "UPDATE `Книги заказа`".
									"SET `№ заказа` = '$new0', `ISBN` = '$new1'".
									"WHERE `№ заказа` = '$old0' AND `ISBN` = '$old1'";

									$result=$conn->query($query);
									
									if (!$result)
										echo "<p>Невозможно обновить записи в таблицы \"Книги заказа\" $conn->error </p>\n";
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
								$query="DELETE FROM `Книги заказа` ".
								"WHERE `№ заказа` = '$new0' AND `ISBN` = '$new1'";   
								
								$result=$conn->query($query);
								if (!$result)
								echo "<p>Невозможно удалить записи из таблицы \"Книги заказа\": $conn->error </p>\n";
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
			
			$query = "SELECT `№ заказа`, `ISBN` FROM `Книги заказа`";
			$query = "SELECT 
						`Книги заказа`.`№ заказа`,
						`Книги заказа`.`ISBN`,
						`Книги`.`Название книги`
					FROM `Книги заказа` 
					INNER JOIN `Книги` ON `Книги`.`ISBN` = `Книги заказа`.`ISBN`;";
			$result=$conn->query($query);
			if (!$result)
				echo "<p>Невозможно просмотреть записи из таблицы \"Книги заказа\":<br>\n".$conn->error."</p>\n";
			else {
				echo "<table cellspacing=0 cellpadding=8 border=1>\n";
				echo "\t<caption>Книги заказа</caption>\n";
				echo "\t<tbody style='text-align:center'>\n";	 
				echo "\t\t<tr><th>№ заказа</th><th>ISBN</th><th>Название книги</th></tr>\n";
				while ($row=$result->fetch_array(MYSQLI_NUM)) 
				{
					echo "\t\t<tr><td>$row[0]</td><td>$row[1]</td><td>$row[2]</td></tr>\n";
				}
				echo "\t</tbody>\n";
				echo "</table>\n";
			}
		}
        $conn->close();
    ?>
</body>
</html>
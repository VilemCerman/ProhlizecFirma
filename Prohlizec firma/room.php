<?php
$id = filter_input(INPUT_GET,
    'roomId',
    FILTER_VALIDATE_INT,
    ["options" => ["min_range"=> 1]]
);

if ($id === null || $id === false) {
    http_response_code(400);
    $status = "bad_request";
} else {

    require_once "inc/db.inc.php";

    $stmt = $pdo->prepare("SELECT *, employee.name as employee_name, room.name as room_name FROM room LEFT JOIN employee ON room.room_id = employee.room WHERE room.room_id=:roomId");
    $stmt->execute(['roomId' => $id]);
    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        $status = "not_found";
    } else {
        $room = $stmt->fetch();
        $status = "OK";
    }

    $stmtAvg = $pdo->prepare("SELECT AVG(wage) as 'avg' FROM employee WHERE room = :roomId");
    $stmtAvg->execute(['roomId' => $id]);
    $avg = $stmtAvg->fetch();

    $stmtKeys = $pdo->prepare("SELECT employee, surname, name, employee_id FROM `key` INNER JOIN employee ON `key`.room = :roomId AND `key`.employee = employee.employee_id ORDER BY surname");
    $stmtKeys->execute(['roomId' => $id]);
    $key = $stmtKeys->fetch();
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
<!--    <meta name="viewport"-->
<!--          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">-->
<!--    <meta http-equiv="X-UA-Compatible" content="ie=edge">-->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <title><?php echo "Místnost č. ".$room->no; ?></title>
</head>
<body class="container">
<?php
switch ($status) {
    case "bad_request":
        echo "<h1>Error 400: Bad request</h1>";
        break;
    case "not_found":
        echo "<h1>Error 404: Not found</h1>";
        break;
    default:
        //var_dump($room);
        echo "<h1>Místnost č. {$room->no}</h1>";
        echo "<dl>";
        echo "<dt>Číslo</dt>";
        echo "<dd>{$room->no}</dd>";
        echo "<dt>Název</dt>";
        echo "<dd>{$room->room_name}</dd>";
        echo "<dt>Telefon</dt>";
        echo "<dd>{$room->phone}</dd>";
        echo "<dt>Lidé</dt>";
        if($room->surname === null){
            echo '<dd>---</dd>';
            echo "<dt>Průměrná mzda</dt>";
            echo '<dd>---</dd>';
        }else {
            do {
                echo "<a href='employee.php?employeeId={$room->employee_id}'><dd>{$room->surname} " . substr($room->employee_name, 0, 1) . ".</dd></a>";
            } while ($room = $stmt->fetch());
            echo "<dt>Průměrná mzda</dt>";
            echo "<dd>".round($avg->avg, 0).'</dd>';
        }
        echo "<dt>Klíče</dt>";
        do{
            echo "<a href='employee.php?employeeId={$key->employee_id}'><dd>{$key->surname} ".substr($key->name,0,1).".</dd></a>";
        }while($key = $stmtKeys->fetch());
        break;
}
echo "</br><a href='rooms.php'>Zpět na seznam místností</a>";

?>
</body>
</html>
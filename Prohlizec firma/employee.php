<?php
$employeeId = filter_input(INPUT_GET,
    'employeeId',
    FILTER_VALIDATE_INT,
    ["options" => ["min_range"=> 1]]
);


if ($employeeId === null || $employeeId === false) {
    http_response_code(400);
    $status = "bad_request";
} else {

    require_once "inc/db.inc.php";
    $stmt = $pdo->prepare("SELECT employee.surname, employee.name, employee.job, employee.wage,room.room_id, room.name as room_name FROM employee INNER JOIN room ON employee.room = room.room_id AND employee_id=:employee_Id");
    $stmt->execute(['employee_Id' => $employeeId]);
    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        $status = "not_found";
    } else {
        $employee = $stmt->fetch();
        $status = "OK";
    }
    $stmtKeys = $pdo->prepare("SELECT `key`.room,room.name as room_name FROM `key` INNER JOIN room ON `key`.room = room.room_id AND `key`.employee=:employee_Id ORDER BY room_name");
    //$stmtKeys = $pdo->prepare("SELECT `key`.room,room.name as room_name FROM `key` RIGHT JOIN room ON `key`.room = room.room_id WHERE `key`.employee=:employee_Id OR  ORDER BY room_name");
    $stmtKeys->execute(['employee_Id' => $employeeId]);
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
    <title><?php echo 'Zaměstnanec '.$employee->surname.' '.$employee->name; ?></title>
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
        echo "<h1>Zaměstnanec <i>{$employee->surname} ".substr($employee->name,0,1).".</i></h1>";
        echo "<dl>";
        echo "<dt>Jméno</dt>";
        echo "<dd>{$employee->name}</dd>";
        echo "<dt>Příjmení</dt>";
        echo "<dd>{$employee->surname}</dd>";
        echo "<dt>Pozice</dt>";
        echo "<dd>{$employee->job}</dd>";
        echo "<dt>Mzda</dt>";
        echo "<dd>{$employee->wage}</dd>";
        echo "<dt>Místnost</dt>";
        echo "<dd><a href='room.php?roomId={$employee->room_id}'>{$employee->room_name}</a></dd>";
        echo "<dt>Klíče</dt>";
        while ($row=$stmtKeys->fetch()){
            echo "<dd><a href='room.php?roomId={$row->room}'>{$row->room_name}</a></dd>";
        }
        echo "</dl>";

        break;
}
echo "<br><a href='employees.php'>Zpět na seznam zaměstnanců</a>";
?>
</body>
</html>
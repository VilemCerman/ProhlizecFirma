<!DOCTYPE html>

<html lang="cs">
<head>
    <meta charset="UTF-8">
    <!-- Bootstrap-->
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
    <title>Seznam zaměstnanců</title>
</head>
<body class="container">
<?php

$input = filter_input(INPUT_GET,'sortby');
$filter = '';
$direction = '';
if($input !== null && $input !== false){
    $filter = substr($input,0,strpos($input, '_'));
    $direction = substr($input,strpos($input, '_')+1);
}
if($direction !== 'asc' && $direction !== 'desc'){
    $direction = 'asc';
}
if($filter === 'name' || ($filter !== 'room' && $filter !== 'phone' && $filter !== 'job')){
    $filter = "surname $direction, name";
}



require_once "inc/db.inc.php";

$stmt = $pdo->prepare('SELECT employee.employee_id,  employee.name, employee.surname, employee.job, room.phone, room.name as room FROM employee INNER JOIN room ON employee.room = room.room_id ORDER BY '.$filter.' '.$direction);
$stmt->execute();

echo "<h1>Seznam zaměstnanců</h1>";
//echo "Počet řádků: " . $stmt->rowCount() . "<br>";

if ($stmt->rowCount() == 0) {
    echo "Záznam neobsahuje žádná data";
} else {
    echo "<table class='table table-striped'>";
    echo "<tr>";
    echo "<th>Jméno <a href='?sortby=name_asc'><span class='glyphicon glyphicon-arrow-up' aria-hidden='true'></a> <a href='?sortby=name_desc'><span class='glyphicon glyphicon-arrow-down' aria-hidden='true'></a></th><th>Místnost <a href='?sortby=room_asc'><span class='glyphicon glyphicon-arrow-up' aria-hidden='true'></a> <a href='?sortby=room_desc'><span class='glyphicon glyphicon-arrow-down' aria-hidden='true'></a></th>
<th>Telefon <a href='?sortby=phone_asc'><span class='glyphicon glyphicon-arrow-up' aria-hidden='true'></a> <a href='?sortby=phone_desc'><span class='glyphicon glyphicon-arrow-down' aria-hidden='true'></a></th><th>Pozice <a href='?sortby=job_asc'><span class='glyphicon glyphicon-arrow-up' aria-hidden='true'></a> <a href='?sortby=job_desc'><span class='glyphicon glyphicon-arrow-down' aria-hidden='true'></a></th>";
    echo "</tr>";
    while ($row = $stmt->fetch()) {
        echo "<tr>";
        echo "<td><a href='employee.php?employeeId={$row->employee_id}'>{$row->surname} {$row->name}</a></td><td>{$row->room}</td><td>{$row->phone}</td><td>{$row->job}</td>";
        echo "</tr>";
    }
    echo "</table>";
}
echo "</br><a href='..\Prohlizec firma'>Zpět</a>";

unset($stmt);
?>
</body>
</html>

<!DOCTYPE html>

<html lang="cs">
<head>
    <meta charset="UTF-8">
    <!-- Bootstrap-->
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
    <title>Prohlížeč firmy</title>
</head>
<body class="container">
<?php

$input = filter_input(INPUT_GET,'sortby');
//if($input === null || $input === false){
//    $filter = 'name';
//    $direction = 'asc';
//    echo "input btl null";
//}
$filter = '';
$direction = '';
if($input !== null && $input !== false){
    $filter = substr($input,0,strpos($input, '_'));
    $direction = substr($input,strpos($input, '_')+1);
}
if($filter !== 'name' &&  $filter !== 'no' && $filter !== 'phone'){
    $filter = 'name';
}
if($direction !== 'asc' && $direction !== 'desc'){
    $direction = 'asc';
}

require_once "inc/db.inc.php";

$stmt = $pdo->prepare('SELECT * FROM room ORDER BY '.$filter.' '.$direction);
$stmt->execute();

echo "<h1>Seznam místností</h1>";
//echo "Počet řádků: " . $stmt->rowCount() . "<br>";

if ($stmt->rowCount() == 0) {
    echo "Záznam neobsahuje žádná data";
} else {
    echo "<table class='table table-striped'>";
    echo "<tr>";
    echo "<th>Název <a href='?sortby=name_asc'><span class='glyphicon glyphicon-arrow-up' aria-hidden='true'></span></a> <a href='?sortby=name_desc'><span class='glyphicon glyphicon-arrow-down' aria-hidden='true'></a></th><th>Číslo <a href='?sortby=no_asc'><span class='glyphicon glyphicon-arrow-up' aria-hidden='true'></a> <a href='?sortby=no_desc'><span class='glyphicon glyphicon-arrow-down' aria-hidden='true'></a></th><th>Telefon <a href='?sortby=phone_asc'><span class='glyphicon glyphicon-arrow-up' aria-hidden='true'></a> <a href='?sortby=phone_desc'><span class='glyphicon glyphicon-arrow-down' aria-hidden='true'></a></th>";
    echo "</tr>";
    while ($row = $stmt->fetch()) {
        echo "<tr>";
        echo "<td><a href='room.php?roomId={$row->room_id}'>{$row->name}</a></td><td>{$row->no}</td><td>{$row->phone}</td>";
        echo "</tr>";
    }
    echo "</table>";
}
echo "</br><a href='..\Prohlizec firma'>Zpět</a>";
unset($stmt);
?>
</body>
</html>
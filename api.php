<?php


try {
    $dbh = new PDO('mysql:dbname=capteurbh1750;host=localhost;charset=utf8', 'root', '');
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$lux = $_GET['valeur'] ?? null;

if ($lux !== null && is_numeric($lux)) {
    $stmt = $dbh->prepare("INSERT INTO lux_readings (lux, recorded_at) VALUES (?, NOW())");
    $stmt->execute([$lux]);
    echo "OK - $lux lux saved";
} else {
    echo "Invalid data";
}
?>

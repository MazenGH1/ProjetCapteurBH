<?php

// Connexion à la bdd
try {
    $dbh = new PDO('mysql:dbname=capteurbh1750;host=localhost;charset=utf8', 'root', '');
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Si la bdd est éteinte on arrete et on affiche l'erreur
    die("Connection failed: " . $e->getMessage());
}

// Recuperation de la donnée envoyee par le Pico dans l url par le fichier python
$lux = $_GET['valeur'] ?? null;

// On verifie que la valeur n est pas vide et que c est bien un nombre
if ($lux !== null && is_numeric($lux)) {
    // On prépare la requête et NOW() genere automatiquement l heure actuelle
    $stmt = $dbh->prepare("INSERT INTO lux_readings (lux, recorded_at) VALUES (?, NOW())");
    $stmt->execute([$lux]);
    //Accuse de reception pour rassurer le Pico
    echo "OK - $lux lux saved";
} else {
    echo "Invalid data";
}
?>

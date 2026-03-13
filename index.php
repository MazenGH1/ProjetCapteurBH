<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="5"/>
    <title>Capteur de Lumiere</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    

    <h1>Dernières mesures de lumière</h1>
    <?php
    try {
        $dbh = new PDO('mysql:dbname=capteurbh1750;host=localhost;charset=utf8', 'root', '');
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Fetch the 10 most recent readings
        $stmt = $dbh->query("SELECT lux, recorded_at FROM lux_readings ORDER BY recorded_at DESC LIMIT 10");
        $readings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ($readings) {
            echo "<ul>";
            foreach ($readings as $row) {
                echo "<li>" . $row['recorded_at'] . " - <strong>" . $row['lux'] . " Lux</strong></li>";
            }
            echo "</ul>";
        } else {
            echo "<p>Aucune donnée disponible.</p>";
        }
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
    ?>
</body>

</html>
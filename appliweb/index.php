<!DOCTYPE html> 
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="5"/>
    <title>Capteur de Lumiere</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/png" href="../images/lightbulb_icon_188087.png">
    <script src="https://cdn.plot.ly/plotly-2.32.0.min.js" charset="utf-8"></script>
</head>
<body>
    <h1>Application pour un capteur de luminosite</h1>
   
    <h2>Dernières mesures de lumière</h2>

    <div id="luxGraph" style="width:80%; height:600px; margin: 20px auto;"></div>
   
    <?php
    try {
        $dbh = new PDO('mysql:dbname=capteurbh1750;host=localhost;charset=utf8', 'root', '');
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
       
  
        $stmt = $dbh->query("SELECT lux, recorded_at FROM lux_readings ORDER BY recorded_at DESC LIMIT 20");

        $stmt2 = $dbh->query("SELECT lux, recorded_at FROM lux_readings ORDER BY recorded_at DESC LIMIT 1");

        $readings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $readings2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);

       
        if ($readings) {
            
            $temps = [];
            $valeurs = [];

            
            $readings_for_graph = array_reverse($readings);

            echo "<ul>";
            foreach ($readings2 as $row2) {
                echo "<li>" . $row2['recorded_at'] . " - <strong>" . $row2['lux'] . " Lux</strong></li>";

                if ( $row2['lux'] < 50 ){
                    echo "Etat de led exterieur : <strong style='color:green;'> allume </strong><br><br>";
                } else {
                    echo "Etat de led exterieur : <strong style='color:red;'> eteinte </strong><br><br>";
                }
            }
            echo "</ul>";

         
            foreach ($readings_for_graph as $row) {
                $temps[] = date("H:i:s", strtotime($row['recorded_at']));
                $valeurs[] = $row['lux'];
            }

        } else {
            echo "<p>Aucune donnée disponible.</p>";
        }
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
    ?>

    <script>
        
        const abbsiceX = <?php echo json_encode($temps ?? []); ?>;
        const abbsiceY = <?php echo json_encode($valeurs ?? []); ?>;

        const data = [{
            x: abbsiceX,
            y: abbsiceY,
            type: 'scatter',
            mode: 'lines+markers',
            line: { color: 'red', width: 5 }
        }];

        const layout = {
            title: 'Luminosité (Lux) en temps réel',
            xaxis: { title: 'Heure' },
            yaxis: { 
                title: 'Valeur Lux' ,
                range : [0,300]
            }
            
        };

        Plotly.newPlot('luxGraph', data, layout);
    </script>

</body>
</html>


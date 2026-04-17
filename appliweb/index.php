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

<header>
<br>
<h1>Application pour un capteur de Luminosité</h1>
<br>
</header>
<body>

   
    <h2>Dernières mesures de lumière</h2>

    <div id="luxGraph" style="width:80%; height:600px; margin: 0px auto;"></div>
   
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
                    echo "Etat de led extérieure : <strong style='color:green;'> allumée </strong><br><br>";
                } else {
                    echo "Etat de led extérieure : <strong style='color:red;'> éteinte </strong><br><br>";
                }
            }

        $val = array_column($readings, 'lux');
        $datss= array_column($readings, 'recorded_at');

        $maxi=max($val);
        $indice_de_max = array_search($maxi, $val);
        $date_max = $readings[$indice_de_max]['recorded_at'];

        $mini=min($val);
        $indice_de_min = array_search($mini, $val);
        $date_min = $readings[$indice_de_min]['recorded_at'];
        
        $moy= array_sum($val) / count($val);

        $prem=reset($datss);
        $h1=date('H:i:s',strtotime($prem));

        $dernier=end($datss);
        $h2=date('H:i:s',strtotime($dernier));


        echo "Valeur maximale sur les 20 dernieres mesures <strong> ".$maxi."</strong>"." enregistré à: "."<strong> ".$date_max."</strong>"."<br/>";
        echo "Valeur minimale sur les 20 dernieres mesures <strong> ".$mini."</strong>"." enregistré à: "."<strong> ".$date_min."</strong>"."<br/>";
        echo "Valeur moyenne sur les 20 dernieres mesures <strong> ".$moy."</strong>"." enregistré entre: "."<strong>".$h2."</strong>"." et "."<strong>".$h1."</strong>" ."<br/>";


            
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

    
        const values = abbsiceY.map(Number);
        const sorted = [...values].sort((a, b) => a - b);
        const index90 = Math.floor(sorted.length * 0.9);
        let maxi = sorted[index90];

        if (!maxi || maxi < 10) {
            maxi = 300;
        }

        let yMax = maxi * 1.1;

        const data = [{
            x: abbsiceX,
            y: values,
            type: 'scatter',
            mode: 'lines+markers',
            line: { color: 'red', width: 5 }
        }];

        const layout = {
            title: 'Luminosité (Lux) en temps réel',
            xaxis: { title: 'Heure' },
            yaxis: { 
                title: 'Valeur Lux',
                range: [0, yMax]
            }
        };

        Plotly.newPlot('luxGraph', data, layout);
    </script>

</body>

</html>


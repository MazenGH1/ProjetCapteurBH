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
    
    <img src="../images/20260417_105850.jpg" alt="Mesure de lumière" style="display:block; margin:20px auto; max-width:600px;">
   
    <h2>Dernières mesures de lumière</h2>

    <div id="luxGraph" style="width:80%; height:600px; margin: 0px auto;"></div>  <!-- le graph sera ici --> 
   
    <?php
    try {
        $dbh = new PDO('mysql:dbname=capteurbh1750;host=localhost;charset=utf8', 'root', '');   // connexion a la db 
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
       
  
        $stmt = $dbh->query("SELECT lux, recorded_at FROM lux_readings ORDER BY recorded_at DESC LIMIT 20");   // 20 derniere valeurs principale pour l affchage du graph  + calcul moyen min max...

        $stmt2 = $dbh->query("SELECT lux, recorded_at FROM lux_readings ORDER BY recorded_at DESC LIMIT 1");   // juste pour afficher 1 valeur dans le html

        $readings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $readings2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);

       
        if ($readings) {
            
            $temps = [];
            $valeurs = [];

            
            $readings_for_graph = array_reverse($readings);

            echo "<ul>";
            foreach ($readings2 as $row2) {
                echo "<li>" . $row2['recorded_at'] . " - <strong>" . $row2['lux'] . " Lux</strong></li>";    // affichage de la derniere valeur avec sa date
                
                if ( $row2['lux'] < 50 ){                           // dans le script python on a mit une condition pour que la led extern s allume si la valeur du lux devient < 50 donc on indique ici sur le site elle est allume ou pas
                    echo "Etat de led extérieure : <strong style='color:green;'> allumée </strong><br><br>";
                } else {
                    echo "Etat de led extérieure : <strong style='color:red;'> éteinte </strong><br><br>";
                }
            }

        $val = array_column($readings, 'lux');  // tableau qui aura les valeurs du lux
        $datss= array_column($readings, 'recorded_at');    // tableau qui aura les datetime (date+heur)

        $maxi=max($val); //utilisation de max de php pour l afficher apres
        $indice_de_max = array_search($maxi, $val);       //extraction de son indice pour trouver la date associer a la val max
        $date_max = $readings[$indice_de_max]['recorded_at'];

        $mini=min($val);      // meme chose 
        $indice_de_min = array_search($mini, $val);
        $date_min = $readings[$indice_de_min]['recorded_at'];
        
        $moy= array_sum($val) / count($val);    // valeur moyenne , array_sum de php fait la somme et count de php fait le nombre d elements

        $prem=reset($datss);   // vue que dans le sql on a fait un ORDER BY recorded_at DESC LIMIT 20 le premier element du tableau sera la date la plus recente donc la fin 
        $h1=date('H:i:s',strtotime($prem)); // on veut juste l heur pas la date

        $dernier=end($datss); // ici le derniere element du tableau sera la date moins recente donc le debut
        $h2=date('H:i:s',strtotime($dernier));


        //affichage des stats
        echo "Valeur maximale sur les 20 dernieres mesures <strong> ".$maxi."</strong>"." enregistré à: "."<strong> ".$date_max."</strong>"."<br/>";
        echo "Valeur minimale sur les 20 dernieres mesures <strong> ".$mini."</strong>"." enregistré à: "."<strong> ".$date_min."</strong>"."<br/>";
        echo "Valeur moyenne sur les 20 dernieres mesures <strong> ".$moy."</strong>"." enregistré entre: "."<strong>".$h2."</strong>"." et "."<strong>".$h1."</strong>" ."<br/>";


            
            echo "</ul>";

            //Remplissage des listes pour le graphique Plotly
            foreach ($readings_for_graph as $row) {
                // On extrait uniquement l'heure (H:i:s)
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
     // Importation des données depuis PHP vers JavaScript
    const abbsiceX = <?php echo json_encode($temps ?? []); ?>;
    const abbsiceY = <?php echo json_encode($valeurs ?? []); ?>;
    
    //forcer la conversion des valeurs en nombres
    const values = abbsiceY.map(Number);

    //Calcul dynamique du plafond de l'axe y
    let maxi = Math.max(...values);

    // Si le capteur est dans le noir complet ou qu'il y a un bug, on force un plafond a 300
    if (!maxi || maxi < 10) {
        maxi = 300;
    }

    // marge de 15% au-dessus de la valeur max 
    let yMax = maxi * 1.15;

    // configuration de la ligne et des points
    const data = [{
        x: abbsiceX,
        y: values,
        type: 'scatter',
        mode: 'lines+markers',
        line: { color: 'red', width: 3 }
    }];

    // Configuration de la mise en page (titres + axes)
    const layout = {
        title: 'Luminosité (Lux) en temps réel',
        xaxis: { title: 'Heure' },
        yaxis: { 
            title: 'Valeur Lux',
            range: [0, yMax]   // commence de 0 avec un plafond dynamique
        }
    };

    // Dessin du graphique dans la balise div 'luxGraph'
    Plotly.newPlot('luxGraph', data, layout);
</script>

</body>

</html>


<?php 

//cacher les erreur
error_reporting(0);

// Inclure la bibliothèque Dotenv
require_once __DIR__ . '/vendor/autoload.php';

// Charger les variables d'environnement à partir du fichier .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Récupérer la clé API YouTube à partir des variables d'environnement
$api_key = $_ENV['YOUTUBE_API_KEY'];

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.84.0">
    <title>SONGBOX</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">

    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style>

    
    <!-- Custom styles for this template -->
  </head>
  <body>
    
<nav class="navbar sticky-top navbar-expand-md navbar-dark fixed-top bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Accueil</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
      <ul class="navbar-nav me-auto mb-2 mb-md-0">
        <li class="nav-item">
          <!-- input pour ajouter un son -->
          <input type="file" class="form-control" id="inputFile" onchange="uploadFile(this.files[0])" style="display: none;">
          <button class="btn btn-outline-success" type="button" onclick="document.getElementById('inputFile').click()">Ajouter un son</button>
        </li>
        <li class="nav-item">
          
        </li>
        <li class="nav-item">
          
        </li>
      </ul>
      <form class="d-flex">
        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
        <button class="btn btn-outline-success" type="submit">Search</button>
      </form>
    </div>
  </div>
</nav>

    <div class="p-3 pb-md-4 mx-auto text-center">
      <h1 class="display-4 fw-normal">SongBox</h1>
    </div>

    <div class="container">
        <div class="row">

        <?php 
        $songs = scandir('song');
        unset($songs[0], $songs[1]);
        //var_dump($songs);
        ?>
            <?php $i = 0; ?>
            <?php foreach ($songs as $song): ?>
            <?php
                
                $q = $song;

                //remplacer les espaces par des %20
                $q = str_replace(' ', '%20', $q);
                $q = str_replace('.mp3', '', $q);

                //lire le fichier songPicture.json
                $json = file_get_contents('songPicture.json');
                $json = json_decode($json, true);
                
                $namePicture = $song;
                $namePicture = str_replace('.mp3', '', $namePicture);
                

                if ( array_key_exists($namePicture, $json)) {
                  $thumbnail_url = $json[$namePicture];
                }else{
                  
                  // Effectuer une requête GET pour récupérer les informations de la vidéo
                  $url = "https://www.googleapis.com/youtube/v3/search?part=snippet&q=".$q."&key=".$api_key."&maxResults=1";

                  //var_dump($url);

                  $ch = curl_init();
                  curl_setopt($ch, CURLOPT_URL, $url);
                  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                  $response = curl_exec($ch);
                  //error
                  if($response === false){
                      //var_dump(curl_error($ch));
                  }
                  curl_close($ch);
                  $data = json_decode($response);
                  //print_r($data);

                  // Récupérer l'URL de la miniature
                  $thumbnail_url = $data->items[0]->snippet->thumbnails->medium->url;

                  //Ajouter l'image dans le fichier songPicture.json
                  $json[$namePicture] = $thumbnail_url;
                  $json = json_encode($json);
                  file_put_contents('songPicture.json', $json);

                }
            
            ?>


            <div class="col-sm-4 my-3">
                <div class="card" style="width: 18rem;">
                    <img src="<?= $thumbnail_url ?>" class="card-img-top" alt="...">
                    <div class="card-body">
                        <h5 class="card-title">
                            <?php $song = str_replace('.mp3', '', $song); ?>
                            <?= $song; ?>
                        </h5>
                        <!--<p class="card-text">text</p>   -->
                        <button class="btn btn-primary" onclick="playAudio(<?= $i ?>)">Lire l'audio</button>
                        <button class="btn btn-danger" onclick="stopAudio(<?= $i ?>)">Stop l'audio</button>
                        <audio id="myAudio-<?= $i ?>">
                            <source src="song/<?= $song ?>.mp3" type="audio/mpeg">
                            Votre navigateur ne prend pas en charge l'élément audio.
                        </audio>
                    </div>
                </div>
            </div>
            <?php $i++; ?>
            <?php endforeach; ?>
        </div>
    </div>


    <script>
        function playAudio($i) {
            var audio = document.getElementById("myAudio-"+$i);
            audio.play();
        }

        function stopAudio($i) {
            var audio = document.getElementById("myAudio-"+$i);
            audio.pause();
            audio.currentTime = 0;
        }

        //uploadFile function
        function uploadFile(file){
          
          //recuperer le fichier est le sauvegarder sur le serveur
          var xhr = new XMLHttpRequest();
          xhr.open('POST', 'upload.php', true);
          xhr.onload = function(){
            if(this.status == 200){
              console.log(this.responseText);
              //recharger la page
              window.location.reload();
            }
          }

          //pendant le chargement du fichier on affiche un message de chargement 
          xhr.upload.onprogress = function(event){
            var percent = Math.round((event.loaded / event.total) * 100);
            document.getElementById('progress').innerHTML = percent + '%';
          }
          var formData = new FormData();
          formData.append('file', file);
          xhr.send(formData);
        }

    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>

  </body>
</html>

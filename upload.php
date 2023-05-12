<?php

// Fichier upload.php qui permet de telecharger les fichiers mp3 sur le serveur 

$file = $_FILES['file']['name'];    
$file_tmp = $_FILES['file']['tmp_name'];
$file_type = $_FILES['file']['type'];

// On vérifie si le fichier est bien un fichier mp3
if($file_type == 'audio/mpeg'){
    // On déplace le fichier dans le dossier song
    move_uploaded_file($file_tmp, 'song/'.$file);
    // On récupère le nom du fichier
    $namePicture = str_replace('.mp3', '', $file);
}



?>
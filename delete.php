<?php

// Fichier delete.php qui permet de supprimer les fichiers mp3 sur le serveur

$file = $_GET['file'];

// On vérifie si le fichier existe
if(file_exists('song/'.$file)){
    // On supprime le fichier
    unlink('song/'.$file);
}

?>

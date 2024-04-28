<?php
// Assurez-vous d'inclure votre fichier de connexion à la base de données ici
include("connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    session_start();
    // Récupérer les données du formulaire
    $user_id = $_SESSION['user_id'];
    $monthly_consumption = $_POST['monthly_consumption'];
    $consumption_date = $_POST['consumption_date'];

    // Traitement du téléchargement de fichier
    $target_directory = "uploads/"; 
    $target_file = $target_directory . basename($_FILES["meter_photo"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Vérifier si l'image est réelle ou une fausse image
    if (isset($_POST["submit"])) {
        $check = getimagesize($_FILES["meter_photo"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            echo "Le fichier n'est pas une image.";
            $uploadOk = 0;
        }
    }

    // Vérifier si le fichier existe déjà
    if (file_exists($target_file)) {
        echo "Désolé, le fichier existe déjà.";
        $uploadOk = 0;
    }

    // Vérifier la taille de l'image
    if ($_FILES["meter_photo"]["size"] > 500000) {
        echo "Désolé, votre fichier est trop volumineux.";
        $uploadOk = 0;
    }

    // Autoriser certains formats de fichier
    if (
        $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif"
    ) {
        echo "Désolé, seuls les fichiers JPG, JPEG, PNG et GIF sont autorisés.";
        $uploadOk = 0;
    }

    // Vérifier si $uploadOk est défini à 0 par une erreur
    if ($uploadOk == 0) {
        echo "Désolé, votre fichier n'a pas été téléchargé.";
    } else {
        // Si tout est correct, essayez de télécharger le fichier
        if (move_uploaded_file($_FILES["meter_photo"]["tmp_name"], $target_file)) {
            header("Location: facture.php");
            // Enregistrez l'URL de l'image dans la base de données
            $image_url = "http://exemple.com/" . $target_file; // Mettez votre propre domaine ici
            $sql = "INSERT INTO consumption (customer_id, date, monthly_consumption, photo_url) 
                    VALUES ('$user_id', '$consumption_date', '$monthly_consumption', '$image_url')";

            if (mysqli_query($conn, $sql)) {
                echo "Consommation enregistrée avec succès.";
            } else {
                echo "Erreur d'enregistrement de la consommation: " . mysqli_error($conn);
            }
        } else {
            echo "Désolé, une erreur s'est produite lors du téléchargement de votre fichier.";
        }
    }
} else {
    // Redirection si la requête n'est pas de type POST
    header("Location: facture.php");
    exit();
}

// Assurez-vous de fermer la connexion à la base de données à la fin du script si vous l'avez ouverte
mysqli_close($conn);
?>

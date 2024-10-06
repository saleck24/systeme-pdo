<?php
require_once("../serv_projet1.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Requête pour récupérer les informations du personnel
    $query = "SELECT * FROM personnel WHERE id = $id";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        echo json_encode($data); // Retourne les données sous forme de JSON
    } else {
        echo json_encode(["error" => "Données non trouvées"]);
    }
}
?>

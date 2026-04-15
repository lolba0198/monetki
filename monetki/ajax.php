<?php
$db = new mysqli("localhost", "root", "ServBay.dev", "monetki");
$db->set_charset("utf8");

$akcja = $_REQUEST['akcja'] ?? '';

if ($akcja == 'pobierz') {
    $sql = "SELECT m.id, m.nominal, m.nr_kat, m.rok, k.symbol_flagi, met.nazwa as metal_nazwa 
            FROM monety m
            JOIN kraje k ON m.id_kraju = k.id
            JOIN metale met ON m.id_metalu = met.id";
    $res = $db->query($sql);
    echo json_encode($res->fetch_all(MYSQLI_ASSOC));
}

if ($akcja == 'dodaj') {
    $stmt = $db->prepare("INSERT INTO monety (id_kraju, nominal, nr_kat, id_metalu, rok) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isssi", $_POST['id_kraju'], $_POST['nominal'], $_POST['nr_kat'], $_POST['id_metalu'], $_POST['rok']);
    $stmt->execute();
}

if ($akcja == 'usun') {
    $id = $_GET['id'];
    $db->query("DELETE FROM monety WHERE id = $id");
}
?>
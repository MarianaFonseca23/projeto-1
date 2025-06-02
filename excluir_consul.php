<?php
session_start();
$conn = new mysqli('localhost', 'root', 'nova_senha', 'minha_base_de_dados');


if ($conn->connect_error) {
    die("Erro ao conectar ao banco de dados: " . $conn->connect_error);
}

if (!isset($_SESSION['utilizador'])) {
    die("Erro: Usuário não autenticado.");
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Erro: ID da consulta não foi informado.");
}

$id_consulta = $_GET['id'];

$sql = "UPDATE consultas SET status = 'cancelada' WHERE id = ? AND utilizador = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id_consulta, $_SESSION['utilizador']);

if ($stmt->execute()) {
    echo "<script>alert('Consulta cancelada com sucesso!'); window.location.href='perfil_utili.php';</script>";
} else {
    echo "Erro ao cancelar consulta: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>

<?php
session_start();

$conn = new mysqli('localhost', 'root', 'nova_senha', 'minha_base_de_dados');

if ($conn->connect_error) {
    die("Erro ao conectar ao banco de dados: " . $conn->connect_error);
}

if (!isset($_SESSION['utilizador'])) {
    die("Erro: Usuário não autenticado.");
}

$id_usuario = $_SESSION['utilizador'];


if (!isset($_POST['data_consulta']) || empty($_POST['data_consulta'])) {
    die("Erro: Data da consulta não foi informada.");
}

$data_consulta = $_POST['data_consulta'];

$data_consulta = date('Y-m-d H:i:s', strtotime($data_consulta));

$sql = "INSERT INTO consultas (utilizador, data_consulta) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $id_usuario, $data_consulta);

if ($stmt->execute()) {
    echo "<script>alert('Consulta marcada com sucesso!'); window.location.href='perfil_utili.php';</script>";
} else {
    echo "Erro ao marcar consulta: " . $stmt->error;
}

$stmt->close();
$conn->close();

?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <title>Marcar Consulta</title>
</head>

<body>


    <p><a href="perfil_utilizador.php">Voltar para o Perfil</a></p>

</body>
</html>
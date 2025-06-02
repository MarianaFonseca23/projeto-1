<?php
session_start();
$conn = new mysqli('localhost', 'root', 'nova_senha', 'minha_base_de_dados');

if ($conn->connect_error) {
    die("Erro ao conectar ao banco de dados: " . $conn->connect_error);
}

if (!isset($_SESSION['utilizador']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Coleta os dados do formulário
$admin_id = $_SESSION['utilizador'];
$nome_projeto = $_POST['nome_projeto'];
$data_projeto = $_POST['data'];
$status = $_POST['status'];
$observacoes = $_POST['observacoes'];

// Inserir os dados na tabela 'projetos'
$sql_insert = "INSERT INTO projetos (nome_projeto, data_projeto, status, observacoes) 
               VALUES (?, ?, ?, ?)";

$stmt = $conn->prepare($sql_insert);
$stmt->bind_param("ssss", $nome_projeto, $data_projeto, $status, $observacoes);

if ($stmt->execute()) {
    // Redireciona para o perfil do admin após o sucesso
    header("Location: perfil_admin.php");
} else {
    echo "Erro ao registrar projeto: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
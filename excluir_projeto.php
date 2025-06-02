<?php
session_start();

// Verificar se o usuário é admin
if (!isset($_SESSION['utilizador']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli('localhost', 'root', 'nova_senha', 'minha_base_de_dados');

if ($conn->connect_error) {
    die("Erro ao conectar ao banco de dados: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Excluir o projeto
    $sql = "DELETE FROM projetos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: perfil_admin.php");  // Redireciona para o perfil do admin
        exit();
    } else {
        echo "Erro ao excluir o projeto";
    }

    $stmt->close();
}

$conn->close();
?>
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


if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $user_id = $_GET['id'];

    $sql_delete_consultas = "DELETE FROM consultas WHERE utilizador = ?";
    $stmt = $conn->prepare($sql_delete_consultas);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    
    $sql_delete_user = "DELETE FROM utilizadores WHERE id = ?";
    $stmt = $conn->prepare($sql_delete_user);
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        header("Location: perfil_admin.php?msg=Usuario excluido com sucesso");
        exit();
    } else {
        echo "Erro ao excluir usuário: " . $conn->error;
    }

    $stmt->close();
} else {
    echo "ID inválido!";
}

$conn->close();
?>
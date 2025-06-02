<?php
$conn = new mysqli('localhost', 'root', 'nova_senha', 'minha_base_de_dados');

if ($conn->connect_error) {
    die("Erro ao conectar ao banco de dados: " . $conn->connect_error);
}


if (!empty($_GET['id'])) {
    $id = intval($_GET['id']); 

    $sql = "DELETE FROM consultas WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "Consulta excluída com sucesso!";
        header("Location: perfil_admin.php"); 
        exit();
    } else {
        echo "Erro ao excluir consulta: " . $conn->error;
    }

    $stmt->close();
} else {
    echo "Erro: ID da consulta não foi informado.";
}

$conn->close();
?>
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

$admin_id = $_SESSION['utilizador']; 


if (isset($_GET['id'])) {
    $consulta_id = intval($_GET['id']);

   
    $sql_verificar = "SELECT id FROM consultas WHERE id = ? AND criado_por = ?";
    $stmt = $conn->prepare($sql_verificar);
    $stmt->bind_param("ii", $consulta_id, $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        
        $sql_excluir = "DELETE FROM consultas WHERE id = ?";
        $stmt = $conn->prepare($sql_excluir);
        $stmt->bind_param("i", $consulta_id);
        
        if ($stmt->execute()) {
            header("Location: perfil_admin.php?success=Consulta excluída");
            exit();
        } else {
            echo "Erro ao excluir a consulta.";
        }
    } else {
        echo "Erro: Você só pode excluir consultas criadas por você.";
    }
} else {
    echo "Erro: ID de consulta não fornecido.";
}
?>
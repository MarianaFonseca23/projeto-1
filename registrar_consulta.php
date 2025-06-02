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

if (isset($_POST['utilizador'], $_POST['data'], $_POST['observacoes']) && 
    !empty($_POST['utilizador']) && !empty($_POST['data']) && !empty($_POST['observacoes'])) {
    
    $utilizador_id = intval($_POST['utilizador']);
    $data_consulta = $_POST['data'];
    $observacoes = trim($_POST['observacoes']);
    $status = 'pendente';

    $sql_nome = "SELECT nome FROM utilizadores WHERE id = ?";
    $stmt_nome = $conn->prepare($sql_nome);
    $stmt_nome->bind_param("i", $utilizador_id);
    $stmt_nome->execute();
    $result_nome = $stmt_nome->get_result();
    $row = $result_nome->fetch_assoc();

    $nome_utilizador = $row['nome'] ?? 'Desconhecido'; 

    $sql_insert = "INSERT INTO consultas (utilizador, nome_utilizador, data_consulta, status, observacoes, criado_por) 
                   VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql_insert);
    $stmt->bind_param("issssi", $utilizador_id, $nome_utilizador, $data_consulta, $status, $observacoes, $admin_id);

    if ($stmt->execute()) {
        header("Location: perfil_admin.php?success=1");
        exit();
    } else {
        echo "Erro ao registrar consulta: " . $stmt->error;
    }

} else {
    echo "Erro: Todos os campos devem ser preenchidos.";
}
?>
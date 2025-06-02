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


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $novo_email = $_POST['email'];
    $nova_senha = $_POST['password'];

   
    if (!empty($nova_senha)) {
        $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
        $sql = "UPDATE utilizadores SET email = ?, password = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $novo_email, $senha_hash, $id_usuario);
    } else {
        $sql = "UPDATE utilizadores SET email = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $novo_email, $id_usuario);
    }

    if ($stmt->execute()) {
        $_SESSION['email'] = $novo_email;
        echo "<script>alert('Dados atualizados com sucesso!'); window.location.href='perfil_utili.php';</script>";
    } else {
        echo "Erro ao atualizar: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Editar Dados</title>
</head>
<body>
    <h2>Editar Dados do Utilizador</h2>
    <form action="editar_perfil_utili.php" method="post">
        <label for="email">Novo E-mail:</label>
        <input type="email" name="email" required>
        
        <label for="password">Nova Senha (deixe em branco para não alterar):</label>
        <input type="password" name="password">
        
        <button type="submit">Salvar Alterações</button>
    </form>
    <a href="perfil_utili.php">Cancelar</a>
    
</body>
</html>
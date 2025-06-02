<?php
session_start();
$conn = new mysqli('localhost', 'root', 'nova_senha', 'minha_base_de_dados');

if ($conn->connect_error) {
    die("Erro ao conectar ao banco de dados: " . $conn->connect_error);
}

if (!isset($_SESSION['utilizador'])) {
    die("Erro: Usuário não autenticado.");
}

$utilizador_id = $_SESSION['utilizador'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Erro: ID de consulta inválido.");
}

$consulta_id = $_GET['id'];

$sql = "SELECT data_consulta FROM consultas WHERE id = ? AND utilizador = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $consulta_id, $utilizador_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Erro: Consulta não encontrada ou não pertence a este utilizador.");
}

$consulta = $result->fetch_assoc();
$data_consulta_atual = new DateTime($consulta['data_consulta']);
$data_atual = new DateTime();
$intervalo = $data_atual->diff($data_consulta_atual);
$horas_faltando = ($intervalo->days * 24) + $intervalo->h;

if ($horas_faltando < 72) {
    header("Location: perfil_utilizador.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['nova_data']) || empty($_POST['nova_data'])) {
        die("Erro: A nova data da consulta é obrigatória.");
    }

    $nova_data = $_POST['nova_data'];

    $sql_update = "UPDATE consultas SET data_consulta = ? WHERE id = ? AND utilizador = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("sii", $nova_data, $consulta_id, $utilizador_id);

    if ($stmt_update->execute()) {
        header("Location: perfil_utili.php?msg=Consulta atualizada com sucesso");
        exit();
    } else {
        echo "Erro ao atualizar consulta: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Consulta</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #2C3E50;
            color: #ECF0F1;
            text-align: center;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 50%;
            margin: auto;
            padding: 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            margin-top: 50px;
        }

        input,
        button {
            padding: 10px;
            margin: 10px;
            border-radius: 5px;
            border: none;
        }

        input {
            width: 80%;
        }

        button {
            background: #E67E22;
            color: white;
            cursor: pointer;
        }

        .voltar {
            background: #C0392B;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Editar Consulta</h2>
        <p>Consulta atual: <?php echo htmlspecialchars($consulta['data_consulta']); ?></p>

        <form method="post">
            <label>Nova Data e Hora:</label><br>
            <input type="datetime-local" name="nova_data" required>
            <br>
            <button type="submit">Salvar Alterações</button>
        </form>

        <button class="voltar" onclick="window.location.href='perfil_utili.php'">Cancelar</button>
    </div>

</body>
</html>
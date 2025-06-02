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


$sql_user = "SELECT nome, apelido, username, email FROM utilizadores WHERE id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $utilizador_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

if ($result_user->num_rows > 0) {
    $usuario = $result_user->fetch_assoc();
} else {
    die("Erro: Usuário não encontrado.");
}
?>

<!DOCTYPE html>

<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil do Utilizador</title>
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
            display: flex;
            width: 60%;
            margin: auto;
            padding: 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            margin-top: 50px;
        }

        .left {
            width: 50%;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: bold;
        }

        table {
            width: 100%;
            background: rgba(255, 255, 255, 0.3);
            border-collapse: collapse;
        }

        th,
        td {
            padding: 10px;
            border: 1px solid #fff;
        }

        .dados,
        .consulta,
        .agendadas {
            background: rgba(255, 255, 255, 0.2);
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
        }

        a,
        button {
            background: #E67E22;
            color: white;
            padding: 10px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px;
        }

        button.logout {
            background: #C0392B;
        }

        input {
            padding: 10px;
            border: none;
            border-radius: 5px;
            margin: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="left">
            <img src="imagens/logo.jpg" alt="logo" width="100%" height="100%" style="border-radius: 10px;">
        </div>
        <div class="right">
            <h1>Perfil do Utilizador</h1>
            <h2>Olá <?php echo htmlspecialchars($usuario["nome"]) . " " . htmlspecialchars($usuario["apelido"]) . "!"; ?></h2>

            <div class="dados">
                <table>
                    <tr>
                        <th>Nome</th>
                        <th>Apelido</th>
                        <th>Nome de Utilizador</th>
                        <th>E-mail</th>
                    </tr>
                    <tr>
                        <td><?php echo htmlspecialchars($usuario["nome"]); ?></td>
                        <td><?php echo htmlspecialchars($usuario["apelido"]); ?></td>
                        <td><?php echo htmlspecialchars($usuario["username"]); ?></td>
                        <td><?php echo htmlspecialchars($usuario["email"]); ?></td>
                    </tr>
                </table>
                <button><a href="editar_perfil_utili.php">Editar dados do utilizador</a></button>
            </div>

            <div class="consulta">
                <h3>Marcar Consulta</h3>
                <form action="marcar_consul.php" method="post">
                    <label>Data e Hora da Consulta:</label>
                    <input type="datetime-local" name="data_consulta" required>
                    <button type="submit">Marcar Consulta</button>
                </form>
            </div>

            <div class="agendadas">
                <h3>Consultas Agendadas</h3>
                <?php
                $sql_consultas = "SELECT id, data_consulta, status FROM consultas WHERE utilizador = ? AND status != 'cancelada'";
                $stmt_consultas = $conn->prepare($sql_consultas);
                $stmt_consultas->bind_param("i", $utilizador_id);
                $stmt_consultas->execute();
                $result_consultas = $stmt_consultas->get_result();

                if ($result_consultas->num_rows > 0) {
                    echo "<table>
                            <tr>
                                <th>Data</th><th>Hora</th><th>Ações</th>
                            </tr>";
                    while ($consulta = $result_consultas->fetch_assoc()) {
                        $data_hora = explode(" ", $consulta['data_consulta']);
                        $data = $data_hora[0];
                        $hora = isset($data_hora[1]) ? $data_hora[1] : '';

                        $data_atual = new DateTime();
                        $data_consulta = new DateTime($consulta['data_consulta']);
                        $intervalo = $data_atual->diff($data_consulta);
                        $horas_faltando = ($intervalo->days * 24) + $intervalo->h;

                        echo "<tr>
                                <td>{$data}</td>
                                <td>{$hora}</td>
                                <td>";

                        if ($horas_faltando >= 72) {
                            echo "<a href='editar_consul.php?id={$consulta['id']}'>Editar</a> <br> | ";
                        } else {
                            echo "<br><span style='color: gray;'>Edição não permitida</span> | ";
                        }

                        echo "<br><br><a href='excluir_consul.php?id={$consulta['id']}'>Cancelar</a></td>
                              </tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p>Nenhuma consulta agendada.</p>";
                }
                ?>
            </div>

            <button class="logout" onclick="window.location.href='logout.php'">Sair</button>
        </div>
    </div>
</body>

</html>
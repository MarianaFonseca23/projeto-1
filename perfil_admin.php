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

$sql_admin = "SELECT nome, email FROM utilizadores WHERE id = ?";
$stmt = $conn->prepare($sql_admin);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result_admin = $stmt->get_result();
$admin = $result_admin->fetch_assoc() ?? ["nome" => "Desconhecido", "email" => "Não encontrado"];

$usuarios = [];
$sql_usuarios = "SELECT id, nome, apelido, username, email FROM utilizadores";
$result_usuarios = $conn->query($sql_usuarios);
while ($row = $result_usuarios->fetch_assoc()) {
    $usuarios[] = $row;
}
$consultas = [];

$sql_consultas = "SELECT c.id, c.utilizador, u.nome AS nome_utilizador, c.data_consulta, c.status
                  FROM consultas c
                  JOIN utilizadores u ON c.utilizador = u.id
                  WHERE c.criado_por != ? AND c.status != 'projeto'"; 

$stmt_consultas = $conn->prepare($sql_consultas);

$stmt_consultas->bind_param("i", $admin_id);

$stmt_consultas->execute();

$result_consultas = $stmt_consultas->get_result();

$consultas = [];
while ($row = $result_consultas->fetch_assoc()) {
    $consultas[] = $row;
}

$stmt_consultas->close();

$novos_projetos = [];
$sql_novos_projetos = "SELECT id, nome_projeto, data_projeto, status, observacoes 
                        FROM projetos 
                        ORDER BY data_projeto DESC";

$result_novos_projetos = $conn->query($sql_novos_projetos);
$novos_projetos = [];
while ($row = $result_novos_projetos->fetch_assoc()) {
    $novos_projetos[] = $row;
}

?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil do Administrador</title>
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
            width: 60%;
            margin: auto;
            padding: 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            margin-top: 50px;
        }

        .user-info,
        .consultas,
        .registro {
            background: rgba(255, 255, 255, 0.2);
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
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

        a,
        button {
            background: #E67E22;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px;
        }

        button.logout {
            background: #E67E22;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Perfil do Administrador</h1>
        <h2>Olá <?php echo $admin["nome"] . "!"; ?></h2>
        <p><strong>Email:</strong> <?php echo $admin["email"]; ?></p>

        <div class="user-info">
            <h3>Informações de Todos os Utilizadores</h3>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Apelido</th>
                    <th>Nome de Utilizador</th>
                    <th>E-mail</th>
                    <th>Ações</th>
                </tr>
                <?php foreach ($usuarios as $usuario) { ?>
                    <tr>
                        <td><?php echo $usuario["id"]; ?></td>
                        <td><?php echo $usuario["nome"]; ?></td>
                        <td><?php echo $usuario["apelido"]; ?></td>
                        <td><?php echo $usuario["username"]; ?></td>
                        <td><?php echo $usuario["email"]; ?></td>
                        <td>
                            <a href="excluir_utili.php?id=<?php echo $usuario['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir este usuário?');">Excluir</a>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>

        <div class="consultas">
            <h3>Consultas Marcadas por Utilizadores</h3>
            <table>
                <tr>
                    <th>Nome do Utilizador</th>
                    <th>Data/Horário</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
                <?php foreach ($consultas as $consulta) { ?>
                    <tr>
                        <td><?php echo $consulta["nome_utilizador"]; ?></td>
                        <td><?php echo $consulta["data_consulta"]; ?></td>
                        <td><?php echo $consulta["status"]; ?></td>
                        <td><a href="excluir_consul_adm.php?id=<?php echo $consulta['id']; ?>">Cancelar</a></td>
                    </tr>
                <?php } ?>
            </table>
        </div>

        <div class="registro">
            <h3>Adicionar Novo Projeto</h3>
            <form action="registrar_projeto.php" method="POST">
                <label for="nome_projeto">Nome do Projeto:</label>
                <input type="text" id="nome_projeto" name="nome_projeto" required><br>

                <label for="data">Data de Início:</label>
                <input type="datetime-local" id="data" name="data" required><br>

                <label for="status">Status:</label>
                <select id="status" name="status">
                    <option value="pendente">Pendente</option>
                    <option value="em_andamento">Em andamento</option>
                    <option value="concluido">Concluído</option>
                </select><br>

                <label for="observacoes">Descrição do Projeto:</label>
                <input type="text" name="observacoes" placeholder="Descrição do projeto" required>

                <button type="submit">Adicionar Projeto</button>
            </form>
        </div>

        <div class="projetos-registrados">
            <h3>Novos Projetos Adicionados pelo Administrador</h3>
            <table>
                <tr>
                    <th>Nome do Projeto</th>
                    <th>Data/Horário</th>
                    <th>Status</th>
                    <th>Observações</th>
                    <th>Ações</th>
                </tr>
                <?php foreach ($novos_projetos as $projeto) { ?>
                    <tr>
                        <td><?php echo $projeto["nome_projeto"]; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($projeto["data_projeto"])); ?></td>
                        <td><?php echo $projeto["status"]; ?></td>
                        <td><?php echo $projeto["observacoes"]; ?></td>
                        <td><a href="excluir_projeto.php?id=<?php echo $projeto['id']; ?>">Excluir</a></td>
                    </tr>
                <?php } ?>
            </table>
        </div>

        <a href="logout.php">
            <button class="logout">Sair</button>
        </a>

    </div>

</body>
</html>
<?php
session_start();

$conn = new mysqli('127.0.0.1', 'root', 'nova_senha', 'minha_base_de_dados');

if ($conn->connect_error) {
    die("Erro ao conectar ao banco de dados: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM utilizadores WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();


        if (password_verify($password, $row['password'])) {

            $_SESSION['utilizador'] = $row['id'];
            $_SESSION['role'] = $row['role'];



            error_log("Usuário autenticado: ID = " . $_SESSION['utilizador'] . ", Role = " . $_SESSION['role']);



            if ($row['role'] === 'admin') {
                header('Location: perfil_admin.php');
                exit();
            } elseif ($row['role'] === 'utilizador') {
                header('Location: perfil_utili.php');
                exit();
            }
        } else {
            $erro = "Senha incorreta";
        }
    } else {
        $erro = "Usuário não encontrado";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: #2c3e50;
            font-family: Arial, sans-serif;
        }

        .container {
            display: flex;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            width: 70%;
            height: 70vh;
        }

        .left {
            width: 40%;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: bold;
        }

        .right {
            padding: 70px;
            width: 60%;
            background: #dff3fc;
            align-items: center;
        }

        h2 {
            margin-bottom: 10px;
        }

        input,
        button {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background: #f39c12;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background: #d68910;
        }

        .link {
            display: block;
            text-align: center;
            margin-top: 10px;
            color: #2980b9;
            text-decoration: none;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="left">
            <img src="imagens/logo.jpg" alt="logo" width="100%" height="100%" style="border-radius: 10px;">
        </div>
        <div class="right">
            <h1>Bem vindo</h1>
            <p>Entrar</p>
            <?php if (isset($erro)) { ?>
                <p style="color: red;"><?php echo $erro; ?></p>
            <?php } ?>

            <form action="login.php" method="post">
                <input type="email" name="email" placeholder="E-mail" required>
                <input type="password" name="password" placeholder="Senha" required>
                <button type="submit" name="login">Entrar</button>
            </form>
            <a href="#" class="link">Esqueceu a senha?</a>
            <a href="registro.php" class="link">Criar conta</a>
        </div>
    </div>

</body>
</html>
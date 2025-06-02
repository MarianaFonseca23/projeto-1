<?php
session_start();

$conn = new mysqli('127.0.0.1', 'root', 'nova_senha', 'minha_base_de_dados');

if ($conn->connect_error) {
    die("Erro ao conectar ao banco de dados: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $nome = $_POST['nome'];
    $apelido = $_POST['apelido'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $checkQuery = "SELECT * FROM utilizadores WHERE email = ? OR username = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("ss", $email, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        
        $existingEmails = [];
        $existingUsernames = [];

        while ($row = $result->fetch_assoc()) {
            if ($row['email'] === $email) {
                $existingEmails[] = $email;
            }
            if ($row['username'] === $username) {
                $existingUsernames[] = $username;
            }
        }

        if (!empty($existingEmails)) {
            echo "Este email já está cadastrado.<br>";
        }
        if (!empty($existingUsernames)) {
            echo "Este username já está cadastrado.<br>";
        }
    } else {

        $sql = "INSERT INTO utilizadores (nome, apelido, email, telefone, username, password) VALUES ('$nome','$apelido', '$email', '$telefone', '$username', '$password')";

        if ($conn->query($sql) === TRUE) {
            echo "Utilizador criado com sucesso!";
            header("Location:login.php");
            exit();
        } else {
            echo "Erro ao criar utilizador: " . $sql . "<br>" . $conn->error;
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Document</title>
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
            width: 80%;
        }

        .left {
            background: url('welcome.jpg') no-repeat center center/cover;
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
            padding: 40px;
            width: 60%;
            background: #dff3fc;
        }

        h1 {
            text-align: center;
            margin-bottom: 10px;
        }

        input,
        select,
        button {
            align-items: center;
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background: #2980b9;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background: #1c5a8a;
        }

        .link {
            display: block;
            text-align: center;
            margin-top: 10px;
            color: #2980b9;
            text-decoration: none;
        }
    </style>
    </style>
</head>

<body>
    <div class="container">

        <div class="left">
            <img src="imagens/logo.jpg" alt="logo" width="100%" height="100%" style="border-radius: 50px;">
        </div>

        <div class="right">

            <h1>REGISTRAR</h1>

            <h4>Insira seus Dados:</h4>

            <form method="post" action="registro.php">

                <input type="text" id="nome" name="nome" placeholder="Nome"><br><br>

                <input type="text" id="apelido" name="apelido" placeholder="Apelido" required><br><br>


                <input type="email" id="email" name="email" placeholder="E-mail" required><br><br>


                <input type="text" id="telefone" name="telefone" placeholder="Telefone"><br><br>


                <input type="text" id="username" name="username" placeholder="Nome de Usuário" required><br><br>


                <input type="password" id="password" name="password" placeholder="Senha"><br><br>

                <button type="submit" name="register">Registrar</button>
            </form>
            <a href="login.php" class="link">Tenho conta</a>

        </div>
    </div>

    </div>

</body>
</html>
<?php
session_start();

require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Por favor, preencha todos os campos.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                header("Location: home.php");
                exit();
            } else {
                $error = "Senha incorreta.";
            }
        } else {
            $error = "Usuário não encontrado.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Agenda de Contatos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .logo {
            display: block;
            margin-left: auto;
            margin-right: auto;
            width: 250px;
            height: auto;
        }
        body {
            background-color: #add8e6; 
        }
        .form-container {
            background-color: #fff; 
            border: 2px solid #000; 
            padding: 20px;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <header class="bg-primary text-white text-center py-3">
        <h1>Agenda de Contatos</h1>
    </header>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="form-container">
                    <div class="card">
                        <div class="card-header text-center"><img src="logo-removebg-preview.png" alt="Logo" class="logo my-4">
                            <h3>Login</h3>
                        </div>
                        <div class="card-body">
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger">
                                    <?= htmlspecialchars($error) ?>
                                </div>
                            <?php endif; ?>
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Senha</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Entrar</button>
                            </form>
                        </div>
                        <div class="card-footer text-center">
                            <a href="index.php">Não possui cadastro? Cadastre-se</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer class="bg-secondary text-white text-center py-3 mt-5">
        <p>&copy; 2024 Agenda de Contatos. Todos os direitos reservados.</p>
    </footer>
</body>
</html>

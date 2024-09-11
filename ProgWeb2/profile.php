<?php
session_start();

require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($name) || empty($email)) {
        $error = "Por favor, preencha todos os campos obrigatórios.";
    } else {
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $email, $user_id);

        if ($stmt->execute()) {
            if (!empty($password) && $password === $confirm_password) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->bind_param("si", $hashed_password, $user_id);
                
                if ($stmt->execute()) {
                    $success = "Informações do perfil atualizadas com sucesso.";
                } else {
                    $error = "Erro ao atualizar a senha. Tente novamente.";
                }
            } else if (!empty($password)) {
                $error = "As senhas não coincidem. Tente novamente.";
            } else {
                $success = "Informações do perfil atualizadas com sucesso.";
            }
        } else {
            $error = "Erro ao atualizar o perfil. Tente novamente.";
        }
    }
}

$stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil do Usuário - Agenda de Contatos</title>
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
    </style>
</head>
<body>
<header class="bg-primary text-white text-center py-3">
        <h1>Agenda de Contatos</h1>
    </header>
    <img src="logo-removebg-preview.png" alt="Logo" class="logo">
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-header">
                        <h3>Perfil do Usuário</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger">
                                <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>
                        <?php if (isset($success)): ?>
                            <div class="alert alert-success">
                                <?= htmlspecialchars($success) ?>
                            </div>
                        <?php endif; ?>
                        <form method="POST" action="">
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Nome</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">E-mail</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Nova Senha (deixe em branco para manter a atual)</label>
                                <input type="password" class="form-control" id="password" name="password">
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirmar Nova Senha</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                            </div>
                            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                            <a href="home.php" class="btn btn-secondary">Cancelar</a> 
                        </form>
                    </div>
                </div>
                <a href="home.php">Voltar</a>
            </div>
        </div>
    </div>
    <footer class="bg-secondary text-white text-center py-3 mt-5">
        <p>&copy; 2024 Agenda de Contatos. Todos os direitos reservados.</p>
    </footer>
</body>
</html>

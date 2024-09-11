<?php
session_start();

require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manage_contacts.php");
    exit();
}

$contact_id = $_GET['id'];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $notes = $_POST['notes'];

    if (empty($name) || empty($phone) || empty($email)) {
        $error = "Por favor, preencha todos os campos obrigatórios.";
    } else {
        $stmt = $conn->prepare("UPDATE contacts SET name = ?, phone = ?, email = ?, address = ?, notes = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ssssssi", $name, $phone, $email, $address, $notes, $contact_id, $user_id);

        if ($stmt->execute()) {
            header("Location: manage_contacts.php");
            exit();
        } else {
            $error = "Erro ao atualizar o contato. Tente novamente.";
        }
    }
} else {
    $stmt = $conn->prepare("SELECT * FROM contacts WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $contact_id, $user_id);
    $stmt->execute();
    $contact = $stmt->get_result()->fetch_assoc();

    if (!$contact) {
        header("Location: manage_contacts.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar - Agenda de Contatos</title>
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
                        <h3>Editar Contato</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger">
                                <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nome Completo</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($contact['name']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Telefone</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($contact['phone']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">E-mail</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($contact['email']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Endereço</label>
                                <input type="text" class="form-control" id="address" name="address" value="<?= htmlspecialchars($contact['address']) ?>">
                            </div>
                            <div class="mb-3">
                                <label for="notes" class="form-label">Observações</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3"><?= htmlspecialchars($contact['notes']) ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                            <a href="manage_contacts.php" class="btn btn-secondary">Cancelar</a>
                        </form>
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

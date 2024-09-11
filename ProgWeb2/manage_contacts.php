<?php
session_start();

require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['delete'])) {
    $contact_id = $_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM contacts WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $contact_id, $user_id);

    if ($stmt->execute()) {
        header("Location: manage_contacts.php");
        exit();
    } else {
        $error = "Erro ao excluir o contato. Tente novamente.";
    }
}

$stmt = $conn->prepare("SELECT * FROM contacts WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$contacts = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar - Agenda de Contatos</title>
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
    <img src="logo-removebg-preview.png" alt="Logo" class="logo my-4">
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h3>Gerenciar Contatos</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger">
                                <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>
                        <a href="add_contact.php" class="btn btn-primary mb-3">Adicionar Contato</a>
                        <table class="table table-striped table-hover">
                            <thead class="table">
                                <tr>
                                    <th>Nome</th>
                                    <th>Telefone</th>
                                    <th>E-mail</th>
                                    <th>Endereço</th>
                                    <th>Observações</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($contact = $contacts->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($contact['name']) ?></td>
                                        <td><?= htmlspecialchars($contact['phone']) ?></td>
                                        <td><?= htmlspecialchars($contact['email']) ?></td>
                                        <td><?= htmlspecialchars($contact['address']) ?></td>
                                        <td><?= htmlspecialchars($contact['notes']) ?></td>
                                        <td>
                                            <a href="edit_contact.php?id=<?= $contact['id'] ?>" class="btn btn-warning btn-sm">
                                                Editar
                                            </a>
                                            <a href="manage_contacts.php?delete=<?= $contact['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir este contato?')">
                                                Excluir
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
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

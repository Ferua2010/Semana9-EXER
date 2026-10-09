<?php

declare(strict_types=1);

require_once 'Conexao.php';
require_once 'PecaDAO.php';

$pecaDAO = new PecaDAO();

$id = 1;

if ($pecaDAO->excluir($id)) {
    echo 'Peça desativada com sucesso.';
} else {
    echo 'Não foi possível desativar a peça.';
}

$pecas = $pecaDAO->listarTodos();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Peças Ativas</title>
</head>
<body>
    <h1>Peças Ativas</h1>

    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>SKU</th>
                <th>Nome</th>
                <th>Preço</th>
                <th>Estoque</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pecas as $peca): ?>
                <tr>
                    <td><?= htmlspecialchars((string) $peca['id'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($peca['sku'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($peca['nome'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td>R$ <?= htmlspecialchars(number_format((float) $peca['preco'], 2, ',', '.'), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string) $peca['quantidade_estoque'], ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
<?php

declare(strict_types=1);

require_once 'Conexao.php';
require_once 'PecaDAO.php';

$pagina = (int) ($_GET['p'] ?? 1);

if ($pagina < 1) {
    $pagina = 1;
}

$offset = ($pagina - 1) * 5;

$pecaDAO = new PecaDAO();
$pecas = $pecaDAO->listarPaginado($offset, 5);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Catálogo de Peças Industriais</title>
</head>
<body>
    <h1>Catálogo de Peças Industriais</h1>

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

    <nav>
        <?php if ($pagina > 1): ?>
            <a href="ex03_catalogo_paginado.php?p=<?= htmlspecialchars((string) ($pagina - 1), ENT_QUOTES, 'UTF-8') ?>">Anterior</a>
        <?php endif; ?>

        <a href="ex03_catalogo_paginado.php?p=<?= htmlspecialchars((string) ($pagina + 1), ENT_QUOTES, 'UTF-8') ?>">Próxima</a>
    </nav>
</body>
</html>
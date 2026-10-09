<?php

declare(strict_types=1);

require_once 'Conexao.php';
require_once 'PecaDAO.php';

$pdo = Conexao::obterConexao();
$pecaDAO = new PecaDAO($pdo);

$sucesso = $pecaDAO->registrarMovimentacao(1, 10, 'entrada');

if ($sucesso) {
    echo 'Movimentação registrada com sucesso.';
} else {
    echo 'Não foi possível registrar a movimentação.';
}
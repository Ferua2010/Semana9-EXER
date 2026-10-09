<?php
declare(strict_types=1);

const DB_DSN = 'pgsql:host=127.0.0.1;port=5432;dbname=senai';
const DB_USUARIO = 'postgres';
const DB_SENHA = 'postgres';

function obterConexao(): PDO
{
    return new PDO(DB_DSN, DB_USUARIO, DB_SENHA, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
}

function buscarVulneravel(string $termo): void
{
    $pdo = obterConexao();

    $sql = "SELECT id, nome, categoria
            FROM ferramentas
            WHERE nome ILIKE '%$termo%'";

    echo "Consulta vulnerável:\n$sql\n";

    $resultado = $pdo->query($sql)->fetchAll();

    echo "Registros encontrados: " . count($resultado) . "\n";
}

function buscarProtegido(string $termo): void
{
    $pdo = obterConexao();

    $sql = 'SELECT id, nome, categoria
            FROM ferramentas
            WHERE nome ILIKE :termo';

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':termo' => "%$termo%"]);

    $resultado = $stmt->fetchAll();

    echo "Consulta protegida:\n$sql\n";
    echo "Registros encontrados: " . count($resultado) . "\n";
}

$termo = "' OR '1'='1";

echo "=== LABORATÓRIO FORENSE DE COMPARAÇÃO ===\n";
echo "Termo testado: $termo\n\n";

try {
    echo "--- Abordagem vulnerável ---\n";
    buscarVulneravel($termo);

    echo "\n--- Abordagem protegida ---\n";
    buscarProtegido($termo);
} catch (PDOException $erro) {
    echo "Erro de banco de dados: {$erro->getMessage()}\n";
}
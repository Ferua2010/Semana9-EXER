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

function lerResposta(string $mensagem): string
{
    echo $mensagem;
    return trim((string) fgets(STDIN));
}

function cadastrarFerramenta(PDO $pdo): void
{
    $nome = lerResposta("Nome da ferramenta: ");
    $categoria = lerResposta("Categoria: ");

    $sql = 'INSERT INTO ferramentas (nome, categoria, ativo)
            VALUES (:nome, :categoria, :ativo)';

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nome' => $nome,
        ':categoria' => $categoria,
        ':ativo' => true,
    ]);

    echo "Ferramenta cadastrada com sucesso!\n";
}

function listarFerramentas(PDO $pdo): void
{
    $sql = 'SELECT id, nome, categoria
            FROM ferramentas
            WHERE ativo = :ativo
            ORDER BY nome';

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':ativo' => true]);

    $ferramentas = $stmt->fetchAll();

    if ($ferramentas === []) {
        echo "Nenhuma ferramenta ativa encontrada.\n";
        return;
    }

    foreach ($ferramentas as $ferramenta) {
        echo "{$ferramenta['id']} - ";
        echo "{$ferramenta['nome']} ({$ferramenta['categoria']})\n";
    }
}

function exibirMenu(): void
{
    echo "\n=== GERENCIADOR DE FERRAMENTAS ===\n";
    echo "1 - Cadastrar nova ferramenta\n";
    echo "2 - Listar ferramentas ativas\n";
    echo "3 - Sair do programa\n";
}

function executarMenu(PDO $pdo): void
{
    do {
        exibirMenu();
        $opcao = lerResposta("Escolha uma opção: ");

        try {
            match ($opcao) {
                '1' => cadastrarFerramenta($pdo),
                '2' => listarFerramentas($pdo),
                '3' => print "Programa encerrado.\n",
                default => print "Opção inválida.\n",
            };
        } catch (PDOException $erro) {
            echo "Não foi possível concluir a operação.\n";
            error_log($erro->getMessage());
        }
    } while ($opcao !== '3');
}

try {
    executarMenu(obterConexao());
} catch (PDOException $erro) {
    echo "Não foi possível conectar ao banco de dados.\n";
    error_log($erro->getMessage());
}
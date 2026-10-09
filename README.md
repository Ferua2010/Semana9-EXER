# Parte A: Exercícios Teóricos de Fixação

### 1. Definição de CRUD
CRUD é um acrônimo formado pelas quatro operações básicas realizadas em um banco de dados:

| Letra | Significado | Operação SQL |
| :---: | :--- | :--- |
| **C** | Create — criar dados | `INSERT` |
| **R** | Read — ler ou consultar dados | `SELECT` |
| **U** | Update — atualizar dados | `UPDATE` |
| **D** | Delete — excluir dados | `DELETE` |

**Exemplo:**
```sql
INSERT INTO ferramentas (nome) VALUES ('Furadeira');
SELECT * FROM ferramentas;
UPDATE ferramentas SET nome = 'Furadeira elétrica' WHERE id = 1;
DELETE FROM ferramentas WHERE id = 1;
```

---

### 2. Anatomia do SQL Injection
SQL Injection acontece quando o sistema monta uma consulta SQL juntando diretamente um valor enviado pelo usuário, por exemplo, por `$_GET` ou `$_POST`.

**Exemplo vulnerável:**
```php
$termo = $_GET['termo'];
$sql = "SELECT * FROM usuarios WHERE nome = '$termo'";
```

Se o usuário enviar um texto contendo comandos SQL, esse conteúdo será colocado dentro da consulta. Assim, o atacante pode alterar a lógica original da instrução.

Por exemplo, o texto:
```text
' OR '1'='1
```

Pode transformar a consulta em algo parecido com:
```sql
SELECT * FROM usuarios WHERE nome = '' OR '1'='1';
```

Como `'1'='1'` é sempre verdadeiro, o banco pode retornar registros que não deveriam ser exibidos.

O problema ocorre porque o sistema mistura duas coisas diferentes:
* A instrução SQL.
* O texto enviado pelo usuário.

Dessa forma, o banco pode interpretar parte do texto como código SQL.

---

### 3. Mecanismo das Prepared Statements
Prepared Statements dividem a execução em duas etapas:
1. `prepare()`: prepara a estrutura da consulta.
2. `execute()`: envia os valores dos parâmetros.

**Exemplo:**
```php
$sql = 'SELECT * FROM usuarios WHERE nome = :nome';
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':nome' => $nome
]);
```

O banco interpreta `:nome` como um espaço reservado para um valor. O conteúdo enviado pelo usuário não é incorporado à estrutura do SQL.

Mesmo que o usuário digite:
```text
' OR '1'='1
```
Esse texto será tratado **apenas como um valor de pesquisa**. Ele não será executado como parte da instrução SQL. Por isso, as Prepared Statements ajudam a impedir SQL Injection.

---

### 4. Vantagem dos Marcadores Nomeados
Marcadores nomeados, como `:sku` e `:preco`, deixam a consulta mais clara e fácil de manter.

**Exemplo:**
```sql
INSERT INTO pecas (codigo_sku, descricao, preco_unitario)
VALUES (:sku, :descricao, :preco);
```

Os valores ficam organizados por nome:
```php
$stmt->execute([
    ':sku' => $sku,
    ':descricao' => $descricao,
    ':preco' => $preco
]);
```

A **principal vantagem** é que não é necessário lembrar a ordem dos valores. 

Com marcadores posicionais, seria necessário usar:
```sql
INSERT INTO pecas (codigo_sku, descricao, preco_unitario)
VALUES (?, ?, ?);
```

E os valores dependeriam da posição no array:
```php
$stmt->execute([
    $sku,
    $descricao,
    $preco
]);
```

Em consultas maiores, os marcadores nomeados reduzem erros de ordem, tornam o código mais legível e facilitam futuras alterações.

---

### 5. Diferença entre bindValue() e bindParam()

O método `bindValue()` vincula o valor **imediatamente**, no momento em que é chamado.
```php
$id = 10;
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$id = 20;
$stmt->execute();
```
Nesse caso, o valor utilizado será **10**, porque esse era o valor no momento do `bindValue()`.

O método `bindParam()` vincula uma variável por **referência**. O valor é lido somente quando o `execute()` é executado.
```php
$id = 10;
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$id = 20;
$stmt->execute();
```
Nesse caso, o valor utilizado será **20**.

| Método | Comportamento |
| :--- | :--- |
| `bindValue()` | Guarda o valor no momento da vinculação. |
| `bindParam()` | Guarda uma referência e lê o valor no momento da execução. |

Na maioria dos casos, `bindValue()` é mais simples. O `bindParam()` pode ser útil quando a mesma variável será modificada e reutilizada em um laço (loop).

---

### 6. Tipagem no PDO
Quando um valor deveria ser inteiro, é importante informar o tipo usando, por exemplo:
```php
PDO::PARAM_INT
```

**Exemplo:**
```php
$stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
```

Se o tipo não for informado, o PDO pode tratar o valor como string. Isso pode causar problemas em cláusulas que esperam números, como `LIMIT`.

**Exemplo prático:**
```php
$sql = 'SELECT * FROM ferramentas LIMIT :limite';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
$stmt->execute();
```

A tipagem ajuda a:
* Garantir que o banco receba o tipo correto.
* Evitar erros de conversão.
* Impedir valores inválidos em operações numéricas.
* Tornar a intenção do código mais clara.

> **Nota:** Mesmo com Prepared Statements, a aplicação também deve validar se o valor está dentro de limites aceitáveis.

---

### 7. Padrão DAO
DAO significa *Data Access Object*, ou Objeto de Acesso a Dados.
O DAO concentra em uma classe as operações relacionadas ao banco de dados.

**Exemplo:**
```php
final class PecaDAO
{
    public function listarTodos(): array
    {
        // Consulta dos registros
    }

    public function salvar(array $dados): bool
    {
        // Inserção de registro
    }
}
```

O principal benefício é separar:
* A lógica do banco de dados.
* A lógica da aplicação.
* A apresentação dos dados.

Isso facilita a manutenção porque, se a consulta SQL precisar ser alterada, a mudança será feita no DAO, sem precisar modificar várias páginas do sistema.

O DAO também se relaciona ao **Princípio da Responsabilidade Única** (do SOLID). Esse princípio indica que uma classe deve ter uma responsabilidade principal. 

Por exemplo:
* `ConexaoBanco` cuida da conexão.
* `PecaDAO` cuida dos dados das peças.
* Um **Controlador** cuida do fluxo da aplicação.
* Uma **Página (View)** cuida da apresentação.

Essa divisão deixa o código mais organizado, testável e reutilizável.

---

### 8. Operações de UPDATE
Sem a cláusula `WHERE`, o comando `UPDATE` altera **todos os registros** da tabela. Isso pode causar perda de dados, prejuízos financeiros e problemas no sistema.

**Exemplo perigoso:**
```sql
UPDATE pecas_industriais
SET preco_unitario = 0;
```

O **correto** é indicar qual registro será alterado:
```sql
UPDATE pecas_industriais
SET preco_unitario = :preco
WHERE id = :id;
```

Por isso, sempre revise o `UPDATE` antes de executá-lo.

---

### 9. Impacto da LGPD
Um vazamento causado por SQL Injection pode gerar:
* Investigação pela ANPD.
* Multas de até 2% do faturamento, limitadas a R$ 50 milhões por infração.
* Obrigação de corrigir a falha.
* Comunicação aos clientes afetados.
* Processos judiciais.
* Prejuízo financeiro e danos à reputação da empresa.

A organização também pode sofrer bloqueio ou eliminação dos dados envolvidos e suspensão das atividades de tratamento de dados.
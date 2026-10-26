<?php
require 'config.php';

$busca = trim($_GET['busca'] ?? '');
$categoria = (int) ($_GET['categoria'] ?? 0);
$ordem = $_GET['ordem'] ?? 'recentes';
$sql = "SELECT produtos.*, categorias.nome AS categoria_nome FROM produtos LEFT JOIN categorias ON categorias.id = produtos.categoria_id WHERE produtos.ativo = 1";
$parametros = [];
$tipos = '';

if ($busca !== '') {
    $sql .= " AND (produtos.nome LIKE ? OR produtos.descricao LIKE ?)";
    $parametros[] = "%$busca%";
    $parametros[] = "%$busca%";
    $tipos .= 'ss';
}

if ($categoria > 0) {
    $sql .= " AND produtos.categoria_id = ?";
    $parametros[] = $categoria;
    $tipos .= 'i';
}

$ordens = [
    'recentes' => 'produtos.id DESC',
    'menor-preco' => 'produtos.preco ASC',
    'maior-preco' => 'produtos.preco DESC',
    'nome' => 'produtos.nome ASC'
];
$sql .= ' ORDER BY ' . ($ordens[$ordem] ?? $ordens['recentes']);
$consulta = $conexao->prepare($sql);

if ($parametros) {
    $consulta->bind_param($tipos, ...$parametros);
}

$consulta->execute();
$produtos = $consulta->get_result();
$categorias = $conexao->query("SELECT * FROM categorias ORDER BY nome");
$titulo = 'Shelby Story | Sua loja online';
require 'header.php';
?>
<section class="hero">
    <div class="limite hero-conteudo">
        <div>
            <span class="etiqueta">Novidades para você</span>
            <h1>Escolhas que combinam com o seu jeito.</h1>
            <p>Uma seleção simples, bonita e feita para transformar pequenos momentos do dia.</p>
            <a class="botao primario" href="#produtos">Ver produtos</a>
        </div>
        <div class="hero-cartao">
            <span>Coleção especial</span>
            <strong>Leve 3 produtos e ganhe frete grátis</strong>
            <p>Válido para todo o Brasil enquanto durarem os estoques.</p>
        </div>
    </div>
</section>

<section class="beneficios limite">
    <div><strong>Envio rápido</strong><span>Postagem em até 2 dias úteis</span></div>
    <div><strong>Compra segura</strong><span>Seus dados sempre protegidos</span></div>
    <div><strong>Troca fácil</strong><span>Até 7 dias após a entrega</span></div>
</section>

<section class="catalogo limite" id="produtos">
    <div class="cabecalho-secao">
        <div>
            <span class="etiqueta">Nossa seleção</span>
            <h2>Produtos em destaque</h2>
        </div>
        <p><?= $produtos->num_rows ?> produto<?= $produtos->num_rows === 1 ? '' : 's' ?> encontrado<?= $produtos->num_rows === 1 ? '' : 's' ?></p>
    </div>

    <form class="filtros" method="get">
        <input type="search" name="busca" value="<?= htmlspecialchars($busca) ?>" placeholder="O que você procura?">
        <select name="categoria">
            <option value="0">Todas as categorias</option>
            <?php while ($item = $categorias->fetch_assoc()): ?>
                <option value="<?= $item['id'] ?>" <?= $categoria === (int) $item['id'] ? 'selected' : '' ?>><?= htmlspecialchars($item['nome']) ?></option>
            <?php endwhile; ?>
        </select>
        <select name="ordem">
            <option value="recentes" <?= $ordem === 'recentes' ? 'selected' : '' ?>>Mais recentes</option>
            <option value="menor-preco" <?= $ordem === 'menor-preco' ? 'selected' : '' ?>>Menor preço</option>
            <option value="maior-preco" <?= $ordem === 'maior-preco' ? 'selected' : '' ?>>Maior preço</option>
            <option value="nome" <?= $ordem === 'nome' ? 'selected' : '' ?>>Nome</option>
        </select>
        <button class="botao secundario" type="submit">Filtrar</button>
    </form>

    <div class="grade-produtos">
        <?php if ($produtos->num_rows === 0): ?>
            <div class="vazio">
                <h3>Nenhum produto encontrado</h3>
                <p>Tente buscar por outro nome ou mudar a categoria.</p>
                <a href="index.php" class="botao secundario">Limpar filtros</a>
            </div>
        <?php endif; ?>
        <?php while ($produto = $produtos->fetch_assoc()): ?>
            <article class="produto-card">
                <a class="produto-imagem" href="produto.php?id=<?= $produto['id'] ?>">
                    <img src="<?= htmlspecialchars($produto['imagem']) ?>" alt="<?= htmlspecialchars($produto['nome']) ?>">
                </a>
                <div class="produto-info">
                    <span class="categoria"><?= htmlspecialchars($produto['categoria_nome'] ?? 'Geral') ?></span>
                    <h3><a href="produto.php?id=<?= $produto['id'] ?>"><?= htmlspecialchars($produto['nome']) ?></a></h3>
                    <p><?= dinheiro($produto['preco']) ?></p>
                    <form action="carrinho.php" method="post">
                        <input type="hidden" name="acao" value="adicionar">
                        <input type="hidden" name="produto_id" value="<?= $produto['id'] ?>">
                        <input type="hidden" name="quantidade" value="1">
                        <button class="botao primario largura-total" type="submit">Adicionar ao carrinho</button>
                    </form>
                </div>
            </article>
        <?php endwhile; ?>
    </div>
</section>
<?php require 'footer.php'; ?>

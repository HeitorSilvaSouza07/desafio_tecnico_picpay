<?php
$host = 'localhost';
$usuario = 'root';
$senha = '';
$banco = 'violeta_store';

$conexao = new mysqli($host, $usuario, $senha, $banco);

if ($conexao->connect_error) {
    die('Não foi possível conectar ao banco de dados. Importe o arquivo database.sql e confira o config.php.');
}

$conexao->set_charset('utf8mb4');

function dinheiro($valor)
{
    return 'R$ ' . number_format((float) $valor, 2, ',', '.');
}

function carrinhoAtual()
{
    if (empty($_COOKIE['carrinho'])) {
        return [];
    }

    $dados = json_decode($_COOKIE['carrinho'], true);
    return is_array($dados) ? $dados : [];
}

function salvarCarrinho($carrinho)
{
    setcookie('carrinho', json_encode($carrinho), time() + 60 * 60 * 24 * 30, '/');
    $_COOKIE['carrinho'] = json_encode($carrinho);
}

function quantidadeCarrinho()
{
    return array_sum(carrinhoAtual());
}
?>

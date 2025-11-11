<?php
// admin.php

// 1. Inclui o guarda de autenticação. Se não estiver logado, redireciona para login.html
include 'php/check-auth.php'; 

// 2. Checa as permissões para a página Admin
// Redireciona para shop.php se não for empresa ou admin
if ($_SESSION['usuario_tipo'] !== 'admin' && $_SESSION['usuario_tipo'] !== 'empresa') {
    header("Location: /mercado-online/shop.php");
    exit();
}
// Se chegou aqui, o usuário está logado E tem permissão de Admin/Empresa.
?>
<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Painel Administrativo - Valify</title>
   <link rel="stylesheet" href="assets/css/style.css" />
    <link rel="stylesheet" href="assets/css/admin.css" />
    <link rel="stylesheet" href="assets/css/admin_products.css" />
        <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    />
  </head>
  <body>
    <header class="header">
      <div class="container nav">
        <a class="brand" href="index.php">
          <div class="brand-badge">VF</div>
          <div>Valify <span class="small" style="display:block;margin-top:-2px;">Mercado Online</span></div>
        </a>
        <div class="spacer"></div>
                <a class="icon-btn" href="shop.php" title="Voltar ao site"><i class="fa-solid fa-house"></i></a>
        <a class="icon-btn" href="admin-products.html" title="Gerenciar produtos">📦</a>
        <a class="btn" href="php/logout.php">Sair</a>
      </div>
    </header>

    <main class="container">
      <h1 class="mt-5">Painel Administrativo</h1>

      <div class="grid cols-3 mt-4">
        <div class="card center admin-card" onclick="location.href='admin-products.html'">
          <div class="admin-icon">📦</div>
          <h3>Produtos</h3>
          <p class="small">Gerencie seu catálogo de produtos</p>
        </div>

        <div class="card center admin-card">
          <div class="admin-icon">📊</div>
          <h3>Relatórios</h3>
          <p class="small">Visualize relatórios de vendas</p>
        </div>

        <div class="card center admin-card">
          <div class="admin-icon">⚙️</div>
          <h3>Configurações</h3>
          <p class="small">Configure sua loja</p>
        </div>
      </div>

      <div class="card mt-5">
        <h2>Visão Geral</h2>
        <div class="grid cols-3 mt-3">
          <div class="kpi">
            <div class="small">Total de Produtos</div>
            <h3 id="totalProducts">0</h3>
          </div>
          <div class="kpi">
            <div class="small">Produtos em Estoque</div>
            <h3 id="inStockProducts">0</h3>
          </div>
          <div class="kpi">
            <div class="small">Categorias</div>
            <h3 id="totalCategories">0</h3>
          </div>
        </div>
      </div>
    </main>

    <footer class="footer">
      <div class="container footer-inner">
        <div>
          <div class="brand"><div class="brand-badge">VF</div>Valify</div>
          <p class="small mt-3">Painel administrativo - Valify Mercado Online</p>
          <small>© 2025 Valify. Todos os direitos reservados.</small>
        </div>
        <div>
          <h4>Administrativo</h4>
          <ul style="list-style:none;padding:0;margin:10px 0 0;line-height:1.9">
            <li><a href="admin-products.html">Gerenciar Produtos</a></li>
            <li><a href="#">Pedidos</a></li>
            <li><a href="#">Relatórios</a></li>
          </ul>
        </div>
        <div>
          <h4>Ajuda</h4>
          <ul style="list-style:none;padding:0;margin:10px 0 0;line-height:1.9">
            <li><a href="#">Suporte</a></li>
            <li><a href="#">Documentação</a></li>
            <li><a href="index.php">Voltar ao site</a></li>
          </ul>
        </div>
      </div>
    </footer>

<script src="assets/js/app.js"></script>
<script src="assets/js/admin-painel.js"></script>

</body>
</html>
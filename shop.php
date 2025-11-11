<?php
// shop.php - Loja de Produtos

// Inclui o guarda de autenticação (garante que apenas logados acessem)
include 'php/check-auth.php'; 

// Se chegou aqui, o usuário está logado.
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Loja - Valify Mercado Online</title>
  <link rel="stylesheet" href="assets/css/style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
</head>
<body>

<header class="header">
  <div class="container nav">
    <a class="brand" href="shop.php">
      <div class="brand-badge">VF</div>
      <div>Valify <span class="small" style="display:block;margin-top:-2px;">Mercado Online</span></div>
    </a>
    
    <form class="search" action="shop.php" method="get">
      <input name="q" id="searchInput" placeholder="Buscar produtos... (ex: arroz, leite)" />
      <button class="btn" type="submit">Buscar</button>
    </form>
    
    <a class="icon-btn" href="shop.php" title="Loja">🛍️</a>
    
    <a class="icon-btn badge" href="cart.html" title="Carrinho">
      🛒
      <span class="count" id="navCartCount">0</span>
    </a>
    
    <?php if(isset($_SESSION['usuario_tipo']) && ($_SESSION['usuario_tipo'] === 'empresa' || $_SESSION['usuario_tipo'] === 'admin')): ?>
      <a class="icon-btn" href="admin.php" title="Painel administrativo">
        <i class="fas fa-chart-bar"></i>
      </a>
    <?php endif; ?>
    
    <div class="nav-auth">
      <?php if(isset($_SESSION['usuario_nome'])): ?>
        <span class="small">Olá, <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?></span>
        <a class="btn ghost" href="php/logout.php">Sair</a>
      <?php endif; ?>
    </div>
  </div>
</header>

<main class="container">
  <h1 class="mt-5">Loja de Produtos</h1>
  <p class="small">Explore nosso catálogo completo de produtos</p>
  
  <div class="card mt-3" style="padding:20px">
    <div class="grid cols-4">
      <div class="field">
        <label for="q">Buscar</label>
        <input id="q" class="input" placeholder="Nome ou código..." />
      </div>
      
      <div class="field">
        <label for="cat">Categoria</label>
        <select id="cat" class="select">
          <option value="">Todas as categorias</option>
          <option value="Hortifruti">Hortifruti</option>
          <option value="Bebidas">Bebidas</option>
          <option value="Laticínios">Laticínios</option>
          <option value="Mercearia">Mercearia</option>
          <option value="Carnes">Carnes</option>
          <option value="Padaria">Padaria</option>
          <option value="Limpeza">Limpeza</option>
          <option value="Higiene">Higiene</option>
        </select>
      </div>
      
      <div class="field">
        <label for="sort">Ordenar por</label>
        <select id="sort" class="select">
          <option value="popular">Mais populares</option>
          <option value="price_asc">Menor preço</option>
          <option value="price_desc">Maior preço</option>
          <option value="name_asc">Nome (A-Z)</option>
        </select>
      </div>
      
      <div class="field">
        <label>&nbsp;</label>
        <button id="apply" class="btn" style="width:100%">
          <i class="fas fa-filter"></i> Aplicar Filtros
        </button>
      </div>
    </div>
  </div>
  
  <div id="products" class="grid cols-4 mt-3">
    <div class="loading">Carregando produtos...</div>
  </div>
</main>

<footer class="footer">
  <div class="container footer-inner">
    <div>
      <div class="brand"><div class="brand-badge">VF</div>Valify</div>
      <p class="small mt-3">Seu mercado online com entrega rápida e produtos fresquinhos. Pague com cartão, Pix ou na entrega.</p>
      <small>© 2025 Valify. Todos os direitos reservados.</small>
    </div>
    <div>
      <h4>Institucional</h4>
      <ul style="list-style:none;padding:0;margin:10px 0 0;line-height:1.9">
        <li><a href="#">Sobre nós</a></li>
        <li><a href="#">Trabalhe conosco</a></li>
        <li><a href="#">Política de privacidade</a></li>
      </ul>
    </div>
    <div>
      <h4>Ajuda</h4>
      <ul style="list-style:none;padding:0;margin:10px 0 0;line-height:1.9">
        <li><a href="cart.html">Carrinho</a></li>
        <li><a href="checkout.html">Checkout</a></li>
        <li><a href="#">Suporte</a></li>
      </ul>
    </div>
  </div>
</footer>

<!-- Modal de Produto -->
<div id="productDialog" class="card" style="position:fixed;inset:0;margin:auto;max-width:800px;max-height:90vh;padding:0;display:none;z-index:999">
  <div style="display:flex;flex-direction:column;height:100%">
    <div style="display:flex;flex:1;overflow:hidden">
      <div style="flex:1;min-width:40%;background:var(--panel);display:grid;place-items:center">
        <img id="dlgImg" alt="Produto" style="width:100%;height:100%;object-fit:cover"/>
      </div>
      <div style="flex:1;padding:24px;overflow-y:auto">
        <div class="tag" id="dlgCat"></div>
        <h2 id="dlgName" class="mt-2"></h2>
        <div class="tags mt-2" id="dlgTags"></div>
        <div class="price-row mt-3" style="margin:16px 0">
          <div class="price" id="dlgPrice"></div>
          <div class="small" id="dlgStock"></div>
        </div>
        <div class="flex mt-3" style="gap:12px;flex-wrap:wrap">
          <input id="dlgQty" type="number" class="input" min="1" value="1" style="max-width:100px"/>
          <button class="btn" id="dlgAdd">
            <i class="fas fa-cart-plus"></i> Adicionar ao carrinho
          </button>
          <button class="btn ghost" id="dlgClose">Fechar</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="assets/js/app.js"></script>
<script src="assets/js/shop.js"></script>
</body>
</html>

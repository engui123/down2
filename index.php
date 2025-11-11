<?php
header("Location: explorar.html");
exit;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Valify - Mercado Online</title>
  <link rel="stylesheet" href="assets/css/style.css" />
  <meta name="description" content="Compre online produtos de mercado: frutas, frios, mercearia e muito mais. Entrega rápida." />
</head>
<body>
  
<header class="header">
  <div class="container nav">
    <a class="brand" href="index.php">
      <div class="brand-badge">VF</div>
      <div>Valify <span class="small" style="display:block;margin-top:-2px;">Mercado Online</span></div>
    </a>
    <form class="search" action="shop.php" method="get">
      <input name="q" placeholder="Buscar produtos... (ex: arroz, leite)" />
      <button class="btn" type="submit">Buscar</button>
    </form>
    <a class="icon-btn" href="shop.php" title="Loja">🛍️</a>
    <a class="icon-btn badge" href="cart.html" title="Carrinho">
      🛒
      <span class="count" id="navCartCount">0</span>
    </a>
    
    <div class="nav-auth">
        <?php if (isset($_SESSION['user'])): ?>
            <span>Olá, <?php echo htmlspecialchars($_SESSION['user']['nome']); ?></span>
            <?php if ($_SESSION['user']['tipo'] === 'empresa' || $_SESSION['user']['tipo'] === 'admin'): ?>
                <a class="btn" href="admin.php">Painel Admin</a>
            <?php else: ?>
                <a class="btn" href="cliente.html">Minha Conta</a>
            <?php endif; ?>
            <a class="btn ghost" href="php/logout.php">Sair</a>
        <?php else: ?>
            <a class="btn ghost" href="#" onclick="openLogin()">Entrar</a>
            <a class="btn" href="#" onclick="openRegister()">Cadastrar</a>
        <?php endif; ?>
    </div>
  </div>
</header>

  <main class="container">
    <section class="hero">
      <div class="hero-inner">
        <div class="card">
          <span class="badge">Entrega hoje</span>
          <h1 style="margin:6px 0 10px">Tudo do seu mercado, <span style="color:var(--brand)">sem sair de casa</span>.</h1>
          <p class="lead">Ofertas diárias em hortifruti, carnes, bebidas, laticínios e mercearia. Escolha, pague e receba rapidinho.</p>
          <div class="kpis">
            <div class="kpi"><div class="small">Produtos</div><h3>1.200+</h3></div>
            <div class="kpi"><div class="small">Entregas/dia</div><h3>900+</h3></div>
            <div class="kpi"><div class="small">Avaliação</div><h3>4.9/5</h3></div>
          </div>
          <div class="mt-4 flex">
            <a class="btn" href="shop.php">Ver ofertas</a>
            <a class="btn ghost" href="#categorias">Categorias</a>
          </div>
        </div>
        <div class="card center" style="padding:0;overflow:hidden">
          <img src="https://images.unsplash.com/photo-1542838132-92c53300491e?q=80&w=1200&auto=format&fit=crop" alt="Sacola de mercado" />
        </div>
      </div>
    </section>

    <section id="categorias" class="mt-6">
      <div class="flex">
        <h2>Categorias</h2><a class="right small" href="shop.php">Ver tudo →</a>
      </div>
      <div class="grid cols-4 mt-3">
        <a class="card center" style="padding:20px" href="shop.php?cat=Hortifruti"> Hortifruti</a>
        <a class="card center" style="padding:20px" href="shop.php?cat=Bebidas"> Bebidas</a>
        <a class="card center" style="padding:20px" href="shop.php?cat=Laticínios"> Laticínios</a>
        <a class="card center" style="padding:20px" href="shop.php?cat=Mercearia"> Mercearia</a>
      </div>
    </section>

    <section class="mt-6">
      <div class="flex">
        <h2>Ofertas da semana</h2><a class="right small" href="shop.php?promo=1">Ver mais →</a>
      </div>
      <div id="homeOffers" class="grid cols-4 mt-3"></div>
    </section>
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

  <script src="assets/js/app.js"></script>
  <script>
    loadOffersHome();
  </script>

  <!-- Modal de Login -->
  <div id="loginModal" class="modal" style="display:none">
    <div class="modal-content" style="max-width:400px;padding:20px">
      <span class="close" onclick="closeModals()">&times;</span>
      <h2>Entrar</h2>
      <form method="POST" action="php/login.php">
        <div class="field">
          <label>Email</label>
          <input type="email" class="input" name="email" required>
        </div>
        <div class="field">
          <label>Senha</label>
          <input type="password" class="input" name="senha" required>
        </div>
        <button type="submit" class="btn mt-3">Login</button>
        <p class="small mt-3">Ainda não tem conta? 
          <a href="#" onclick="switchToRegister()">Cadastre-se</a>
        </p>
      </form>
    </div>
  </div>

  <!-- Modal de Cadastro -->
  <div id="registerModal" class="modal" style="display:none">
    <div class="modal-content" style="max-width:400px;padding:20px">
      <span class="close" onclick="closeModals()">&times;</span>
      <h2>Criar Conta</h2>
      <form method="POST" action="php/register.php">
        <div class="field">
          <label>Nome</label>
          <input type="text" class="input" name="nome" required>
        </div>
        <div class="field">
          <label>Email</label>
          <input type="email" class="input" name="email" required>
        </div>
        <div class="field">
          <label>Senha</label>
          <input type="password" class="input" name="senha" required>
        </div>
        <div class="field">
          <label>Tipo de Conta</label>
          <select class="select" name="tipo" required>
            <option value="cliente">Cliente</option>
            <option value="empresa">Empresa</option>
          </select>
        </div>
        <button type="submit" class="btn mt-3">Cadastrar</button>
        <p class="small mt-3">Já tem conta? 
          <a href="#" onclick="switchToLogin()">Entrar</a>
        </p>
      </form>
    </div>
  </div>

  <script>
    function openLogin(){
      document.getElementById("loginModal").style.display = "flex";
    }
    
    function openRegister(){
      document.getElementById("registerModal").style.display = "flex";
    }
    
    function closeModals(){
      document.getElementById("loginModal").style.display = "none";
      document.getElementById("registerModal").style.display = "none";
    }
    
    function switchToRegister(){
      closeModals();
      openRegister();
    }
    
    function switchToLogin(){
      closeModals();
      openLogin();
    }
    
    // Fechar modal ao clicar fora dele
    window.onclick = function(event) {
      const loginModal = document.getElementById('loginModal');
      const registerModal = document.getElementById('registerModal');
      
      if (event.target == loginModal) {
        closeModals();
      }
      if (event.target == registerModal) {
        closeModals();
      }
    }
  </script>
</body>
</html>
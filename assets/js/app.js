// Valify - App Principal - Integração com Backend PHP

const STORAGE_KEY = 'Valify_cart_v1';

// Formatação de moeda
function formatBRL(n){
  return n.toLocaleString('pt-BR',{style:'currency',currency:'BRL'});
}

// Gerenciamento do Carrinho
function getCart(){
  try{ 
    return JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]'); 
  } catch(e){ 
    return []; 
  }
}

function setCart(items){
  localStorage.setItem(STORAGE_KEY, JSON.stringify(items));
  updateNavCount();
}

function addToCart(product, qty=1){
  const items = getCart();
  const i = items.findIndex(x=>x.id===product.id);
  if(i>=0){ 
    items[i].qty += qty; 
  } else { 
    items.push({
      id:product.id, 
      name:product.name, 
      price:product.price, 
      img:product.img, 
      qty
    }); 
  }
  setCart(items);
}

function removeFromCart(id){
  const items = getCart().filter(x=>x.id!==id);
  setCart(items);
}

function updateQty(id, qty){
  const items = getCart();
  const it = items.find(x=>x.id===id);
  if(!it) return;
  it.qty = Math.max(1, qty|0);
  setCart(items);
}

function updateNavCount(){
  const el = document.getElementById('navCartCount');
  if(!el) return;
  const total = getCart().reduce((a,b)=>a+b.qty,0);
  el.textContent = total;
}

// Buscar produtos do backend PHP
async function fetchProducts(){
  try {
    const res = await fetch('php/get_products.php');
    if(!res.ok) throw new Error('Erro ao carregar produtos');
    
    const data = await res.json();
    
    // Mapear dados do banco para formato do frontend
    return data.map(p => ({
      id: p.id,
      name: p.nome,
      category: p.categoria,
      price: parseFloat(p.preco),
      stock: parseInt(p.estoque) || 0,
      img: p.imagem || 'https://images.unsplash.com/photo-1542838132-92c53300491e?q=80&w=400&auto=format&fit=crop',
      tags: [],
      popular: Math.random() * 10, // Para ordenação
      price_old: parseFloat(p.preco) * 1.15, // Preço antigo (15% maior)
      codigo: p.codigo || ''
    }));
  } catch(error) {
    console.error('Erro ao buscar produtos:', error);
    return [];
  }
}

// Carregar ofertas na home
async function loadOffersHome(){
  updateNavCount();
  
  try {
    const data = await fetchProducts();
    
    if(data.length === 0) {
      const wrap = document.getElementById('homeOffers');
      if(wrap) {
        wrap.innerHTML = '<div class="empty-state"><p>Nenhum produto disponível no momento.</p></div>';
      }
      return;
    }
    
    // Pegar 4 produtos aleatórios
    const offers = data
      .sort(() => Math.random() - 0.5)
      .slice(0, 4);
    
    const wrap = document.getElementById('homeOffers');
    if(wrap) {
      wrap.innerHTML = offers.map(p=>ProductCard(p)).join('');
    }
  } catch(error) {
    console.error('Erro ao carregar ofertas:', error);
  }
}

// Card de produto
function ProductCard(p){
  const hasStock = p.stock > 0;
  const stockBadge = hasStock 
    ? `<span class="tag" style="background:rgba(56,215,159,0.15);color:var(--ok)">Em estoque</span>` 
    : `<span class="tag" style="background:rgba(255,93,93,0.15);color:var(--danger)">Sem estoque</span>`;
  
  return `
    <div class="card product">
      <div class="thumb">
        <img src="${p.img}" alt="${p.name}" onerror="this.src='https://images.unsplash.com/photo-1542838132-92c53300491e?q=80&w=400&auto=format&fit=crop'"/>
      </div>
      <h4>${p.name}</h4>
      <div class="tags">
        ${stockBadge}
        ${p.category ? `<span class="tag">${p.category}</span>` : ''}
      </div>
      <div class="price-row">
        <div class="price">
          ${formatBRL(p.price)} 
          ${p.price_old ? `<small>${formatBRL(p.price_old)}</small>` : ''}
        </div>
        <button class="btn ${!hasStock ? 'secondary' : ''}" 
                onclick='openProduct(${JSON.stringify(p).replace(/'/g, "&apos;")})' 
                ${!hasStock ? 'disabled' : ''}>
          ${hasStock ? 'Ver' : 'Indisponível'}
        </button>
      </div>
    </div>
  `;
}

// Abrir modal de produto (usado globalmente)
window.openProduct = function(product) {
  // Se receber string, é o ID antigo - buscar produto
  if(typeof product === 'string') {
    fetchProducts().then(products => {
      const p = products.find(x => x.id == product);
      if(p) openProduct(p);
    });
    return;
  }
  
  const p = product;
  const dlg = document.getElementById('productDialog');
  if(!dlg) return;
  
  dlg.style.display='block';
  
  const imgEl = document.getElementById('dlgImg');
  const nameEl = document.getElementById('dlgName');
  const catEl = document.getElementById('dlgCat');
  const tagsEl = document.getElementById('dlgTags');
  const priceEl = document.getElementById('dlgPrice');
  const stockEl = document.getElementById('dlgStock');
  const qtyEl = document.getElementById('dlgQty');
  const addBtn = document.getElementById('dlgAdd');
  const closeBtn = document.getElementById('dlgClose');
  
  if(imgEl) imgEl.src = p.img;
  if(nameEl) nameEl.textContent = p.name;
  if(catEl) catEl.textContent = p.category;
  
  if(tagsEl) {
    const tags = [];
    if(p.stock > 0) tags.push('<span class="tag" style="background:rgba(56,215,159,0.15);color:var(--ok)">Disponível</span>');
    tagsEl.innerHTML = tags.join('');
  }
  
  if(priceEl) {
    priceEl.innerHTML = `${formatBRL(p.price)} ${p.price_old ? `<small>${formatBRL(p.price_old)}</small>` : ''}`;
  }
  
  if(stockEl) {
    stockEl.textContent = p.stock > 0 ? `${p.stock} em estoque` : 'Sem estoque';
    stockEl.style.color = p.stock > 0 ? 'var(--ok)' : 'var(--danger)';
  }
  
  if(qtyEl) {
    qtyEl.value = 1;
    qtyEl.max = p.stock;
    qtyEl.disabled = p.stock === 0;
  }
  
  if(addBtn) {
    addBtn.disabled = p.stock === 0;
    addBtn.onclick = () => { 
      const qty = parseInt(qtyEl?.value || 1);
      addToCart(p, qty); 
      alert('✅ Produto adicionado ao carrinho!');
      dlg.style.display='none';
    };
  }
  
  if(closeBtn) {
    closeBtn.onclick = () => { 
      dlg.style.display='none'; 
    };
  }
  
  // Fechar ao clicar fora
  dlg.onclick = (e) => {
    if(e.target === dlg) {
      dlg.style.display='none';
    }
  };
};

// Inicialização
window.addEventListener('load', () => {
  updateNavCount();
  
  // Carregar ofertas se estiver na home
  if(typeof loadOffersHome === 'function' && document.getElementById('homeOffers')) {
    loadOffersHome();
  }
});

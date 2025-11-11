// Shop.js - Página de Loja com Produtos do Banco MySQL

let ALL_PRODUCTS = [];
const qs = new URLSearchParams(location.search);

async function initShop(){
  updateNavCount();
  
  // Mostrar loading
  const productsWrap = document.getElementById('products');
  if(productsWrap) {
    productsWrap.innerHTML = '<div class="loading"><p>Carregando produtos...</p></div>';
  }
  
  try {
    // Buscar produtos do backend
    ALL_PRODUCTS = await fetchProducts();
    
    // Pre-carregar filtros da URL
    const qInput = document.getElementById('q');
    const catSelect = document.getElementById('cat');
    
    if(qInput) qInput.value = qs.get('q') || '';
    if(catSelect) catSelect.value = qs.get('cat') || '';
    
    // Renderizar produtos
    render();
    
    // Event listeners
    const applyBtn = document.getElementById('apply');
    if(applyBtn) {
      applyBtn.addEventListener('click', render);
    }
    
    if(qInput) {
      qInput.addEventListener('keydown',(e)=>{ 
        if(e.key==='Enter'){ 
          e.preventDefault(); 
          render(); 
        }
      });
    }
    
    // Auto-render quando mudar filtros
    if(catSelect) catSelect.addEventListener('change', render);
    const sortSelect = document.getElementById('sort');
    if(sortSelect) sortSelect.addEventListener('change', render);
    
  } catch(error) {
    console.error('Erro ao inicializar loja:', error);
    if(productsWrap) {
      productsWrap.innerHTML = '<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><p>Erro ao carregar produtos. Tente novamente.</p></div>';
    }
  }
}

function render(){
  const qInput = document.getElementById('q');
  const catSelect = document.getElementById('cat');
  const sortSelect = document.getElementById('sort');
  
  const q = qInput ? qInput.value.toLowerCase() : '';
  const cat = catSelect ? catSelect.value : '';
  const sort = sortSelect ? sortSelect.value : 'popular';

  // Filtrar produtos
  let list = ALL_PRODUCTS.filter(p => {
    const matchCategory = !cat || p.category === cat;
    const matchSearch = !q || 
      p.name.toLowerCase().includes(q) || 
      p.category.toLowerCase().includes(q) ||
      (p.codigo && p.codigo.toLowerCase().includes(q));
    
    return matchCategory && matchSearch;
  });

  // Ordenar produtos
  if(sort==='price_asc') {
    list.sort((a,b)=>a.price-b.price);
  } else if(sort==='price_desc') {
    list.sort((a,b)=>b.price-a.price);
  } else if(sort==='name_asc') {
    list.sort((a,b)=>a.name.localeCompare(b.name));
  } else {
    // Popular (padrão) - produtos com mais estoque primeiro
    list.sort((a,b)=>b.stock-a.stock);
  }

  // Renderizar
  const wrap = document.getElementById('products');
  if(!wrap) return;
  
  if(list.length === 0) {
    wrap.innerHTML = `
      <div class="empty-state" style="grid-column: 1/-1;">
        <i class="fas fa-search"></i>
        <p>Nenhum produto encontrado.</p>
        <p class="small">Tente ajustar os filtros ou buscar por outro termo.</p>
      </div>
    `;
    return;
  }
  
  wrap.innerHTML = list.map(ProductCard).join('');
}

// Usar a função ProductCard do app.js (já está definida globalmente)
// Mas vamos garantir que funcione aqui também
if(typeof ProductCard === 'undefined') {
  window.ProductCard = function(p){
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
  };
}

// Inicializar quando a página carregar
window.addEventListener('load', initShop);

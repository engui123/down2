
function renderResume(){
  const wrap = document.getElementById('resume');
  const items = getCart();
  if(items.length===0){ wrap.innerHTML = '<div class="small">Carrinho vazio.</div>'; return; }
  const subtotal = items.reduce((a,b)=>a + b.qty * b.price, 0);
  const frete = subtotal > 149 ? 0 : 14.9;
  const total = subtotal + frete;
  wrap.innerHTML = `
    <div>Itens: <strong>${items.map(i=>i.qty+'x '+i.name).join(', ')}</strong></div>
    <div class="mt-2">Subtotal: <strong>${formatBRL(subtotal)}</strong></div>
    <div>Frete: <strong>${frete===0?'Grátis':formatBRL(frete)}</strong></div>
    <div class="mt-2">Total: <strong>${formatBRL(total)}</strong></div>
  `;
}

function initCheckout(){
  updateNavCount();
  renderResume();
  document.getElementById('checkoutForm').addEventListener('submit', (e)=>{
    e.preventDefault();
    // Fake "order created"
    localStorage.removeItem('Valify_cart_v1');
    location.href = 'success.html';
  });
}

window.addEventListener('load', initCheckout);

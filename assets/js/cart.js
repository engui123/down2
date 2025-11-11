
async function initCart(){
  updateNavCount();
  const data = await fetchProducts();
  const map = Object.fromEntries(data.map(p=>[p.id,p]));
  const items = getCart();

  const tbody = document.getElementById('cartBody');
  if(items.length===0){
    tbody.innerHTML = `<tr><td colspan="5" class="small">Seu carrinho está vazio.</td></tr>`;
    document.getElementById('subtotal').textContent = formatBRL(0);
    return;
  }
  tbody.innerHTML = items.map(it=>{
    const p = map[it.id];
    const line = it.qty * it.price;
    return `
      <tr>
        <td><div class="flex"><img src="${p.img}" style="width:60px;border-radius:10px" alt=""><div><strong>${it.name}</strong><div class="small">${p.category}</div></div></div></td>
        <td><input type="number" min="1" value="${it.qty}" class="input" style="max-width:90px" onchange="onQty('${it.id}', this.value)"></td>
        <td>${formatBRL(it.price)}</td>
        <td>${formatBRL(line)}</td>
        <td><button class="btn danger" onclick="onRemove('${it.id}')">Remover</button></td>
      </tr>
    `;
  }).join('');

  updateSubtotal();
}

function onQty(id, v){
  updateQty(id, parseInt(v||'1',10));
  initCart();
}
function onRemove(id){
  removeFromCart(id);
  initCart();
}

function updateSubtotal(){
  const items = getCart();
  const subtotal = items.reduce((a,b)=>a + b.qty * b.price, 0);
  document.getElementById('subtotal').textContent = formatBRL(subtotal);
}

window.addEventListener('load', initCart);

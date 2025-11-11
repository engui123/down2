const precoInput = form.querySelector("[name='preco']");
precoInput.value = "";
precoInput.placeholder = "0,00";

const precoInput = document.querySelector("[name='preco']");

// Preenche automaticamente os centavos como em apps de banco
precoInput.addEventListener("input", function (e) {
  // Remove tudo que não for número
  let val = e.target.value.replace(/\D/g, "");

  // Garante pelo menos 3 dígitos (ex: 001 → 0,01)
  while (val.length < 3) {
    val = "0" + val;
  }

  // Separa os centavos (últimos 2 dígitos)
  const centavos = val.slice(-2);
  const reais = val.slice(0, -2);

  // Formata os reais com pontos de milhar
  const reaisFormatado = reais.replace(/\B(?=(\d{3})+(?!\d))/g, ".");

  // Atualiza o valor formatado no input
  e.target.value = reaisFormatado + "," + centavos;
});


function closeModal() {
  const modal = document.getElementById('productModal');
  modal.style.display = 'none';

  // Libera interações
  document.body.style.pointerEvents = 'auto';
  document.body.style.userSelect = 'auto';
  document.body.style.overflow = 'auto';
}

window.onclick = function (event) {
  if (event.target === document.getElementById('productModal')) {
    closeModal();
  }
};

// =========================
// CARREGAR PRODUTOS
// =========================
async function loadProducts() {
  try {
    const res = await fetch("php/get_products.php");
    const data = await res.json();
    const tbody = document.getElementById("productsTableBody");

    if (data.length === 0) {
      tbody.innerHTML = `<tr><td colspan="6" class="empty-state">
        <i class="fas fa-box-open"></i><p>Nenhum produto cadastrado</p>
        <button class="btn primary" onclick="showProductModal()"><i class="fas fa-plus"></i> Adicionar Produto</button>
      </td></tr>`;
      return;
    }

    tbody.innerHTML = data.map(prod => `
      <tr data-id="${prod.id}">
        <td class="product-cell">
          <img src="${prod.imagem || 'https://via.placeholder.com/45'}" class="product-img">
          <div class="product-info">
            <span class="product-name">${prod.nome}</span>
            <span class="product-id">#${prod.codigo}</span>
          </div>
        </td>
        <td>${prod.categoria}</td>
        <td>R$ ${prod.preco}</td>
        <td>${prod.estoque}</td>
        <td><span class="badge ${prod.estoque > 0 ? 'success' : 'danger'}">${prod.estoque > 0 ? 'Disponível' : 'Sem estoque'}</span></td>
        <td class="action-buttons">
          <button class="btn small edit" onclick="editProduct(${prod.id})"><i class="fas fa-edit"></i></button>
          <button class="btn small delete" onclick="deleteProduct(${prod.id})"><i class="fas fa-trash"></i></button>
        </td>
      </tr>`).join('');
  } catch (err) { 
    console.error("Erro ao carregar produtos:", err); 
  }
}

document.addEventListener("DOMContentLoaded", loadProducts);

// =========================
// FILTRO DE PRODUTOS
// =========================
document.getElementById("searchProducts").addEventListener("input", function () {
  const termo = this.value.toLowerCase();
  document.querySelectorAll("#productsTableBody tr").forEach(tr => {
    tr.style.display = tr.innerText.toLowerCase().includes(termo) ? "" : "none";
  });
});

// =========================
// EXCLUIR PRODUTO
// =========================
async function deleteProduct(id) {
  if (!confirm("Tem certeza que deseja excluir este produto?")) return;
  const formData = new FormData();
  formData.append("id", id);
  const res = await fetch("php/delete_product.php", { method: "POST", body: formData });
  const result = await res.json();
  if (result.success) { 
    document.querySelector(`tr[data-id='${id}']`)?.remove(); 
    alert("Produto excluído com sucesso!"); 
  } else {
    alert("Erro ao excluir produto: " + (result.error || "Tente novamente."));
  }
}

// =========================
// SALVAR PRODUTO
// =========================
form.addEventListener("submit", async (e) => {
  e.preventDefault();

  // Converte vírgula para ponto antes de enviar para backend (ex: 20,50 -> 20.50)
  const precoVal = precoInput.value.replace(/\./g, '').replace(',', '.');
  precoInput.value = precoVal;

  const formData = new FormData(form);
  try {
    const res = await fetch(form.action, { method: "POST", body: formData });
    const result = await res.json();
    if (result.success) {
      alert("Produto salvo com sucesso!");
      closeModal();
      loadProducts();
    } else {
      alert("Erro: " + (result.error || "Verifique os dados."));
    }
  } catch (err) {
    alert("Erro ao enviar o formulário.");
    console.error(err);
  }
});

// =========================
// EDITAR PRODUTO
// =========================
async function editProduct(id) {
  document.body.style.pointerEvents = 'none';
  document.body.style.userSelect = 'none';
  document.body.style.overflow = 'hidden';

  try {
    const res = await fetch(`php/get_product_by_id.php?id=${id}`);
    const produto = await res.json();
    if (produto.error) { 
      alert("Erro: " + produto.error); 
      return; 
    }

    form.querySelector("[name='id']").value = produto.id;
    form.querySelector("[name='codigo']").value = produto.codigo;
    form.querySelector("[name='nome']").value = produto.nome;
    form.querySelector("[name='categoria']").value = produto.categoria;
    
    // Exibe preço formatado com vírgula e pontos
    // Se o preço vier como número decimal (ex: 20.5), transforma em "20,50"
    let precoFormatado = String(produto.preco).replace('.', ',');
    if (!precoFormatado.includes(',')) {
      precoFormatado += ',00';
    } else {
      // Garantir dois dígitos após a vírgula
      let partes = precoFormatado.split(',');
      if (partes[1].length === 1) precoFormatado += '0';
    }
    priceMask.value = precoFormatado;
    form.querySelector("[name='quantidade']").value = produto.estoque;
    form.querySelector("[name='unidade']").value = produto.unidade || "";
    form.querySelector("[name='descricao']").value = produto.descricao || "";
    form.querySelector("[name='imagem']").value = produto.imagem || "";

    document.getElementById('modalTitle').textContent = "Editar Produto";
    form.action = "php/update_product.php";
    document.getElementById('productModal').style.display = 'flex';
  } catch (err) {
    alert("Erro ao carregar o produto para edição.");
    console.error(err);
  }
}

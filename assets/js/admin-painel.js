
// JavaScript para o painel administrativo principal

document.addEventListener('DOMContentLoaded', function() {
    updateDashboardStats();
});

// Atualizar estatísticas do dashboard
function updateDashboardStats() {
    const products = getProducts();
    const totalProducts = products.length;
    const inStockProducts = products.filter(p => p.stock > 0).length;
    
    // Contar categorias únicas
    const categories = [...new Set(products.map(p => p.category))];
    const totalCategories = categories.length;
    
    document.getElementById('totalProducts').textContent = totalProducts;
    document.getElementById('inStockProducts').textContent = inStockProducts;
    document.getElementById('totalCategories').textContent = totalCategories;
}

// Obter produtos do localStorage (função compartilhada)
function getProducts() {
    try {
        return JSON.parse(localStorage.getItem('Valify_products_v1')) || [];
    } catch (e) {
        return [];
    }
}

// Logout (simulado)
function logout() {
    if (confirm('Deseja sair do painel administrativo?')) {
        window.location.href = 'index.php';
    }
}

// login.js

document.addEventListener("DOMContentLoaded", function() {

    var btnEntrar = document.querySelector("#entrar");
    var btnCadastrar = document.querySelector("#cadastrar");
    var body = document.querySelector("body");

    // Lógica de transição de formulário
    if (btnEntrar) {
        btnEntrar.addEventListener("click", function() {
            body.className = "Entrar-js";
        });
    }

    if (btnCadastrar) {
        btnCadastrar.addEventListener("click", function() {
            body.className = "Cadastrar-js";
        });
    }

    // Lógica de exibição do campo CNPJ
    var selectTipo = document.getElementById('select-tipo');
    var cnpjField = document.getElementById('cnpj-field');
    var cnpjInput = cnpjField ? cnpjField.querySelector('input[name="cnpj"]') : null;

    if (selectTipo && cnpjField) {
        function toggleCnpjField() {
            if (selectTipo.value === 'empresa') {
                cnpjField.style.display = 'flex';
                if (cnpjInput) {
                    cnpjInput.setAttribute('required', 'required');
                    cnpjInput.setAttribute('minlength', '14');
                    cnpjInput.setAttribute('maxlength', '14');
                }
            } else {
                cnpjField.style.display = 'none';
                if (cnpjInput) {
                    cnpjInput.removeAttribute('required');
                    cnpjInput.removeAttribute('minlength');
                    cnpjInput.removeAttribute('maxlength');
                    cnpjInput.value = '';
                }
            }
        }
        toggleCnpjField();
        selectTipo.addEventListener('change', toggleCnpjField);
    }

    // Submissão AJAX cadastro
    var formCadastro = document.getElementById('form-cadastro');
    if (formCadastro) {
        formCadastro.addEventListener('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            var mensagemDiv = document.getElementById('mensagem-cadastro');
            mensagemDiv.textContent = 'Processando...';
            mensagemDiv.style.color = '#7f8c8d';

            fetch("php/cadastro.php", {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                mensagemDiv.textContent = data.message;
                mensagemDiv.style.color = data.success ? '#2ecc71' : '#e74c3c';
                if (data.success) {
                    formCadastro.reset();
                    cnpjField.style.display = 'none';
                    selectTipo.value = 'cliente';
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                mensagemDiv.textContent = 'Erro de conexão ou no servidor. (Verifique o caminho do PHP)';
                mensagemDiv.style.color = '#e74c3c';
            });
        });
    }

    // Submissão AJAX login
    var formLogin = document.getElementById('form-login');
    if (formLogin) {
        formLogin.addEventListener('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            var mensagemDiv = document.getElementById('mensagem-login');
            mensagemDiv.textContent = 'Verificando credenciais...';
            mensagemDiv.style.color = '#7f8c8d';

            fetch("php/login.php", {
                method: 'POST',
                body: formData
            })
            fetch("php/login.php", {
                method: 'POST',
                body: formData
            })
            .then(response => {
                // Cria um clone para ler o texto em caso de falha no JSON
                const clone = response.clone();
                return response.json().catch(() => {
                    // Se falhar, lê o texto e lança um erro com o conteúdo (geralmente HTML/Warnings do PHP)
                    return clone.text().then(text => {
                        console.error("Resposta do servidor não é JSON. Conteúdo:", text);
                        throw new Error("Erro de resposta do servidor. Verifique o console.");
                    });
                });
            })
            .then(data => {
                mensagemDiv.textContent = data.message;
                mensagemDiv.style.color = data.success ? '#2ecc71' : '#e74c3c';
                if (data.success && data.redirect) {
                    // Redirecionamento BEM-SUCEDIDO
                    window.location.href = data.redirect;
                }
            })
            .catch(error => {
                console.error('Erro de Fetch/JSON:', error);
                // Exibe uma mensagem de erro que aponta para o problema no PHP
                mensagemDiv.textContent = 'Falha no Login. Verifique o console para erros do servidor (PHP).';
                mensagemDiv.style.color = '#e74c3c';
            });
        });
    }

});
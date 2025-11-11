// Simulação de banco de dados com localStorage
function getUsers() {
  return JSON.parse(localStorage.getItem("users") || "[]");
}

function saveUser(user) {
  let users = getUsers();
  users.push(user);
  localStorage.setItem("users", JSON.stringify(users));
}

function login(email, senha) {
  let users = getUsers();
  let user = users.find(u => u.email === email && u.senha === senha);
  if (user) {
    localStorage.setItem("loggedUser", JSON.stringify(user));
    if (user.tipo === "empresa") {
      window.location.href = "admin.php";
    } else {
      window.location.href = "cliente.html";
    }
  } else {
    alert("Email ou senha inválidos!");
  }
}

function logout() {
  localStorage.removeItem("loggedUser");
  window.location.href = "login.html";
}

// Cadastro
const registerForm = document.getElementById("registerForm");
if (registerForm) {
  registerForm.addEventListener("submit", e => {
    e.preventDefault();
    const form = new FormData(registerForm);
    const user = {
      nome: form.get("nome"),
      email: form.get("email"),
      senha: form.get("senha"),
      tipo: form.get("tipo")
    };
    saveUser(user);
    alert("Cadastro realizado com sucesso!");
    window.location.href = "login.html";
  });
}

// Login
const loginForm = document.getElementById("loginForm");
if (loginForm) {
  loginForm.addEventListener("submit", e => {
    e.preventDefault();
    const form = new FormData(loginForm);
    login(form.get("email"), form.get("senha"));
  });
}

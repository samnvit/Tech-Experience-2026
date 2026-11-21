document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('form-inscricao');
  const mensagem = document.getElementById('mensagem');
  const contador = document.getElementById('contador');
  const erroOficinas = document.getElementById('erro-oficinas');
  const grupoOficinas = document.getElementById('grupo-oficinas');
  const nome = document.getElementById('nome');
  const email = document.getElementById('email');
  const erroNome = document.getElementById('erro-nome');
  const erroEmail = document.getElementById('erro-email');

  if (!form || !mensagem || !contador) return;

  const MAX = parseInt(mensagem.getAttribute('maxlength'), 10) || 200;

  // Contador de caracteres em tempo real
  function atualizarContador() {
    const restantes = MAX - mensagem.value.length;
    contador.textContent = `Restam ${restantes} caracteres`;
    contador.classList.toggle('limite', restantes <= 20);
  }
  mensagem.addEventListener('input', atualizarContador);
  atualizarContador();

  function mostrarErro(campo, span, texto) {
    span.textContent = texto;
    span.hidden = false;
    if (campo) campo.classList.add('is-invalid');
  }
  function limparErro(campo, span) {
    span.textContent = '';
    span.hidden = true;
    if (campo) campo.classList.remove('is-invalid');
  }

  // Limpa erros ao interagir
  nome.addEventListener('input', () => limparErro(nome, erroNome));
  email.addEventListener('input', () => limparErro(email, erroEmail));
  form.querySelectorAll('input[name="oficinas[]"]').forEach((cb) => {
    cb.addEventListener('change', () => limparErro(grupoOficinas, erroOficinas));
  });

  // Validação preventiva no envio
  form.addEventListener('submit', (event) => {
    let valido = true;

    if (nome.value.trim().length < 3) {
      mostrarErro(nome, erroNome, 'Informe um nome com ao menos 3 caracteres.');
      valido = false;
    }

    if (!email.checkValidity() || email.value.trim() === '') {
      mostrarErro(email, erroEmail, 'Informe um e-mail válido.');
      valido = false;
    }

    const marcadas = form.querySelectorAll('input[name="oficinas[]"]:checked');
    if (marcadas.length === 0) {
      mostrarErro(grupoOficinas, erroOficinas, 'Selecione ao menos uma oficina.');
      valido = false;
    }

    if (!valido) {
      event.preventDefault();
      const primeiro = form.querySelector('.is-invalid');
      if (primeiro) {
        const alvo = primeiro.matches('fieldset') ? primeiro.querySelector('input') : primeiro;
        if (alvo) alvo.focus();
      }
    }
  });
});

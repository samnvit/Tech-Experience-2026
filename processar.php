<?php
declare(strict_types=1);

// 1. Verificação do método HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    header('Content-Type: text/html; charset=UTF-8');
    echo '<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><title>405</title></head>'
       . '<body><h1>405 - Método não permitido</h1>'
       . '<p>Esta página aceita apenas requisições POST. <a href="index.html">Voltar ao formulário</a></p></body></html>';
    exit;
}

// Função de escape de saída
function e(string $valor): string
{
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}

// 2. Leitura e normalização
$nome        = trim((string) ($_POST['nome'] ?? ''));
$email       = trim((string) ($_POST['email'] ?? ''));
$experiencia = trim((string) ($_POST['experiencia'] ?? ''));
$turno       = trim((string) ($_POST['turno'] ?? ''));
$mensagem    = trim((string) ($_POST['mensagem'] ?? ''));
$oficinasRaw = $_POST['oficinas'] ?? [];

// Listas permitidas (whitelists)
$niveisPermitidos    = ['iniciante', 'intermediario', 'avancado'];
$turnosPermitidos    = ['manha', 'noite'];
$oficinasPermitidas  = ['html_css', 'js_dom', 'php_backend'];

// Rótulos para exibição
$rotulosNivel   = ['iniciante' => 'Iniciante', 'intermediario' => 'Intermediário', 'avancado' => 'Avançado'];
$rotulosTurno   = ['manha' => 'Manhã', 'noite' => 'Noite'];
$rotulosOficina = ['html_css' => 'HTML & CSS', 'js_dom' => 'JavaScript & DOM', 'php_backend' => 'PHP Backend'];

// 3. Validação
$erros = [];

if (mb_strlen($nome, 'UTF-8') < 3) {
    $erros[] = 'O nome deve ter no mínimo 3 caracteres.';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erros[] = 'Informe um e-mail com formato válido.';
}

if ($experiencia === '' || !in_array($experiencia, $niveisPermitidos, true)) {
    $erros[] = 'Selecione um nível de experiência válido.';
}

if ($turno === '' || !in_array($turno, $turnosPermitidos, true)) {
    $erros[] = 'Selecione um turno de preferência válido.';
}

$oficinas = [];
if (!is_array($oficinasRaw)) {
    $erros[] = 'O formato das oficinas é inválido.';
} else {
    foreach ($oficinasRaw as $item) {
        if (!is_string($item) || !in_array($item, $oficinasPermitidas, true)) {
            $erros[] = 'Foi enviada uma oficina inválida.';
            $oficinas = [];
            break;
        }
        $oficinas[] = $item;
    }
    $oficinas = array_values(array_unique($oficinas));
    if (empty($oficinas) && !in_array('Foi enviada uma oficina inválida.', $erros, true)) {
        $erros[] = 'Selecione ao menos uma oficina.';
    }
}

if (mb_strlen($mensagem, 'UTF-8') > 200) {
    $erros[] = 'A mensagem deve ter no máximo 200 caracteres.';
}

// 4. Renderização segura
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Resultado da Inscrição — Tech Experience 2026</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header class="site-header">
    <h1>Tech Experience 2026</h1>
  </header>

  <main class="container">
<?php if (!empty($erros)): ?>
    <section class="card erro">
      <h2>Corrija os itens abaixo</h2>
      <ul>
<?php foreach ($erros as $erro): ?>
        <li><?= e($erro) ?></li>
<?php endforeach; ?>
      </ul>
      <a class="btn" href="index.html">Voltar ao formulário</a>
    </section>
<?php else: ?>
    <section class="card ok">
      <h2>Inscrição confirmada!</h2>
      <dl>
        <dt>Nome</dt><dd><?= e($nome) ?></dd>
        <dt>E-mail</dt><dd><?= e($email) ?></dd>
        <dt>Experiência</dt><dd><?= e($rotulosNivel[$experiencia]) ?></dd>
        <dt>Turno</dt><dd><?= e($rotulosTurno[$turno]) ?></dd>
        <dt>Oficinas</dt><dd><?= e(implode(', ', array_map(fn($o) => $rotulosOficina[$o], $oficinas))) ?></dd>
        <dt>Mensagem</dt><dd><?= $mensagem !== '' ? e($mensagem) : '—' ?></dd>
      </dl>
      <a class="btn" href="index.html">Nova inscrição</a>
    </section>
<?php endif; ?>
  </main>

  <footer class="site-footer">
    <p>&copy; 2026 Tech Experience — Desenvolvimento Web</p>
  </footer>
</body>
</html>

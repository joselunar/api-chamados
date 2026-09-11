<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Chamados') ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body>
    <header>
        <a href="<?= site_url('/') ?>">API Chamados</a>
        <nav>
            <span><?= esc(session('user_name')) ?> · <?= esc(session('user_role')) ?></span>
            <a href="<?= site_url('logout') ?>">Sair</a>
        </nav>
    </header>
    <main>
        <?php if (session()->getFlashdata('success')): ?><p class="ok"><?= esc(session()->getFlashdata('success')) ?></p><?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?><p class="err"><?= esc(session()->getFlashdata('error')) ?></p><?php endif; ?>
        <?= $this->renderSection('content') ?>
    </main>
</body>
</html>

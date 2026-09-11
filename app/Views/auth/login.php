<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body>
    <main class="narrow">
        <h1>Help Desk</h1>
        <?php if (session()->getFlashdata('error')): ?><p class="err"><?= esc(session()->getFlashdata('error')) ?></p><?php endif; ?>
        <form method="post" action="<?= site_url('login') ?>">
            <?= csrf_field() ?>
            <label>E-mail <input type="email" name="email" value="usuario@chamados.local" required></label>
            <label>Senha <input type="password" name="password" value="Usuario@123" required></label>
            <button type="submit">Entrar</button>
        </form>
        <p>usuario@chamados.local / Usuario@123<br>atendente@chamados.local / Atendente@123<br>admin@chamados.local / Admin@123</p>
    </main>
</body>
</html>

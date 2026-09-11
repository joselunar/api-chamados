<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<p><a href="<?= site_url('/') ?>">← Voltar</a></p>
<h1><?= esc($ticket['protocol']) ?></h1>
<p><strong><?= esc($ticket['title']) ?></strong></p>
<p><?= nl2br(esc($ticket['description'])) ?></p>
<p>Status: <?= esc($ticket['status']) ?> · Prioridade: <?= esc($ticket['priority']) ?> · Atendente: <?= esc($ticket['assignee']['name'] ?? '—') ?></p>

<?php if (in_array(session('user_role'), ['atendente', 'admin'], true)): ?>
    <form method="post" action="<?= site_url('chamados/' . $ticket['id']) ?>" class="inline">
        <?= csrf_field() ?>
        <select name="status">
            <?php foreach (['aberto', 'em_andamento', 'aguardando_usuario', 'resolvido', 'fechado'] as $status): ?>
                <option value="<?= $status ?>" <?= $ticket['status'] === $status ? 'selected' : '' ?>><?= $status ?></option>
            <?php endforeach; ?>
        </select>
        <select name="priority">
            <?php foreach (['baixa', 'media', 'alta', 'urgente'] as $priority): ?>
                <option value="<?= $priority ?>" <?= $ticket['priority'] === $priority ? 'selected' : '' ?>><?= $priority ?></option>
            <?php endforeach; ?>
        </select>
        <input type="hidden" name="assignee_id" value="<?= (int) session('user_id') ?>">
        <button type="submit">Atualizar</button>
    </form>
<?php endif; ?>

<h2>Comentários</h2>
<?php foreach ($ticket['comments'] as $comment): ?>
    <article>
        <strong><?= esc($comment['author_name']) ?></strong> · <?= esc($comment['author_role']) ?>
        <p><?= nl2br(esc($comment['body'])) ?></p>
    </article>
<?php endforeach; ?>
<form method="post" action="<?= site_url('chamados/' . $ticket['id'] . '/comentarios') ?>">
    <?= csrf_field() ?>
    <textarea name="body" required></textarea>
    <button type="submit">Comentar</button>
</form>

<h2>Histórico</h2>
<ul>
    <?php foreach ($ticket['history'] as $event): ?>
        <li><?= esc($event['author_name']) ?> alterou <strong><?= esc($event['field']) ?></strong> de <?= esc($event['old_value'] ?? '—') ?> para <?= esc($event['new_value'] ?? '—') ?></li>
    <?php endforeach; ?>
</ul>
<?= $this->endSection() ?>

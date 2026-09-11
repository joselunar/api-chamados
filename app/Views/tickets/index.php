<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1>Chamados</h1>
<form method="get" class="filters">
    <input type="search" name="q" value="<?= esc($filters['q'] ?? '') ?>" placeholder="Buscar protocolo ou título">
    <select name="status">
        <option value="">Status</option>
        <?php foreach (['aberto', 'em_andamento', 'aguardando_usuario', 'resolvido', 'fechado'] as $status): ?>
            <option value="<?= $status ?>" <?= ($filters['status'] ?? '') === $status ? 'selected' : '' ?>><?= $status ?></option>
        <?php endforeach; ?>
    </select>
    <select name="priority">
        <option value="">Prioridade</option>
        <?php foreach (['baixa', 'media', 'alta', 'urgente'] as $priority): ?>
            <option value="<?= $priority ?>" <?= ($filters['priority'] ?? '') === $priority ? 'selected' : '' ?>><?= $priority ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Filtrar</button>
</form>

<table>
    <thead>
        <tr><th>Protocolo</th><th>Título</th><th>Prioridade</th><th>Status</th><th>Solicitante</th></tr>
    </thead>
    <tbody>
        <?php foreach ($tickets as $ticket): ?>
            <tr>
                <td><a href="<?= site_url('chamados/' . $ticket['id']) ?>"><?= esc($ticket['protocol']) ?></a></td>
                <td><?= esc($ticket['title']) ?></td>
                <td><?= esc($ticket['priority']) ?></td>
                <td><?= esc($ticket['status']) ?></td>
                <td><?= esc($ticket['requester']['name'] ?? '') ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<p>Página <?= (int) $pager['page'] ?> · <?= (int) $pager['total'] ?> registros</p>

<h2>Abrir chamado</h2>
<form method="post" action="<?= site_url('chamados') ?>">
    <?= csrf_field() ?>
    <label>Título <input name="title" required></label>
    <label>Prioridade
        <select name="priority">
            <option value="baixa">baixa</option>
            <option value="media" selected>media</option>
            <option value="alta">alta</option>
            <option value="urgente">urgente</option>
        </select>
    </label>
    <label>Descrição <textarea name="description" required></textarea></label>
    <button type="submit">Abrir</button>
</form>
<?= $this->endSection() ?>

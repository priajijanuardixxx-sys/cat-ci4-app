<?php if(!empty($participants)): ?>
<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>Nama Peserta</th>
           
        </tr>
    </thead>
    <tbody>
        <?php foreach($participants as $i => $p): ?>
        <tr>
            <td><?= $i + 1 ?></td>
            <td><?= esc($p['full_name']) ?></td>
           
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<div class="text-center text-muted py-3">Belum ada peserta.</div>
<?php endif; ?>

<?php $base = '/' . getEnv('USER_PATH'); ?>
<?php $renderer->wrap('layout', ['title' => 'スタブ一覧']); ?>
<form method="POST" action="<?= $base.'/new' ?>">
<button type="submit">新規スタブ</button>
</form>
<ul>
<?php foreach ($stubs as $stub): ?>
<li><a href="<?= $base.'/'.$stub->getStubId() ?>"><?= eh($stub->getMatcher()->getPath()) ?></a></li>
<?php endforeach; ?>
</ul>

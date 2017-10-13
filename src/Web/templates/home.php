<?php $renderer->wrap('layout', ['title' => 'ホーム']); ?>
<p>こんにちは、<?= eh($user->getDisplayName()) ?> [<?= $user->getUserId()->getValue() ?>]</p>

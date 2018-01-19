<?php $renderer->wrap('layout', ['title' => 'スタブ'.$stub->getStubId()]); ?>
<?php $renderer->section('headerExtra'); ?>
<a href="<?= eh($form['matcher']['path']) ?>" target="_blank">スタブにアクセス</a>
<?php $renderer->endsection('headerExtra'); ?>
<?php if ($errors): ?>
<div class="ui hidden error message">
<i class="icon close"></i>
<div class="header">スタブの更新でエラーが発生しました</div>
</div>
<?php endif; ?>
<?php if ($feedback == 'stubCreated'): ?>
<div class="ui hidden success message">
<i class="close icon"></i>
<div class="header">スタブを作成しました</div>
</div>
<?php elseif ($feedback == 'stubUpdated'): ?>
<div class="ui hidden success message">
<i class="close icon"></i>
<div class="header">スタブを更新しました</div>
</div>
<?php endif; ?>
<form method="POST" class="ui form">
<div class="field">
<label>メソッド</label>
<div class="ui checkbox">
<input type="checkbox" name="matcher[getEnabled]" value="true" tabindex="0" class="hidden" <?= checked($form['matcher']['getEnabled'], 'true') ?>><label>GET</label>
</div>
<div class="ui checkbox">
<input type="checkbox" name="matcher[postEnabled]" value="true" <?= checked($form['matcher']['postEnabled'], 'true') ?>><label>POST</label>
</div>
<div class="ui checkbox">
<input type="checkbox" name="matcher[putEnabled]" value="true" <?= checked($form['matcher']['putEnabled'], 'true') ?>><label>PUT</label>
</div>
<div class="ui checkbox">
<input type="checkbox" name="matcher[patchEnabled]" value="true" <?= checked($form['matcher']['patchEnabled'], 'true') ?>><label>PATCH</label>
</div>
<div class="ui checkbox">
<input type="checkbox" name="matcher[deleteEnabled]" value="true" <?= checked($form['matcher']['deleteEnabled'], 'true') ?>><label>DELETE</label>
</div>
</div><!-- /.field -->
<div class="field">
<label>パス</label>
<input type="text" name="matcher[path]" value="<?= eh($form['matcher']['path']) ?>">
</div><!-- /.field -->
<div class="field <?= @$errors['responder']['statusCode'] ? 'error' : '' ?>">
<label>ステータスコード</label>
<input type="text" name="responder[statusCode]" value="<?= eh($form['responder']['statusCode']) ?>">
<?php if ($error = @$errors['responder']['statusCode']): ?>
<div class="ui pointing red basic label">
<?php if ($error == 'required'): ?>ステータスコードを入力してください<?php endif; ?>
<?php if ($error == 'tooSmall' || $error == 'tooLarge' || $error == 'invalid'): ?>ステータスコードは100から599までの数字にしてください<?php endif; ?>
</div>
<?php endif; ?>
</div><!-- /.field -->
<div class="field">
<label>ヘッダ</label>
<textarea name="responder[header]"><?= eh($form['responder']['header']) ?></textarea>
</div><!-- /.field -->
<div class="field">
<label>ボディ</label>
<textarea name="responder[body]"><?= eh($form['responder']['body']) ?></textarea>
</div><!-- /.field -->
<input type="hidden" name="__METHOD" value="PATCH">
<input type="hidden" name="authorizer[__selection]" value="NoneAuthorizer">
<input type="hidden" name="authorizer[NoneAuthorizer][padding]" value="1">
<input type="hidden" name="_csrf_token" value="<?= $_csrf_token ?>">
<button type="submit" class="ui button primary">更新</button>
<a href="" class="ui button">元に戻す</a>
</form>
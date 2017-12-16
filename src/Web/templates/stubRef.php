<?php $renderer->wrap('layout', ['title' => 'スタブ'.$stub->getStubId()]); ?>
<?php if ($errors): ?>
<ul>
<?php if (@$errors['responder']['statusCode'] == 'required'): ?><li>ステータスコードを入力してください。</li><?php endif; ?>
<?php if (@$errors['responder']['statusCode'] == 'tooSmall' || @$errors['responder']['statusCode'] == 'tooLarge'): ?><li>ステータスコードは100から599までの数字にしてください。</li><?php endif; ?>
</ul>
<?php endif; ?>
<form method="POST">
<label>メソッド</label>
<label><input type="checkbox" name="matcher[getEnabled]" value="true" <?= checked($form['matcher']['getEnabled'], 'true') ?>> GET</label>
<label><input type="checkbox" name="matcher[postEnabled]" value="true" <?= checked($form['matcher']['postEnabled'], 'true') ?>> POST</label>
<label><input type="checkbox" name="matcher[putEnabled]" value="true" <?= checked($form['matcher']['putEnabled'], 'true') ?>> PUT</label>
<label><input type="checkbox" name="matcher[patchEnabled]" value="true" <?= checked($form['matcher']['patchEnabled'], 'true') ?>> PATCH</label>
<label><input type="checkbox" name="matcher[deleteEnabled]" value="true" <?= checked($form['matcher']['deleteEnabled'], 'true') ?>> DELETE</label>
<label>パス</label>
<input type="text" name="matcher[path]" value="<?= eh($form['matcher']['path']) ?>">
<label>ステータスコード</label>
<input type="text" name="responder[statusCode]" value="<?= eh($form['responder']['statusCode']) ?>">
<label>ヘッダ</label>
<textarea name="responder[header]"><?= eh($form['responder']['header']) ?></textarea>
<label>ボディ</label>
<textarea name="responder[body]"><?= eh($form['responder']['body']) ?></textarea>
<input type="hidden" name="__METHOD" value="PATCH">
<input type="hidden" name="authorizer[__selection]" value="NoneAuthorizer">
<input type="hidden" name="authorizer[NoneAuthorizer][padding]" value="1">
<button type="submit">更新</button>
</form>
<?php if ($feedback == 'stubCreated'): ?>
<p>スタブを作成しました。</p>
<?php elseif ($feedback == 'stubUpdated'): ?>
<p>スタブを更新しました。</p>
<?php endif; ?>
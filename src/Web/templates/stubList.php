<?php $base = '/' . getEnv('USER_PATH'); ?>
<?php $renderer->wrap('layout', ['title' => 'スタブ一覧']); ?>
<?php $renderer->section('headerExtra'); ?>
<form method="POST" action="<?= $base.'/new' ?>">
<input type="hidden" name="_csrf_token" value="<?= $_csrf_token ?>">
<button class="ui primary small compact button" type="submit">新規スタブ</button>
</form>
<?php $renderer->endsection('headerExtra'); ?>

<?php if ($feedback == 'stubDeleted'): ?>
<div class="ui hidden success message">
<i class="close icon"></i>
<div class="header">スタブを削除しました</div>
</div>
<?php endif; ?>

<div class="ui relaxed items" id="stubs">
<?php foreach ($stubs as $stub): ?>
<div class="item" data-id="<?= $stub->getStubId() ?>">
<div class="ui nano image"><i class="cube icon"></i></div>
<div class="middle aligned content">
<a class="header" href="<?= $base.'/'.$stub->getStubId() ?>"><?= $base.'/'.$stub->getStubId() ?></a>
<div class="meta">
<span><?= eh($stub->getMatcher()->getPath()) ?></span>
</div>
<div class="extra">
<form method="POST" action="<?= $base.'/'.$stub->getStubId() ?>">
<input type="hidden" name="__METHOD" value="DELETE">
<input type="hidden" name="_csrf_token" value="<?= $_csrf_token ?>">
<button class="ui red basic mini compact button" type="submit">削除</button>
</form>
<div class="ui right floated">
<i class="move icon sort-handle"></i>
</div>
</div>
</div>
</div><!-- /.item -->
<?php endforeach; ?>
</div><!-- /.items -->
<script>
var el = document.getElementById('stubs');
var sortable = Sortable.create(el, {
    handle: '.sort-handle', 
    onEnd: function (e) {
        var stubId = $(e.item).attr('data-id');
        var newIndex = e.newIndex;
        $.post('<?= $base ?>', 
               'stubId[value]='+stubId+'&index='+newIndex+'&__METHOD=PUT&_csrf_token=<?= $_csrf_token ?>');
    }
});
</script>
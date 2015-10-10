Jemand hat im Gästebuch ein Kommentar geschrieben.<br/>
<br/>
Kategorie: <?= $page->commentName?><br/>
Name: <?=$comment->name?><br/>
Message:<p>
<?= nl2br(CHtml::encode($comment->message)); ?>
</p>


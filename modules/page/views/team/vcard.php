BEGIN:VCARD
VERSION:4.0
N:<?= array_reverse(explode(' ', $model->name))[0]?>;<?= explode(' ',$model->name)[0]?>;;;
FN:<?= $model->name?>

ORG:<?=Yii::app()->name?>

PHOTO;MEDIATYPE=image/<?= array_reverse(explode('.',$model->image))[0]?>:<?= Yii::app()->baseUrl.'/bilder/'.$model->image ?>

<?=$model->mobile?"TEL;TYPE=work,cell;VALUE=uri:".$model->mobile."\n":""?>
<?=$model->phone?"TEL;TYPE=work,voice;VALUE=uri:".$model->phone."\n":""?>
ADR;TYPE=work;LABEL="<?=Yii::app()->params['address']['street']?>\n<?=Yii::app()->params['address']['zip']?> <?=Yii::app()->params['address']['city']?>\nDeutschland"
 :;;<?=Yii::app()->params['address']['street']?>;<?=Yii::app()->params['address']['city']?>;<?=Yii::app()->params['address']['state']?>;<?=Yii::app()->params['address']['zip']?>;Germany
<?=$model->email?"EMAIL:".$model->email."\n":""?>
<?=$model->web?"URL:".$model->web."\n":""?>
LOGO;MEDIATYPE=image/jpg:<?=Yii::app()->baseUrl?>/bilder/logo.jpg
SOURCE:<?=$this->createAbsoluteUrl('/page/team/vcard', array('key'=>$model->key))?>

REV:20140301T221110Z
END:VCARD

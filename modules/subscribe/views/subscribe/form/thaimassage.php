<h2>Anmeldebogen</h2>

<?php


	$model = new SubscribeForm();
	$model->labels = array(
			'name'=>'Name',
			'att2'=>'Adresse',
			'email'=>'Email Adresse',
			'att4'=>'Telefon',
			'att5'=>'Welche Erfahrungen mit Massage bringst du mit?',
			'att6'=>'Was wünschst du von diesem Workshop?',
		);
	$model->rules = array(
			array('att2, att4', 'required'),
			array('att4, att5, att6', 'safe'),
		);

	if (isset($_POST['SubscribeForm']))
	{
		$model->attributes = $_POST['SubscribeForm'];
		$model->validate();
		if (!$model->hasErrors())
		{
			$model->subscribeType = 'Thaimassage Modul 1';
			$model->beginText = '
Erlerne die Kunst der Thaimassage Modul 1 (Basiskurs)<br/>
Seminar vom 29. November – 1. Dezember 2013,<br/>
Beginn:  Freitag17 Uhr, Ende:  Sonntag 17 Uhr  <br/>
<br/>
	Der Workshop kostet 285 €.<br/>
	<strong>Bitte überweise innerhalb von 7 Tagen 100 € Anzahlung als Deine Platzreservierung oder den kompletten Betrag</strong><br/>
	<br/>
	Bankverbindung:<br/>
	Inh. Stefan Peters - Massagen<br/>
	HypoVereinsbank <br/>
	Kto: 388008148<br/>
	BLZ: 86020086<br/>
	Verwendungszweck:     Thaikurs DD  und dein…. Name<br/>
	<br/>
	Der Restbetrage in Höhe von 185 € ist zu Beginn des Kurses fällig.<br/>
	Fragen beantworten wir gern per mail und persönlich.<br/>
	Anmeldung:  Milam M. Horn, <br/> Email: spiritoflove@t-online.de, <br/>Tel.: 0175 86 57 233<br/>
';

			$model->endText = 'Vielen Dank für Ihre Anmeldung';

			// 1. to customer
            $mail = new Email('subscribe');
            $mail
				->AddAddress($model->email)
                ->setSubject('Anmeldung zur Veranstaltung: Thaimassage')
                ->setMsg($this->renderPartial('aiajaya.views.mail.subscribe_client', array('model' => $model), true, false))
                ->setFrom('anmelden@balance-dresden.info', 'Anmeldung Balance Zentrum')
                ->send();

			// 2. to admin
            $mail = new Email('subscribe');
            $mail
				->AddAddress('spiritoflove@t-online.de')
                ->setSubject('Anmeldung zur Veranstaltung: Thaimassage')
                ->setMsg($this->renderPartial('aiajaya.views.mail.subscribe_admin', array('model' => $model), true, false))
                ->setFrom('anmelden@balance-dresden.info', 'Anmeldung Balance Zentrum')
                ->send();
		}
	}

	if (!isset($_POST['SubscribeForm']) || $model->hasErrors())
	{
?>
<p>
Erlerne die Kunst der Thaimassage Modul 1 (Basiskurs)<br/>
Seminar vom 29. November – 1. Dezember 2013,<br/>
Beginn:  Freitag17 Uhr, Ende:  Sonntag 17 Uhr  <br/>
</p>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm'); ?>

	<p class="note">Felder mit <span class="required">*</span> müssen ausgefüllt werden.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'att2'); ?>
		<?php echo $form->textField($model,'att2'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'att4'); ?>
		<?php echo $form->textField($model,'att4'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'att5'); ?>
		<?php echo $form->textArea($model,'att5',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'att6'); ?>
		<?php echo $form->textArea($model,'att6',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row">
		Der Workshop kostet 285 €.
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Submit'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
<?php
	} else {
?>
	Vielen Dank für die Anmeldung.<br/>
	<br/>
	Der Workshop kostet 285 €.<br/>
	<strong>Bitte überweise innerhalb von 7 Tagen 100 € Anzahlung als Deine Platzreservierung oder den kompletten Betrag.</strong><br/>
	<br/>
	Bankverbindung:<br/>
	Inh. Stefan Peters - Massagen<br/>
	HypoVereinsbank <br/>
	Kto: 388008148<br/>
	BLZ: 86020086<br/>
	Verwendungszweck:     Thaikurs DD  und dein…. Name<br/>
	<br/>
	Der Restbetrage in Höhe von 185 € ist zu Beginn des Kurses fällig.<br/>
	Fragen beantworten wir gern per mail und persönlich.<br/>
	Anmeldung:  Milam M. Horn,<br/> Email: spiritoflove@t-online.de,<br/> Tel.: 0175 86 57 233<br/>
<?php
	}
?>

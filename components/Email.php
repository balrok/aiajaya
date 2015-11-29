<?php

class Email
{
	public $mail;
	public $subject;
	public $msg;
	public $txtmsg;
	public $scenario;

	public function __get($name)
	{
		switch ($name)
		{
			case 'FromName':
			case 'From':
			case 'Subject':
			case 'Body':
				return $this->mail->$name;
		}
		return $this->$name;
	}

	public function __construct($scenario='none')
	{
		$this->scenario = $scenario;
		$this->mail = Yii::createComponent('aiajaya.extensions.mailer.EMailer');
		$this->mail->yiiDefaultMail = null; //Yii::app()->params['email']['defaultMail'];
		$this->mail->yiiDebug = false; //!Yii::app()->params['email']['send'];
		$this->mail->CharSet = "UTF-8";
		$this->mail->AltBody = "Ihr E-Mail Programm muss HTML unterstützen, damit Sie diese E-Mail korrekt lesen können.";
		$adminEmails = Yii::app()->params['adminEmails'];
		$adminEmails[] = Yii::app()->params['adminEmail'];
		$this->AddAddress($adminEmails[0]);
		foreach ($adminEmails as $adminEmail)
			$this->AddBCC($adminEmail);
	}

	public function validateAddress($address)
	{
		return $this->mail->validateAddress($address);
	}

	public function setFrom($mail, $display=null)
	{
		if ($display == null)
			$display = $mail;
		$this->mail->SetFrom($mail, $display);
		return $this;
	}

	public function addBCC($mail)
	{
		$this->mail->AddBCC($mail);
		return $this;
	}

    /**
     * Adds an email address with an optional display name.
     * @param string $mail an email address
     * @param string $display the display name
     * @return Email
     */
    public function addAddress($mail, $display='')
	{
		$this->mail->AddAddress($mail, $display);
		return $this;
	}

	public function setSubject($subj)
	{
		$this->subject = $subj;
		return $this;
	}

	public function wrapContent($_scenario_, $data)
	{
		foreach($data as $k=>$v)
			$$k = $v;
		ob_start();
		include YiiBase::getPathOfAlias('aiajaya.components.email_template.'.$_scenario_).'.php';
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	public function setTxtMsg($msg)
	{
		$this->txtmsg = $msg;
		return $this;
	}

	// TODO it should be possible to set the subject (and maybe other values) from inside the msg
	public function setMsg($msg)
	{
		$this->msg = $msg;
		return $this;
	}

	public function getMsgHtml()
	{
		return $this->wrapContent($this->scenario, array('title'=>$this->subject, 'msg'=>$this->msg));
	}

	// will send the mail and in some cases print debug
	public function send() {
		static $count = 0;
		$this->mail->Subject = $this->subject;
		$this->mail->MsgHTML($this->getMsgHtml());
		if ($this->txtmsg) {
			$this->mail->AltBody = $this->wrapContent($this->scenario.'_txt', array('title'=>$this->subject, 'msg'=>$this->txtmsg));
		}
		if (constant('YII_DEBUG'))
		{
			$count++;
			// dont spam so much
			if ($count < 5)
				dump($this->mail, 4, true);
		}
		$this->mail->Send();
		Yii::log($this->getMsgHtml().'<hr />'.dumpc($this->mail, 3, true), 'info', "email");
		//die($this->getMsgHtml());
		return $this;
	}

	public function addAttachment($path, $name, $encoding = "base64", $type = "application/octet-stream") {
		$this->mail->addAttachment($path, $name, $encoding, $type);
		return $this;
	}

	public function getAttachments() {
		return $this->mail->GetAttachments();
	}

}

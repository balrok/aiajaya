<?php

/**
 * ContactForm class.
 * ContactForm is the data structure for keeping
 * contact form data. It is used by the 'contact' action of 'SiteController'.
 */
class SubscribeForm extends CFormModel
{
	public $name;
	public $email;

	// only for email
	public $subscribeType = '';
	public $beginText = '';
	public $endText = '';

	// generic attributes
	public $att1;
	public $att2;
	public $att3;
	public $att4;
	public $att5;
	public $att6;
	public $att7;
	public $att8;
	public $att9;
	public $att10;
	public $att11;
	public $att12;
	public $att13;
	public $att14;
	public $att15;
	public $att16;
	public $att17;
	public $att18;
	public $att19;

	public $labels;
	public $rules;

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		if ($this->rules)
		{
			$rules = $this->rules;
			$rules[] = array('name, email', 'required');
			return $rules;
		}
		return array('name, email', 'required');
	}

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		if ($this->labels)
		{
			$labels = $this->labels;
			if (!isset($labels['name']))
				$labels['name'] = 'Ihr Name';
			if (!isset($labels['email']))
				$labels['email'] = 'Ihre Email';
			return $labels;
		}
		return array(
			'name'=>'Ihr Name',
			'email'=>'Ihre Email',
		);
	}
}

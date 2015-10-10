<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	public function authenticate()
	{
		$this->username = strtolower($this->username);
		$this->password = strtolower($this->password);

		$user = User::model()->find('LOWER(name)=?',array($this->username));

		if($user===null)
		{
			// when there is only 1 user (thats my admin) then we can create a new account for the customer
			if (User::model()->count() < 2)
			{
				$user = new User();
				$user->name = $this->username;
				$user->password = $user->hashPassword($this->password);
				$user->active = true;
				$user->save();
				$this->errorCode=self::ERROR_NONE;
				return !$this->errorCode;
			}
			else
 				$this->errorCode=self::ERROR_USERNAME_INVALID;
		}
		else if(!$user->validatePassword($this->password))
			$this->errorCode=self::ERROR_PASSWORD_INVALID;
		else
			$this->errorCode=self::ERROR_NONE;

		return !$this->errorCode;
	}
}

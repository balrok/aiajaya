<?php


/**
 * Created by JetBrains PhpStorm.
 * User: dkhan
 * Date: 21.06.12
 * Time: 20:57
 * To change this template use File | Settings | File Templates.
 */
class GoogleApis extends CApplicationComponent {


    // Credentials can be obtained at https://code.google.com/apis/console
    // See http://code.google.com/p/google-api-php-client/wiki/OAuth2 for more information
    private $clientId = null;
    private $clientSecret = null;

    // Make sure that this matches a registered redirect URI for your app
    private $redirectUri = null;

    // This is the API key for 'Simple API Access'
    private $developerKey = null;

    private $client = null;


    public function init() {
        $path = dirname(__FILE__) . '/lib/google-api-php-client/src';
        set_include_path(get_include_path() . PATH_SEPARATOR . $path);
		require_once 'Google_Client.php';
        parent::init();
    }


    /**
     * @param $service
     * @return apiService
     * @throws CException
     */
    public function serviceFactory($service) {

        $serviceClassName = sprintf('Google_%sService', $service);
        $serviceClassFileName = sprintf('%s.php', $serviceClassName);

        $servicePath = realpath(dirname(__FILE__).'/lib/google-api-php-client/src/contrib/'.$serviceClassFileName);
        if(!$servicePath) {
            throw new CException(sprintf('The API for %s (%s) was not found!', $service, $serviceClassName));
        }

        require_once($servicePath);

        return new $serviceClassName($this->getClient());
    }


    /*
     * Getters, setters and helpers
     */

    /**
     * @return Google_Client
     */
    public function getClient() {

        if(is_null($this->client)) {
            $this->setClient($this->createClient());
        }
        return $this->client;
    }

    /**
     * @param Google_Client $client
     */
    private function setClient(Google_Client $client) {
        $this->client = $client;
    }

    /**
     * @return Google_Client
     */
    private function createClient() {

        $client = new Google_Client();
        $client->setClientId($this->getClientID());
        $client->setClientSecret($this->getClientSecret());
        $client->setRedirectUri($this->getRedirectUri());
        $client->setDeveloperKey($this->getDeveloperKey());
        $client->setApplicationName(yii::app()->name);

        return $client;
    }

    public function setRedirectUri($redirectUri) {
        $this->redirectUri = $redirectUri;
    }

    public function getRedirectUri() {
        return $this->redirectUri;
    }

    public function setClientSecret($clientSecret) {
        $this->clientSecret = $clientSecret;
    }

    public function getClientSecret() {
        return $this->clientSecret;
    }

    public function setDeveloperKey($developerKey) {
        $this->developerKey = $developerKey;
    }

    public function getDeveloperKey() {
        return $this->developerKey;
    }

    public function setClientId($clientID) {
        $this->clientId = $clientID;
    }

    public function getClientId() {
        return $this->clientId;
    }



}

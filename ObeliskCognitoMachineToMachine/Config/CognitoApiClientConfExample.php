<?php namespace ObeliskModules\ObeliskCognitoMachineToMachine\Config; //namespace ObeliskModules\ObeliskCognitoApi\Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Parametros  de la userpool  de cognito
 *
 * @link https://docs.aws.amazon.com/cognito/latest/developerguide/cognito-user-identity-pools.html
 */
class CognitoApiClientConfigFile extends BaseConfig
{
  public $aliasToken = '';
  public $client_id =  '';
  public $client_secret = '';
  public $base_uri = '';
  public $redirec_uri = '';
  public $grant_type =  'client_credentials';
  public $timeout = 3.0;
  public $verifySSL= true;
  public $region = "";
  public $userPoolId = "";
  public $leeway = 10;


}

<?php namespace ObeliskModules\ObeliskCognitoMachineToMachine\Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Parametros  de la userpool  de cognito
 *
 * @link https://docs.aws.amazon.com/cognito/latest/developerguide/cognito-user-identity-pools.html
 */
class Cognito extends BaseConfig
{
  public $apiDefault = [
    'aliasToken' => '', // todo en la siguiente version cambiar a un valor sacado del resto por ejemenplo sha256(clienteid.userpolid.'obelisk')
    'client_id' =>  '',
    'client_secret' => '',
    'base_uri' => '',
    'redirec_uri' => '',
    'grant_type' =>  'client_credentials',
    'timeout' => 3.0,
    'verifySSL'=> true,
    'region' => "",
    'userPoolId' => "",
    'leeway' => 10,

  ];


}

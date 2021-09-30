<?php
namespace  ObeliskModules\ObeliskCognitoMachineToMachine\Libraries\ObeliskCognitoMachineToMachineLibrary;
use Firebase\JWT\JWT;
use Firebase\JWT\JWK;


use  \ObeliskModules\ObeliskCognitoMachineToMachine\Entities\TokenEntity;
use  \ObeliskModules\ObeliskCognitoMachineToMachine\Libraries\ObeliskCognitoMachineToMachineLibrary\ObeliskAuthMachineToMachineInterface;
use  \ObeliskModules\ObeliskCognitoMachineToMachine\Models\ModelCognitoToken;


class ObeliskCognitoMachineToMachineLibrary implements ObeliskAuthMachineToMachineInterface
{
  private $cognitoTokenModel;
  public function __construct(string $apiGroup ='apiDefault'){
    //inicializo modelo

    $this->cognitoTokenModel = model('\ObeliskModules\ObeliskCognitoMachineToMachine\Models\ModelCognitoToken') ;
    $this->cognitoTokenModel->setApiConfig($apiGroup);

  }
  /**
   * Método que obtine el token de acceso para una api configurada
   *
   * @return token. Devuelve un token de acceso a la api configurada en archivode configuracion
   * @throws Exception Si se produce alguna anomalia en la comunicacion con Aws Cognito.
   */
  public function getAccessToken(){

      // $token =   $this->cognitoTokenModel->getTokenFromDB();
      // if ( $token === null ||   $token->is_expired()){
      //   $response =  $this->cognitoTokenModel->getTokenFromCognito();
      //   $token = new  TokenEntity();
      //   $token->token = json_decode($response->getBody())->access_token;
      //   $token->expires_at = (json_decode($response->getBody())->expires_in + time());
      //   $this->cognitoTokenModel->setTokenToDB($token);
      // }
      //  // d($token->expires_at);
      // return $token->token;
      return $this->cognitoTokenModel->getToken();
    }

}

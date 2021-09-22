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
  public function __construct(string $configFile){
    //inicializo modelo

    $this->cognitoTokenModel = model('\ObeliskModules\ObeliskCognitoMachineToMachine\Models\ModelCognitoToken') ;
    $this->cognitoTokenModel->setConfigFile($configFile);

  }
  /**
   * Método que obtine el token de acceso para una api configurada
   *
   * @return token. Devuelve un token de acceso a la api configurada en archivode configuracion
   * @throws Exception Si se produce alguna anomalia en la comunicacion con Aws Cognito.
   */
  public function getAccessToken(){
    if(!isset($this->cognitoTokenModel)){
      return $this->cognitoTokenModel->getToken();
    }
    return false;
  }

}

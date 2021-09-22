<?php namespace  ObeliskModules\ObeliskCognitoMachineToMachine\Libraries\ObeliskCognitoMachineToMachineLibrary;

/**
 * Interface que fija los metodos necesarios para dotar de Logeo a Codeignirer.
 *
 * @todo Documetar esta primera descripcion mejor
 *
 * @package    ObeliskModules\ObeliskCognito\Libraries;
 * @author     Obelisk
 * @license    https://opensource.org/licenses/MIT	MIT License
 * @link       https://obelisk-services.com
 * @filesource
 */



interface ObeliskAuthMachineToMachineInterface
{
  /**
   * Método que obtine el token de acceso para una api configurada
   *
   * @return token. Devuelve un token de acceso a la api configurada en archivode configuracion
   * @throws Exception Si se produce alguna anomalia en la comunicacion con Aws Cognito.
   */
  public function getAccessToken();

}

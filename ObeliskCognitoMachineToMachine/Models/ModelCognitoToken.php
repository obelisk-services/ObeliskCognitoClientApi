<?php

namespace ObeliskModules\ObeliskCognitoMachineToMachine\Models;
use  GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\ClientException;
use Firebase\JWT\JWT;
use Firebase\JWT\JWK;
//use ObeliskModules\ObeliskEntitiesModels\Models\ObeliskBaseModel;
use   \ObeliskModules\ObeliskCognitoMachineToMachine\Entities\TokenEntity;
use CodeIgniter\Model;
class ModelCognitoToken extends Model {
  //configuracion modelo
  protected $table = 'tokens';
  protected $primaryKey = 'id_token';
  protected $allowedFields = ['token', 'expires_at', 'alias'];
  protected $returnType    = '\ObeliskModules\ObeliskCognitoMachineToMachine\Entities\TokenEntity';
  protected $DBGroup = 'tokensApi';
  protected $useSoftDeletes = false;
  protected $useTimestamps = false;
  private  $aliasToken;

  //archivo configuracion
  private $config;
  //variables userpool
  private  $client_id;
  private  $client_secret;
	private  $expire_at ;
  private  $region;
  private  $userPoolId;
  private  $publicKidUrl;

  //propiedades para el curl
	private  $headers;
	private  $params;
	private  $client;
	private  $verifySSL;


    protected function initialize()
    {
      $this->config = config('CognitoApiClientConfigFile', false);
    }

	public function setApiConfig(string $apiGroup ='apiDefault')
	{

    //configuracion userPool
		$this->client_id =  $this->config->{$apiGroup}['client_id'];
		$this->client_secret = $this->config->{$apiGroup}['client_secret'];
    $this->region = $this->config->{$apiGroup}['region'];
    $this->userPoolId = $this->config->{$apiGroup}['userPoolId'];
    $this->publicKidUrl = sprintf('https://cognito-idp.%s.amazonaws.com/%s/.well-known/jwks.json', $this->config->{$apiGroup}['region'], $this->config->{$apiGroup}['userPoolId']);
    //configuracion curl
		$this->headers = ['Content-Type' => 'application/x-www-form-urlencoded', 'Authorization'=>'Basic ' .
													 base64_encode(utf8_encode($this->config->{$apiGroup}['client_id'] . ":".  $this->config->{$apiGroup}['client_secret']))
								];
		$this->params =[
			'grant_type' => 'client_credentials',
			//'scope'=> 'https;//licencias.nuberu.obelisk-services.com/api.prueba',
			'redirec_uri'=>$this->config->{$apiGroup}['redirec_uri']
		];
		$this->client = new Client([

		//Base URI is used with relative requests
			'base_uri' => $this->config->{$apiGroup}['base_uri'],
		// You can set any number of default request options.
			'timeout'  => $this->config->{$apiGroup}['timeout'],

		]);
		$this->verifySSL=$this->config->{$apiGroup}['verifySSL'];
    // para la bd. lo uso como clave Me permite usar la libreria para mas de una api a la vez
    $this->aliasToken = $this->config->{$apiGroup}['aliasToken'];
	}
  private function getTokenFromCognito(){
    try{
      return $this->client->request('POST', 'token',['verify'=> $this->verifySSL, 'headers' => $this->headers, 'query' => $this->params ]);

    }catch (RequestException $e) {
      echo Psr7\Message::toString($e->getRequest());
      if ($e->hasResponse()) {
        echo Psr7\Message::toString($e->getResponse());
      }
    }catch( ClientException $e){
      echo Psr7\Message::toString($e->getRequest());
      echo Psr7\Message::toString($e->getResponse());
    }
  }
  private function getTokenFromDB(){
    $result = $this->where('alias',	$this->aliasToken)->find();
    return sizeof($result)!== 1 ? null : $result[0];
  }
  private function setTokenToDB($token){
    // dd($token);
    $tokenDB=$this->getTokenFromDB();

    $token->id_token = !empty($tokenDB)?$tokenDB->id_token:null;
    $token->alias = $this->aliasToken;
    $this->save($token);
  }

	public  function getToken(){
    $token = $this->getTokenFromDB();
   d($token->is_expired());

		if ( $token === null ||   $token->is_expired()){
			$response = $this->getTokenFromCognito();
			$token = new  TokenEntity();
      $token->token = json_decode($response->getBody())->access_token;
			$token->expires_at = (json_decode($response->getBody())->expires_in + time());
      $this->setTokenToDB($token);
		}
     // d($token->expires_at);
		return $token->token;
	}

	// private  function get_public_key(){
	//   if (!isset($this->pem)){
  //
	//     $ch = curl_init($this->publicKidUrl);
	//     curl_setopt($ch, CURLOPT_RETURNTRANSFER , true);
	//     $jwk_public = curl_exec($ch);
	//     $jwk_public_array = json_decode($jwk_public, true);
	//   //las parseamos y las ponemos en session
  //
	//     $public_keys = json_decode($jwk_public, true);
	//     $this->pem = $public_keys;
	//   }
	//   return $this->pem;
	// }
  //
	// /** Verifica la que los token utilizados son validos en tres pasos: Verificacion de la estructura, verifica la firma del token y verifica las "claims" siguiendo el proceso
	// * que se recomienda en la documnetacion de Aws Cognito
	// * @link https://docs.aws.amazon.com/cognito/latest/developerguide/amazon-cognito-user-pools-using-tokens-verifying-a-jwt.html Verificacion de JWT Aws Cognito
	// * @param type $token
	// * @return boolean
	// */
	// private  function verify_json($token){
  //
  //
	//   $public_keys = self::get_public_key();
  //
	//   try{
	//     $claims = (array)JWT::decode($token, JWK::parseKeySet($public_keys),  array('RS256')); //verifica estructura, codificacion, firma, qye se haya creado antes de ahora, y que haya expirado
	// 		d($claims);
	//    }
	//    catch (\Exception $e){
	//        var_dump($e); die();
	//        return false;
	//    }
	//    try {
  //
	//        //verificamos los claims
	// 			 $region = "eu-west-1";
 	// 			$userPoolId = "eu-west-1_S00dsiRrT";
  //
	//      $expectedIss = sprintf('https://cognito-idp.%s.amazonaws.com/%s', $region, $userPoolId);
	//       // if ($claims['iss'] !== $expectedIss || $claims['exp'] < time()|| $claims['token_use'] !== 'access' ) {
	//      if ($claims['client_id']!=="7qlf3prjpngn5glih7vfamv568" ||$claims['iss'] !== $expectedIss || $claims['token_use'] !== 'access' ) {
	//            return false;
	//       }
  //
	//      return true;
  //
	//    } catch (\Exception $ex) { //Capturamos las excepciones posibles y negamos la verificacion para evitar mensajes que puedan ser utilizados por usuarios malicionos
	//        // var_dump( $ex); die();
	//        return false;
	//    }

	// }



}

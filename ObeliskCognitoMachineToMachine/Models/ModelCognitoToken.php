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




	public function setConfigFile($configFile='CognitoApiClientConfigFile')
	{
    $config = config($configFile, false);
    //configuracion userPool
		$this->client_id =  $config->client_id;
		$this->client_secret = $config->client_secret;
    $this->region = $config->region;
    $this->userPoolId = $config->userPoolId;
    $this->publicKidUrl = sprintf('https://cognito-idp.%s.amazonaws.com/%s/.well-known/jwks.json', $config->region, $config->userPoolId);
    //configuracion curl
		$this->headers = ['Content-Type' => 'application/x-www-form-urlencoded', 'Authorization'=>'Basic ' .
													 base64_encode(utf8_encode($config->client_id . ":".  $config->client_secret))
								];
		$this->params =[
			'grant_type' => 'client_credentials',
			//'scope'=> 'https;//licencias.nuberu.obelisk-services.com/api.prueba',
			'redirec_uri'=>$config->redirec_uri
		];
		$this->client = new Client([

		//Base URI is used with relative requests
			'base_uri' => $config->base_uri,
		// You can set any number of default request options.
			'timeout'  => $config->timeout,

		]);
		$this->verifySSL=$config->verifySSL;
    // para la bd. lo uso como clave Me permite usar la libreria para mas de una api a la vez
    $this->aliasToken = $config->aliasToken;
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
}

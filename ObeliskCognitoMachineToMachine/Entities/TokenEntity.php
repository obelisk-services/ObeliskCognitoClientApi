<?php


namespace ObeliskModules\ObeliskCognitoMachineToMachine\Entities;
//use CodeIgniter\Entity;

/**
 * ENtity Token
 *
 * @author garbanzo
 */
//use ObeliskModules\ObeliskEntitiesModels\Entities\ObeliskBaseEntity;
use CodeIgniter\Entity\Entity;
// use CodeIgniter\Entity;
class TokenEntity extends Entity {
  // protected $cast =[
  //   'expires_at' => 'integer'
  // ];

  // protected $attributesToHtmlOption = [

  // ];
  public  function is_expired():bool{
       // d( $this->attributes['expires_at']);
       // d( time());
    if (($this->attributes['expires_at']) < time()){//los 10 de margen de error
      return true;
    }
    return false;
  }

}

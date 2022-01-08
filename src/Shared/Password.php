<?php 
namespace OpenForms\Shared;
define(PASSWORD_DEFAULT, '1fc281');
class Password{
    public function __construct(private $value)
    {
     
    }

    public function validate($email)
    {
        $user_repository=new UserRepository();
        $user=$user_repository->findByEmail($email);
        return password_verify( $this->value,$user['password'])?$user:false;    
    }

    public function encrypt()
    {
        return password_hash($this->value, PASSWORD_DEFAULT);
    }
}
 ?>

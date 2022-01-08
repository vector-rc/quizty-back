<?php 
namespace OpenForms\Shared;
class Email{
    public function __construct(private $value)
    {
     
    }

    public function validate()
    {
        return filter_var($this->value, FILTER_VALIDATE_EMAIL);     
    }
}
 ?>

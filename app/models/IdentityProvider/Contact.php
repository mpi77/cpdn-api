<?php
namespace CpdnAPI\Models\IdentityProvider;

use Phalcon\Mvc\Model;

class Contact extends Model 
{

    /**
     * @var integer
     *
     */
    public $id;

    /**
     * @var string
     *
     */
    public $firstname;

    /**
     * @var string
     *
     */
    public $surname;

    /**
     * @var string
     *
     */
    public $email;

    /**
     * @var string
     *
     */
    public $phone;


    /**
     * Validations
     */
    public function validation()
    {        
        $this->validate(new Email(array(
            "field" => "email",
            "required" => true
        )));
        if ($this->validationHasFailed() == true) {
            return false;
        }
    }
    
    public function initialize() {
		$this->setConnectionService ( 'idpDb' );
		
		$this->hasOne ( "id", "CpdnAPI\Models\IdentityProvider\Profile", "contactId", array (
				'alias' => 'oProfile' 
		) );
	}
    public function beforeValidationOnCreate() {
    }
    public function beforeValidationOnUpdate() {
    }
    public function columnMap() {
    	return array (
    			'id' => 'id',
    			'first_name' => 'firstname',
    			'surname' => 'surname',
    			'email' => 'email',
    			'phone' => 'phone'
    	);
    }

    /**
     * Get contact in defined output structure.
     *
     * @param Contact $contact
     * @return array
     */
    public static function getContact(Contact $contact) {
    	return array (
    			"firstname" => $contact->firstname,
    			"surname" => $contact->surname,
    			"email" => $contact->email,
    			"phone" => $contact->phone
    	);
    }
}

<?php

namespace CpdnAPI\Models\Network;

use Phalcon\Mvc\Model;

class ObjectMember extends Model 
{

	public function initialize()
	{
		$this->setConnectionService('networkDb');
	}
	
    /**
     * @var integer
     *
     */
    public $object_id;

    /**
     * @var integer
     *
     */
    public $node_id;


}

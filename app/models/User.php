<?php

namespace app\models;
// echo "iuser here";die();
// use app\extensions\helper\CommonHelper;
use lithium\aop\Filters;
use lithium\util\Text;

class User extends \lithium\data\Model
{
	 protected $_meta = [
        'connection' => 'default',
    ];
    public $validates = ['firstname' => 'Please enter ','lastname' => 'Please enter '];
   
    }
?>

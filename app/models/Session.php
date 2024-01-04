<?php

namespace app\models;
// echo "iuser here";die();
// use app\extensions\helper\CommonHelper;
use lithium\aop\Filters;
use lithium\util\Text;

class Session extends \lithium\data\Model
{
	  protected $_meta = array(
        'source' => 'sessions',
        'key' => 'session_uuid'
    );

    }
?>

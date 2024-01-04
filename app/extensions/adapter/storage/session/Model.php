<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2010, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace app\extensions\adapter\storage\session;
use app\extensions\helper\CommonHelper;
use lithium\core\ConfigException;
use lithium\core\Libraries;
use lithium\data\Entity;
use lithium\core\AutoConfigurable;


/**
 * The `Model` adapter is a simple session adapter which allows session data to be written to a
 * persistent storage that can be queried by a Lithium model.
 * In order to use this adapter, you must first create a model that will be used to connect the
 * adapter to.
 *
 * For example:
 *
 * {{{
 * use \lithium\storage\Session;
 *
 * Session::config(array(
 *  'default' => array('adapter' => 'Model', 'model' => 'Session')
 * ));
 * }}}
 *
 * This will cause your users' session handling to be taken care of by the normal
 * model CRUD operations.
 */
class Model {
    use AutoConfigurable;
    /**
     * Holds an instance of the record for the current session. Usually an instance of
     * `lithium\data\entity\Record` or `lithium\data\entity\Document`.
     *
     * @var object
     */
    protected $_data = null;

    /**
     * The fully namespaced class name which the adapter uses to read and write session data.
     *
     * @var string
     */
    protected $_model = null;
    protected $_autoConfig=[];
    /**
     * Default ini settings for this session adapter
     *
     * @var array Keys are session ini settings, but without the `session.` namespace.
     */
    protected $_defaults = array(
        'cookie_secure' => true,
        'cookie_httponly' => false,
        'name' => 'PHPSESSID',
        'cookie_lifetime' => 0,
        'gc_maxlifetime' => 1440
    );

    /**
     * Sets up the adapter with the configuration assigned by the `Session` class.
     *
     * @param array $config Available configuration options for this adapter:
     *              - `'config'` _string_: The name of the model that this adapter should use.
     */
    public function __construct(array $config = array()) {
        // echo "model inside test case";exit;
        $defaults = array('model' => null, 'expiry' => '+2 hours');
        //parent::__construct($config + $defaults);
       //$this->_autoConfig($config + $defaults, ['model', 'expiry']);//check with the expiry again
       $this->_config = $config + $defaults;
       $this->_autoInit($config);
    }

    /**
     * Initialization of the session.
     * Sets the session save handlers to this adapters' corresponding methods.
     *
     * @return void
     */
    protected function _init() {
        //parent::_init();
        $this->_autoConfig($this->_config, $this->_autoConfig);
        if (!$this->_config['model']) {
            $message = "A valid model is required to use the Model session adapter.";
            throw new ConfigException($message);
        }
        
        foreach ($this->_defaults as $key => $config) {
            if (isset($this->_config[$key])) {
                // if ($key == 'cookie_secure' && !CommonHelper::isHTTPS()) {
                if ($key == 'cookie_secure') {
                    $this->_config[$key] = false;
                }
                if (ini_set("session.{$key}", $this->_config[$key]) === false) {
                    throw new ConfigException("Could not initialize the session.");
                }
            }
        }
        $this->_model = Libraries::locate('models', $this->_config['model']);
        session_set_save_handler(
            array(&$this, '_open'), array(&$this, '_close'), array(&$this, '_read'),
            array(&$this, '_write'), array(&$this, '_destroy'), array(&$this, '_gc')
        );
        register_shutdown_function('session_write_close');
        $this->_startup();
    }


    /**
     * Starts the session.
     *
     * @return boolean True if session successfully started (or has already been started),
     *         false otherwise.
     */
    protected static function _startup() {
        if (session_id() !== '') {
            return true;
        }
        if (!isset($_SESSION)) {
            session_cache_limiter("nocache");
        }
        
        return @session_start();
    }

    /**
     * Obtain the status of the session.
     *
     * @return boolean Returns `true` if `$_SESSION` is accessible, `false` otherwise.
     */
    public function isStarted() {
        return isset($_SESSION);
    }

    /**
     * Uses PHP's default session handling to generate a unique session ID.
     * This will be used as the primary key for all session-related operations.
     *
     * @return string Returns the session ID for the current request, or `null` if the session is
     *         invalid or if a key could not be generated.
     */
    public function key() {
        return session_id() ?: null;
    }

    /**
     * Called when opening the session - the equivalent of a 'session constructor'.
     * Creates & memoizes a Model record/document, on which future session operations will interact
     * to reduce the number of roundtrip operations on the persistent storage engine.
     *
     * @param string $path Not used for this adapter.
     * @param string $name Not used for this adapter.
     * @return void
     */
    public function _open($path, $name) {
        $model = $this->_model;
        $id = $this->key();

        if (!$id || !($this->_data = $model::first($id))) {
            $data = $id ? $model::key($id) : array();
            $this->_data = $model::create($data);
           
            $this->_data->expiry = strtotime($this->_config['expiry']);
        }
        return true;
    }

    /**
     * Session save handler callback for session destruction - called when session_destroy()
     * is invoked.
     *
     * @param string $id The session ID to be destroyed. This is not used explicitly - rather,
     *        the memoized DB record object's delete() method is called.
     * @return boolean True on successful destruction, false otherwise.
     */
    public function _destroy($id) {
        return $this->_data->delete();
    }

    /**
     * Closes the session.
     *
     * @return boolean Always returns true.
     */
    public function _close() {
        $this->_data = null;
        return true;
    }

    /**
     * Delete all expired entries from the session.
     *
     * @param integer $lifetime Maximum valid session lifetime.
     * @return boolean True on successful garbage collect, false otherwise.
     */
    public function _gc($lifetime) {
        $model = $this->_model;
        $model::remove(array('expiry' => array('<=' => time() - $lifetime)));
    }

    /**
     * Delete a value from the session.
     *
     * @param string $key The key of the data to be deleted.
     * @param array $options Not implemented for this adapter method.
     * @return boolean
     */
    public function delete($key, array $options = array()) {
        $_data = $this->_data;

        return function($params) use (&$_data) {
            //if (isset($params['key']) && property_exists($self, $params['key']) && $_data->{$params['key']} !== null) {
            if ($_data->{$params['key']} !== null) {
                $_data->{$params['key']} = null;
                return true;
            }
            return false;
        };
    }

    /**
     * Read a value from the session.
     *
     * @param string $key The key of the data to be returned. If no key is specified,
     *        then all session data is returned in an array of key/value pairs.
     * @param array $options Not implemented for this adapter method.
     * @return mixed
     */
    public function read($key = null, array $options = array()) {
        $_this =& $this;
        return function ($params) use (&$_this) {
            return $_this->_read($params['key']);
        };
    }

    /**
     * The session save handler callback for reading data from the session.
     *
     * @param string $key The key of the data to be returned. If no key is specified,
     *        then all session data is returned in an array of key/value pairs.
     * @return mixed Value corresponding to key if set, null otherwise.
     */
    public function _read($key = null) {
        if (!$this->_data || !is_object($this->_data)) {
            return null;
        }
        if ($key === null) {
            return $this->_data->data();
        }
        $data = $this->_data->{$key};
        //return ($data instanceof Entity) ? $data->data() : (string)$data;
         if($data === null || $data == ''){
            return ($data instanceof Entity) ? $data->data() : (string)$data;
        }else{
            return ($data instanceof Entity) ? $data->data() : $data;
        }
    }

    /**
     * Write a value to the session.
     *
     * @param string $key The key of the data to be returned.
     * @param mixed $value The value to be written to the session.
     * @param array $options Not implemented for this adapter method.
     * @return boolean True if write was successful, false otherwise.
     */
    public function write($key, $value = null, array $options = array()) {

        $_data =& $this->_data;
        return function($params) use (&$_data) {
            $_data->set(array($params['key'] => $params['value']));
            return true;
        };
    }

    /**
     * The session save handler callback for writing data to the session.
     *
     * @param string $key The key of the data to be returned.
     * @param mixed $value The value to be written to the session.
     * @return boolean True if write was successful, false otherwise.
     */
    public function _write($key, $value) {

        if (!$this->_data || !is_object($this->_data)) {
            return false;
        }
        $model = $this->_data->model();
        $key = $model::key($key);
        $expiry = strtotime($this->_config['expiry']);
        return $this->_data->save($key + compact('expiry'));
    }

    /**
     * Checks if a value has been set in the session.
     *
     * @param string $key Key of the entry to be checked.
     * @return boolean True if the key exists, false otherwise.
     */
    public function check($key) {
        $_data = $this->_data;
        return function($params) use (&$_data) { return isset($_data->{$params['key']}); };
    }

    public static function enabled() {
        return session_id() ?: null;
    }

    /**
     * Clears all keys from the session.
     *
     * @param array $options Options array. Not used fro this adapter method.
     * @return boolean True on successful clear, false otherwise.
     */
    public function clear(array $options = array()) {
        $_data = $this->_data;

        return function($params) use (&$_data) {
            return $_data->delete();
        };
    }
}

?>
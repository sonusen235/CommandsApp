<?php
namespace app\extensions\adapter\storage\cache;

require_once LITHIUM_LIBRARY_PATH.'/lithium/storage/cache/adapter/Memcache.php';

class Memcache_old extends \lithium\storage\cache\adapter\Memcache
{
    protected function _init()
    {
        $this->connection = $this->connection ? : new \Memcache();
        $servers = isset($this->_config['servers'])
            ? $this->_config['servers']
            : $this->_formatHostList($this->_config['host']);

        foreach ($servers as $server)
        {
            $this->connection->addServer($server[0], $server[1]);
        }
    }

    public function read(array $key)
    {
        $connection =& $this->connection;

        return function ($params) use (&$connection) {
            $key = $params['key'];

            if (is_array($key))
            {
                return array_map(array($this, __FUNCTION__), $key);
            }
            if (($result = $connection->get($key)) === false)
            {
                $result = null;
            }
            return $result;
        };
    }

    public function write(array $keys, $expiry = null)
    {
        $connection =& $this->connection;
        $expiry = ($expiry) ? : $this->_config['expiry'];

        return function ($params) use (&$connection, $expiry) {
            $expires = is_int($expiry) ? $expiry : strtotime($expiry);
            $key = $params['key'];

            if (is_array($key))
            {
                return array_map(
                    array($this, __FUNCTION__),
                    array_keys($key),
                    array_values($key),
                    array_fill(0, count($key), $expires)

                );
            }
            return $connection->set($key, $params['data'], false, $expires);
        };
    }

    public static function enabled()
    {
        return extension_loaded('memcache');
    }
}

?>
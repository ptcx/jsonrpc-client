<?php
/**
 * Json-rpc Client
 */

namespace JRClient;

use JRClient\Connection\Connection;
use JRClient\Connection\TcpConnection;
use JRClient\Exception\ConnectionException;

class Client
{
    /**
     * supported network
     */
    const VALID_NETWORK = ['tcp'];

    /**
     * @var int request id
     */
    private static $SEQ = 1;

    /**
     * @var string
     */
    private $network;
    /**
     * @var string
     */
    private $address;
    /**
     * @var Connection
     */
    private $conn;

    /**
     * Client constructor.
     * @param $network string 'tcp'
     * @param $address string 'host:port'
     * @throws ConnectionException
     */
    public function __construct($network, $address)
    {
        if (!in_array($network, self::VALID_NETWORK)) {
            throw new ConnectionException("network {$network} not support");
        }

        $this->network = $network;
        $this->address = $address;

        if ($this->network == 'tcp') {
            $this->conn = new TcpConnection($this->address);
        }
    }

    /**
     * @param $method
     * @param $params
     * @param int $timeout
     * @return array
     */
    public function call($method, $params, $timeout=3000)
    {
        $request = new Request($method, $params, self::$SEQ++);
        $result = $this->conn->send($request, $timeout);
        return $result;
    }
}
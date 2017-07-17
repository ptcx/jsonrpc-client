<?php
/**
 * Json-rpc Request
 */

namespace JRClient;


class Request
{
    /**
     * @var string
     */
    public $method;

    /**
     * @var array
     */
    public $params;

    /**
     * @var int
     */
    public $id;

    /**
     * Request constructor.
     * @param $method string
     * @param $params array
     * @param $id int
     */
    public function __construct($method, $params, $id)
    {
        $this->method = $method;
        $this->params = $params;
        $this->id = $id;
    }

    /**
     * format to json
     * @return string
     */
    public function toJson()
    {
        return json_encode([
            'method' => $this->method,
            'params' => [ $this->params ],
            'id' => $this->id
        ]);
    }


}
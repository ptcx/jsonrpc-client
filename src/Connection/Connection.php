<?php

namespace JRClient\Connection;


use JRClient\Request;

interface Connection
{
    /**
     * send request to json-rpc server
     * @param $request Request
     * @param $timeout int
     * @return array
     */
    public function send(Request $request, $timeout);
}
<?php
/**
 * tcp connection to json-rpc server
 */

namespace JRClient\Connection;

use JRClient\Request;
use JRClient\Exception\ConnectionException;

class TcpConnection implements Connection
{
    const DEFAULT_PORT = 80;
    const READ_BUF = 4096;

    /**
     * @var string
     */
    private $ip;

    /**
     * @var string
     */
    private $port;

    /**
     * @var resource
     */
    private $sockfp;

    /**
     * TcpConnection constructor.
     * @param $address string
     */
    public function __construct($address)
    {
        $addr = explode(':', $address, 2);
        if (count($addr) == 1) {
            $this->ip = $addr[0];
            $this->port = self::DEFAULT_PORT;
        } else {
            $this->ip = $addr[0];
            $this->port = $addr[1];
        };
    }

    /**
     * @param $request Request
     * @param $timeout int
     * @return array
     * @throws ConnectionException
     */
    public function send(Request $request, $timeout)
    {
        $this->open();
        $res = [
            'error' => false,
            'errorMsg' => null,
            'data' => null,
        ];
        $content = $request->toJson() . "\n";
        $written = $this->fwriteAll($content);
        if ($written != strlen($content)) {
            $res['error'] = true;
            $res['errorMsg'] = 'write request error';
            return $res;
        }
        stream_set_blocking($this->sockfp, true);
        stream_set_timeout($this->sockfp, 0, $timeout * 1000);

        $dataStr = $this->fgetsAll();

        $info = stream_get_meta_data($this->sockfp);
        if ($info['timed_out'] && empty($dataStr)) {
            $res['error'] = true;
            $res['errorMsg'] = 'time out error';
            return $res;
        }

        $this->close();


        $data = json_decode($dataStr, true);
        if (!isset($data)) {
            $res['error'] = true;
            $res['errorMsg'] = 'respond data error: not json';
            return $res;
        }
        $res['data'] = $data;
        return $res;
    }

    private function fwriteAll($content)
    {
        for ($written = 0; $written < strlen($content); $written += $fwrite) {
            $fwrite = fwrite($this->sockfp, substr($content, $written));
            if ($fwrite === false) {
                return $written;
            }
        }
        return $written;
    }

    private function fgetsAll()
    {
        $data = '';
        while (($buffer = fgets($this->sockfp, self::READ_BUF)) !== false) {
            $data .= $buffer;
            if (substr($buffer, -1) == "\n") {
                break;
            }
        }
        return $data;
    }

    /**
     * open a tcp connection
     * @throws ConnectionException
     */
    private function open()
    {
        $sockfp = @fsockopen('tcp://' . $this->ip, $this->port, $errno, $errstr);
        if (!$sockfp) {
            throw new ConnectionException("open connection failed: {$errstr} ({$errno})");
        } else {
            $this->sockfp = $sockfp;
        }
    }

    /**
     * close current tcp connection
     */
    private function close()
    {
        if (!isset($this->sockfp)) {
            return;
        }
        fclose($this->sockfp);
        $this->sockfp = null;
    }

}
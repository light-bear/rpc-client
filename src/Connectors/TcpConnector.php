<?php
/**
 * Created by PhpStorm.
 * User: Madman
 * Date: 2020/4/17
 * Time: 11:17
 */

namespace LightBear\RpcClient\Connectors;

use LightBear\RpcClient\Exceptions\RpcClientException;

class TcpConnector
{
    protected $host;
    protected $port;
    protected $timeout = 3;
    protected $fp;

    public function __construct($host, $port, $timeout = 3)
    {
        $this->host = $host;
        $this->port = $port;
        $this->timeout = $timeout;
    }

    public function connect()
    {
        if ($this->isConnected()) {
            return $this;
        }

        $dsn = $this->getDsn();
        $this->fp = stream_socket_client($dsn, $errCode, $errMsg, $this->timeout);

        if (!$this->fp) {
            throw new RpcClientException("stream_socket_client fail errCode={$errCode} errMsg={$errMsg}");
        }

        return $this;
    }

    public function send($data)
    {
        return fwrite($this->fp, $data);
    }

    public function receive($eof)
    {
        $result = '';

        while (!feof($this->fp)) {
            $tmp = stream_socket_recvfrom($this->fp, 1024);

            if ($pos = strpos($tmp, $eof)) {
                $result .= substr($tmp, 0, $pos);
                break;
            } else {
                $result .= $tmp;
            }
        }

        return $result;
    }

    public function isConnected()
    {
        return !!$this->fp;
    }

    public function close()
    {
        return $this->isConnected() && fclose($this->fp);
    }

    protected function getDsn()
    {
        return sprintf("tcp://%s:%s", $this->host, $this->port);
    }

    public function __destruct()
    {
        $this->close();
    }
}
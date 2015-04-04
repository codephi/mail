<?php

/**
 * Created by PhpStorm.
 * User: philippe
 * Date: 02/04/15
 * Time: 23:59
 */
class Mail
{
    public $error = [];
    public $success = [];
    public $config;
    public $required = [];
    public $body;
    public $title;
    public $from;
    public $to;
    public $cc;
    public $bcc;
    public $reply;
    public $returnpath;
    public $lang = 'pt-br';


    public function __construct($json = false)
    {
        if ($json)
            $this->jsonHeader();
    }

    public function jsonHeader()
    {
        header('Content-Type: application/json');
    }

    public function base64($path)
    {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }

    public function getClientIp()
    {
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if (getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if (getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if (getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if (getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if (getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    public function body($file, $data = [])
    {
        if (!file_exists($file))
            die('Render impossible, the file does not exist.');

        if (count($data))
            extract($data);

        ob_start();
        include $file;
        $this->body = ob_get_clean();
    }

    public function header()
    {
        $headers = "From: {$this->from} \r\n";

        if ($this->to) {
            if (is_array($this->to))
                $this->to = implode(',', $this->to);

            $headers .= "To: {$this->to}\r\n";
        }


        if ($this->reply) {
            if (is_array($this->reply))
                $this->reply = implode(',', $this->reply);

            $headers .= "Reply-To: {$this->reply}\r\n";
        }

        if ($this->cc) {
            if (is_array($this->cc))
                $this->cc = implode(',', $this->cc);

            $headers .= "Cc: {$this->cc}\r\n";
        }

        if ($this->bcc) {
            if (is_array($this->bcc))
                $this->bcc = implode(',', $this->bcc);

            $headers .= "Bcc: {$this->bcc}\r\n";
        }

        if ($this->returnpath)
            $headers .= "Return-Path: <{$this->returnpath}>\r\n";

        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= 'X-Mailer: PHP/' . phpversion() . "\r\n";
        $headers .= "X-Accept-Language: {$this->lang}\r\n";

        return $headers;
    }


    public function required($name, $error = false, $call = false, $callError = false)
    {
        $this->data[] = ['name' => $name, 'error' => $error, 'call' => $call, 'call_error' => $callError];
    }

    public function filter($call)
    {
        foreach ($this->post as $key => $value)
            $call($key, $value);
    }

    public function handling()
    {
        foreach ($this->data as $key => $value) {

            if (isset($value['error']))
                if (!isset($this->post[$value['name']]))
                    $this->addError($value['error']);

            if ($value['call'])
                if (method_exists('Mail', 'validate_' . $value['call'])) {
                    $call = 'validate_' . $value['call'];
                    if ($value['call_error'])
                        $this->$call($value['name'], $this->post[$value['name']], $value['call_error']);
                    else
                        $this->$call($value['name'], $this->post[$value['name']]);
                } elseif (function_exists($value['call']))
                    $value['call']($this->post[$value['name']]);
        }

        if (count($this->error))
            return false;
        else
            return true;
    }

    public function validate_mail($key, $mail, $error = false)
    {
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            if ($error)
                $this->addError($error);
            return false;
        } else
            return true;
    }

    public function error()
    {
        $error = [
            'error' => true,
            'success' => false
        ];

        $error['msg'] = $this->error;

        return $error;
    }

    public function success()
    {
        $success = [
            'success' => true,
            'error' => false
        ];

        $success['msg'] = $this->success;

        return $success;
    }

    public function addError($error)
    {
        $this->error[] = $error;
    }

    public function addSuccess($success)
    {
        $this->success[] = $success;
    }

    public function answer($answer, $msg = false)
    {
        $msg = (!$answer) ? $this->addError($msg) : $this->addSuccess($msg);

        if ($msg)
            if ($answer)
                $this->addSuccess($msg);
            else
                $this->addError($msg);

        return $answer;
    }

    public function post($post, $value = null)
    {
        if (is_array($post))
            $this->post = $post;
        elseif (!is_null($value))
            $this->post[$post] = $value;
        else
            return $this->post[$post];
    }

    public function send()
    {
        if (!$this->handling())
            return false;

        if (!mail($this->to, $this->subject, $this->body, $this->header()))
            if (!mail($this->to, $this->subject, $this->body, $this->header(), "-r" . $this->to))
                return $this->answer(false, 'Email not sent');

        return $this->answer(true, 'Email sent successfully');
    }
}
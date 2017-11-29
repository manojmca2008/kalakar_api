<?php

namespace MCommons;

use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions as SmtpOptions;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use MCommons\StaticFunctions;

class Message extends \Zend\Mail\Message {

    protected $_smtpOptions = array();
    protected $_attachment = false;

    public function addAttachment(MimePart $attachment) {
        $this->_attachment = $attachment;
    }

    /**
     * Send mail from the message API
     *
     * @return \StaticFunctions\Message
     */
    public function Sendmail() {
        // first of all get the body of the message as plain text and convert it to HTML
        $text = new MimePart('');
        $text->type = "text/plain";

        $html = new MimePart($this->getBody());
        $html->type = "text/html";

        if (!$this->getSubject()) {
            $this->setSubject("Email:");
        }

        $body = new MimeMessage();
        $parts = array($html);
        if ($this->_attachment) {
            $parts[] = $this->_attachment;
        }
        $body->setParts($parts);

        $this->setBody($body);

        $this->setEncoding("UTF-8");

        $transport = new SmtpTransport();
        $transport->setOptions($this->getSmtpOptions());
        $transport->send($this);
        $this->_attachment = false;
        return $this;
    }

    protected function getSmtpOptions() {
        $config = StaticFunctions::getServiceLocator()->get('Config');
       // print_r($config);die;
        return new SmtpOptions(array(
            'name' => 'kalakar.com',
            'host' => $config['constants']['smtp']['host'],
            'connection_class' => 'plain',
            'connection_config' => array(
                'username' => $config['constants']['smtp']['username'],
                'password' => $config['constants']['smtp']['password'],
                'ssl' => $config['constants']['smtp']['ssl']
            )
        ));
    }
}

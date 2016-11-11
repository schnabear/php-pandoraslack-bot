<?php

namespace PhpPandoraSlackBot;

class PandoraCommand extends \PhpSlackBot\Command\BaseCommand
{
    const API_URL = 'http://www.pandorabots.com/pandora/talk-xml';

    protected $botid = null;

    public function __construct($botid)
    {
        $this->botid = $botid;
    }

    protected function configure()
    {
    }

    protected function execute($data, $context)
    {
        if (!isset($data['type']) || !isset($data['user']) || !isset($data['text'])) {
            return;
        }

        if ($data['type'] == 'message') {
            if ($data['user'] == $context['self']['id']) {
                return;
            }

            $mention_self = '<@' . $context['self']['id'] . '>';
            $mention_self_text_position = strpos($data['text'], $mention_self);
            $channel = $this->getChannelNameFromChannelId($data['channel']);

            if ($mention_self_text_position === false && $channel) {
                return;
            }

            $text = str_replace($mention_self, '', $data['text']);
            $text = preg_replace('/(^|\s)[^A-Za-z0-9]*($|\s)/', ' ', $text);
            $text = trim($text);

            if (strtolower($text) == 'ping') {
                $message = str_replace(array('i', 'I'), array('o', 'O'), $text);
            } else {
                $options = array(
                    'botid' => $this->botid,
                    'custid' => crc32($data['channel']),
                    'input' => $text,
                );
                $message = $this->request(self::API_URL, $options);

                if (!$message) {
                    $message = '...';
                }
            }

            $this->send($data['channel'], $data['user'], $message);
        }
    }

    protected function request($url, $data)
    {
        $data = http_build_query($data);
        $options = array(
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
        );
        $curl = curl_init($url);
        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            throw new \ErrorException(curl_error($curl));
        }
        curl_close($curl);

        $xml = new \SimpleXMLElement($response);
        $result = $xml->xpath('//result/that/text()');

        if (!isset($result[0][0])) {
            return '';
        }

        return $result[0][0];
    }
}

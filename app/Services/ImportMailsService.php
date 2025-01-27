<?php

namespace Mss\Services;

use Mss\Models\Order;
use Webklex\IMAP\Client;
use Webklex\IMAP\Folder;
use Webpatser\Uuid\Uuid;
use Webklex\IMAP\Message;
use Mss\Models\OrderMessage;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class ImportMailsService {

    /**
     * @var \Webklex\IMAP\Client
     */
    protected $client;

    /**
     * ImportMailsService constructor.
     *
     * @param Client $client
     */
    public function __construct(Client $client) {
        $this->client = $client;
    }

    public function process() {
        $this->client->connect();

        /** @var Folder $oFolder */
        $oFolder = $this->client->getFolder('INBOX');

        //Get all Messages
        /** @var Message $message */
        foreach($oFolder->getMessages('UNSEEN') as $message) {
            $order = $this->getOrderFromMessage($message);

            if ($order) {
                $order->messages()->create($this->getOrderMessageData($message));
            } else {
                OrderMessage::create($this->getOrderMessageData($message));
            }

            if (settings('imap.delete', false)) {
                $message->delete();
            } else {
                $message->setFlag('SEEN');
            }

        }
    }

    protected function getOrderMessageData(Message $message) {
        return [
            'sender' => collect($message->from)->pluck('mail'),
            'receiver' => ['System'],
            'received' => $message->date,
            'subject' => $message->subject,
            'htmlBody' => $message->getHTMLBody(),
            'textBody' => $message->getTextBody(),
            'attachments' => $message->getAttachments()->map(function ($attachment) {
                $fileName = Uuid::generate(4)->string;
                file_put_contents(storage_path('attachments').DIRECTORY_SEPARATOR.$fileName, $attachment->content);
                if (!file_exists(storage_path('attachments').DIRECTORY_SEPARATOR.$fileName)) {
                    throw new FileNotFoundException('Attachment not saved');
                }
                return [
                    'fileName' => $fileName,
                    'contentType' => $attachment->content_type,
                    'orgFileName' => $attachment->name
                ];
            })
        ];
    }

    /**
     * @param Message $message
     * @return Order|null
     */
    protected function getOrderFromMessage(Message $message) {
        $subject = $message->subject;
        $htmlContent = $message->getHTMLBody(true);
        $textContent = $message->getTextBody();

        foreach(compact('subject', 'htmlContent', 'textContent') as $content) {
            if (preg_match('/\D?([0-9]{7,8})\D?/', $content, $matches)) {
                $order = Order::where('internal_order_number', $matches[1])->first();
                if ($order) {
                    return $order;
                }
            }
        }

        return null;
    }

}
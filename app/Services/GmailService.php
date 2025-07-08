<?php

namespace App\Services;

use App\Models\User;
use Google\Service\Gmail;
use Google_Client;
use Google_Service_Gmail;

class GmailService
{

  protected $client;
  protected $gmail;
  protected $user;
  protected $openAiService;

  public function __construct(User $user)
  {
    $googleAuthToken = $user->google_auth_token;
    $googleRefreshToken = $user->google_refresh_token;
    $service = new GoogleService($user, $googleAuthToken, $googleRefreshToken);
    $this->client = $service->getClient();
    $this->gmail = new Gmail($this->client);
    $this->user = $user;
    $this->openAiService = new OpenAiService();
  }

  public function syncEmails(string $userId = 'me')
  {
    try {
      $messages = $this->getMessages('in:inbox', 100);
      foreach ($messages as $message) {
        $this->processEmail($userId, $message);
      }
    } catch (\Exception $e) {
      throw $e;
    }
  }

  public function processEmail(string $userId, $message)
  {
    $existingMessage = $this->user->emails()->where('gmail_id', $message['id'])->first();
    if ($existingMessage) {
      return;
    }
    $emailContent = "Subject: " . $message['subject']
      . '\nFrom: ' . $message['from']
      . '\nBody: ' . $message['body'];
    $embedding = $this->openAiService->generateEmbedding($emailContent);
    $this->user->emails()->create([
      'gmail_id' => $message['id'],
      'subject' => $message['subject'],
      'from_email' => $message['from'],
      'from_name' => $message['from'],
      'to_email' => $message['to'],
      'body' => $message['body'],
      'embedding' => json_encode($embedding),
      'received_at' => date('Y-m-d H:i:s', (int)($message['internalDate'] / 1000)),
    ]);
  }

  public function getMessages($query = '', $maxResults = 100)
  {
    try {
      $opt = [
        'maxResults' => $maxResults,
        'q' => $query,
      ];
      $userId = 'me';
      $messages = $this->gmail->users_messages->listUsersMessages($userId, $opt);
      foreach ($messages->getMessages() as $message) {
        $messageData = $this->gmail->users_messages->get($userId, $message->getId());
        $messageList[] = $this->formatMessage($messageData);
      }

      return $messageList;
    } catch (\Exception $e) {
      throw $e;
    }
  }

  protected function formatMessage($message)
  {
    $headers = $message->getPayload()->getHeaders();
    $headerArray = [];

    foreach ($headers as $header) {
      $headerArray[$header->getName()] = $header->getValue();
    }

    return [
        'id' => $message->getId(),
        'threadId' => $message->getThreadId(),
        'labelIds' => $message->getLabelIds(),
        'snippet' => $message->getSnippet(),
        'historyId' => $message->getHistoryId(),
        'internalDate' => $message->getInternalDate(),
        'subject' => $headerArray['Subject'] ?? '',
        'from' => $headerArray['From'] ?? '',
        'to' => $headerArray['To'] ?? '',
        'date' => $headerArray['Date'] ?? '',
        'body' => $this->getMessageBody($message),
        'headers' => $headerArray,
        'isUnread' => in_array('UNREAD', $message->getLabelIds() ?? [])
    ];
  }

  protected function getMessageBody($message)
  {
    $body = '';
    $payload = $message->getPayload();

    if ($payload->getParts()) {
      foreach ($payload->getParts() as $part) {
        if ($part->getMimeType() == 'text/plain' || $part->getMimeType() == 'text/html') {
          $data = $part->getBody()->getData();
          $body .= base64_decode($data);
        }
      }
    } else {
      $body = base64_decode($payload->getBody()->getData());
    }
    return $body;
  }

  public function sendEmail($to, $subject, $body)
  {
    //
  }

  public function createCalendarEvent($data)
  {

  }

  public function getAvailableTimeSlots($starDate, $endDate)
  {
    //
  }
}
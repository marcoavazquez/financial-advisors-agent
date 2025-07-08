<?php

namespace App\Services;

use OpenAI;

class OpenAiService
{

  protected $client;

  public function __construct()
  {
    $this->client = OpenAI::client(config('openai.api_key'));
  }

  public function generateEmbedding(string $input): array
  {
    try {
      $response = $this->client->embeddings()->create([
        'model' => 'text-embedding-ada-002',
        'input' => $input,
      ]);
  
      return $response->toArray();
    } catch (\Exception $e) {
      throw new \Exception('Failed to generate embedding: ' . $e->getMessage());
    }
  }

  public function chatCompletion(array $messages, ?array $tools)
  {
    try {
      $params = [
        'model' => 'gpt-3.5-turbo-0613',
        'messages' => $messages,
        'temperature' => 0.7,
      ];

      if ($tools) {
        $params['tools'] = $tools;
        $params['tool_choice'] = 'auto';
      }

      return $this->client->completions()->create($params);
    } catch (\Exception $e) {
      throw new \Exception('Error in chat completion: ' . $e->getMessage());
    }
  }
}
<?php

namespace App\Services;

use App\Models\ChatMessage;
use App\Models\User;
use Exception;

class AiAgentService
{
  protected User $user;
  protected OpenAiService $openAiService;
  protected GmailService $gmail;
  protected HubspotService $hubspot;

  public function __construct(User $user)
  {
    $this->user = $user;
    $this->openAiService = new OpenAiService();
    $this->gmail = new GmailService($user);
    $this->hubspot = new HubspotService($user);
  }

  public function processMessage(ChatMessage $message): string
  {
    $context = $this->getContext();
    $history = $this->getHistory($message);
    $systemMessage = $this->buildSystemMessage($context);

    $messages = array_merge([
      new ChatMessage([
        'is_assistant' => true,
        'content' => $systemMessage,
      ])
    ], $history->toArray(), [new ChatMessage([
      'is_assistant' => false,
      'content' => $message->content,
    ])]);

    $response = $this->openAiService->chatCompletion($messages, $this->getTools());

    return '';
  }

  protected function getContext()
  {
    return [
      'emails' => $this->user->emails,
      'contacts' => $this->user->hubspotContacts,
      'events' => $this->user->calendarEvents,
      'instructions' => $this->user->ongoingInstructions,
    ];
  }

  protected function getHistory(ChatMessage $chatMessage)
  {
    return $this->user->chatMessages()->where('thread_id', $chatMessage->thread_id)->get();
  }

  protected function buildSystemMessage($context)
  {
    $emailsCount = $context['emails']->count();
    $contactsCount = $context['contacts']->count();
    $eventsCount = $context['events']->count();

    $instructions = $context['instructions']->map(function ($instruction) {
      return $instruction->instruction;
    })->join(', ');
    $emails = $context['emails']->map(function ($email) {
      return "From" . $email->from . "\n" .
        "Subject: " . $email->subject;
    })->join(', ');
    $contacts = $context['contacts']->map(function ($contact) {
      return "Name: " . $contact->first_name . " " . $contact->last_name . "\n" .
        "Email: " . $contact->email;
    })->join(', ');

    return <<<EOT
      You are an AI assistant for a financial advisor. You have access to the following information and tools:

      CONTEXT:
      - Recent emails: $emailsCount emails available
      - HubSpot contacts: $contactsCount contacts available  
      - Upcoming calendar events: $eventsCount events
      - Ongoing instructions: $instructions

      CAPABILITIES:
      - Search through emails and contacts using semantic search
      - Send emails via Gmail
      - Create and manage calendar events
      - Create and update HubSpot contacts
      - Schedule appointments by coordinating between email and calendar
      - Remember ongoing instructions and apply them proactively

      INSTRUCTIONS:
      - Always be helpful and professional
      - When scheduling appointments, check calendar availability first
      - When creating contacts, always add relevant notes
      - Follow ongoing instructions when applicable
      - Be proactive in suggesting actions based on context
      - Use tools to accomplish tasks rather than just providing information

      Available emails preview: $emails

      Available contacts preview: $contacts;
    EOT;
  }

  protected function getTools()
  {
    return [
      [
        'type' => 'function',
        'function' => [
          'name' => 'search_emails',
          'description' => 'Search through emails using semantic search',
          'parameters' => [
            'type' => 'object',
            'properties' => [
              'query' => [
                'type' => 'string',
                'description' => 'Search query for emails',
              ],
              'limit' => [
                'type' => 'number',
                'description' => 'Maximum number of results to return',
                'default' => 10,
              ],
            ],
            'required' => ['query'],
          ],
        ],
      ],
      [
        'type' => 'function',
        'function' => [
          'name' => 'search_contacts',
          'description' => 'Search through HubSpot contacts using semantic search',
          'parameters' => [
            'type' => 'object',
            'properties' => [
              'query' => [
                'type' => 'string',
                'description' => 'Search query for contacts',
              ],
              'limit' => [
                'type' => 'number',
                'description' => 'Maximum number of results to return',
                'default' => 10,
              ],
            ],
            'required' => ['query'],
          ],
        ],
      ],
      [
        'type' => 'function',
        'function' => [
          'name' => 'send_email',
          'description' => 'Send an email via Gmail',
          'parameters' => [
            'type' => 'object',
            'properties' => [
              'to' => [
                'type' => 'string',
                'description' => 'Recipient email address',
              ],
              'subject' => [
                'type' => 'string',
                'description' => 'Email subject',
              ],
              'body' => [
                'type' => 'string',
                'description' => 'Email body content',
              ],
            ],
            'required' => ['to', 'subject', 'body'],
          ],
        ],
      ],
      [
        'type' => 'function',
        'function' => [
          'name' => 'get_available_times',
          'description' => 'Get available time slots for scheduling',
          'parameters' => [
            'type' => 'object',
            'properties' => [
              'start_date' => [
                'type' => 'string',
                'description' => 'Start date for availability check (YYYY-MM-DD)',
              ],
              'end_date' => [
                'type' => 'string',
                'description' => 'End date for availability check (YYYY-MM-DD)',
              ],
            ],
            'required' => ['start_date', 'end_date'],
          ],
        ],
      ],
      [
        'type' => 'function',
        'function' => [
          'name' => 'create_calendar_event',
          'description' => 'Create a calendar event',
          'parameters' => [
            'type' => 'object',
            'properties' => [
              'title' => [
                'type' => 'string',
                'description' => 'Event title',
              ],
              'description' => [
                'type' => 'string',
                'description' => 'Event description',
              ],
              'start_time' => [
                'type' => 'string',
                'description' => 'Start time (ISO string)',
              ],
              'end_time' => [
                'type' => 'string',
                'description' => 'End time (ISO string)',
              ],
              'attendees' => [
                'type' => 'array',
                'items' => ['type' => 'string'],
                'description' => 'List of attendee email addresses',
              ],
            ],
            'required' => ['title', 'start_time', 'end_time'],
          ],
        ],
      ],
      [
        'type' => 'function',
        'function' => [
          'name' => 'create_hubspot_contact',
          'description' => 'Create a new contact in HubSpot',
          'parameters' => [
            'type' => 'object',
            'properties' => [
              'email' => [
                'type' => 'string',
                'description' => 'Contact email address',
              ],
              'first_name' => [
                'type' => 'string',
                'description' => 'Contact first name',
              ],
              'last_name' => [
                'type' => 'string',
                'description' => 'Contact last name',
              ],
              'company' => [
                'type' => 'string',
                'description' => 'Contact company',
              ],
              'notes' => [
                'type' => 'string',
                'description' => 'Notes about the contact',
              ],
            ],
            'required' => ['email', 'first_name', 'last_name'],
          ],
        ],
      ],
      [
        'type' => 'function',
        'function' => [
          'name' => 'add_ongoing_instruction',
          'description' => 'Add a new ongoing instruction for proactive behavior',
          'parameters' => [
            'type' => 'object',
            'properties' => [
              'instruction' => [
                'type' => 'string',
                'description' => 'The ongoing instruction to remember and follow',
              ],
            ],
            'required' => ['instruction'],
          ],
        ],
      ],
    ];
  }

  protected function handleToolCalls(array $tools)
  {
    $result = [];
    foreach ($tools as $tool) {
      $name = $tool['function']['name'];
      $args = json_decode($tool['function']['arguments'], true);

      $result = null;
      try {
        switch ($name) {
          case 'search_emails':
            $result = $this->searchEmails($args['query'], $args['limit']);
            break;
          case 'search_contacts':
            $result =  $this->searchContacts($args['query'], $args['limit']);
            break;
          case 'send_email':
            $result =  $this->sendEmail($args['to'], $args['subject'], $args['body']);
            break;
          case 'get_available_times':
            $result =  $this->getAvailableTimes($args['start_date'], $args['end_date']);
            break;
          case 'create_calendar_event':
            $result =  $this->createCalendarEvent($args);
            break;
          case 'create_hubspot_contact':
            $result =  $this->createHubspotContact($args);
            break;
          case 'add_ongoing_instruction':
            $result =  $this->addOngoingInstruction($args['instruction']);
            break;
          default:
            $result =  [ 'error' => "Unknown tool: name}" ];
        }
      } catch (Exception $e) {
        $result =  [ 'error' => $e->getMessage() ];
      }

      $results[] = [
        "tool_call_id" => $tool['id'],
        "result" => $result
      ];
    }

    return $results;
  }

  protected function searchEmails($query, $limit = 10)
  {
    return $this->user->emails()->orderByRaw("embedding <=> $query")->limit($limit)->get();
  }

  protected function searchContacts($query, $limit = 10)
  {
    return $this->user->hubspotContacts()->orderByRaw("embedding <=> $query")->limit($limit)->get();
  }

  protected function sendEmail($to, $subject, $body)
  {
    return $this->gmail->sendEmail($to, $subject, $body);
  }

  protected function getAvailableTimes($startDate, $endDate)
  {
    return $this->gmail->getAvailableTimeSlots($startDate, $endDate);
  }

  protected function createCalendarEvent($params) {
    return $this->gmail->createCalendarEvent(
      $params['title'],
      $params['description'],
      $params['start_time'],
      $params['end_time'],
      $params['attendees']
    );
  }

  protected function createHubspotContact($params) {
    return $this->hubspot->createContact(
      $params['email'],
      $params['first_name'],
      $params['last_name'],
      $params['company'],
      $params['notes']
    );
  }

  protected function addOngoingInstruction($instruction) {
    $this->user->ongoingInstructions()->create([
      'instruction' => $instruction
    ]);
  }

  protected function saveChatMessage($assistant = false, $content) {
    $this->user->chatMessages()->create([
      'is_assistant' => $assistant,
      'content' => $content
    ]);
  }
}

<?php

namespace App\Services;

use App\Models\User;
use HubSpot\Factory;
use HubSpot\Client\Crm\Objects\Notes\Model\BatchReadInputSimplePublicObjectId;
use HubSpot\Client\Crm\Objects\Notes\Model\SimplePublicObjectId;
use Illuminate\Support\Facades\Http;

class HubspotService
{

    protected $hubspot;
    protected $oauthApi;
    protected $user;
    protected $openAiService;

    public function __construct(User $user)
    {
      $accessToken = $user->hubspot_access_token;
      $this->openAiService = new OpenAiService();
      $this->hubspot = Factory::createWithAccessToken($accessToken);
      $this->user = $user;
    }

    static function getAuthUrl():string
    {
      $scopes = [
        'crm.objects.companies.read',
        'crm.objects.contacts.read',
        'crm.objects.contacts.write',
        'crm.objects.deals.read',
        'crm.objects.deals.write',
        'crm.schemas.appointments.read',
        'crm.schemas.appointments.write',
      ];

      return "https://app.hubspot.com/oauth/authorize?" .
        "client_id=" . config('services.hubspot.client_id') .
        "&scope=" . implode('%20', $scopes) .
        "&redirect_uri=" . urlencode(config('services.hubspot.redirect_uri'));
    }

    static function getAccessToken(string $code)
    {
      $tokenResponse = Http::asForm()->post('https://api.hubapi.com/oauth/v1/token', [
        'grant_type' => 'authorization_code',
        'client_id' => config('services.hubspot.client_id'),
        'client_secret' => config('services.hubspot.client_secret'),
        'redirect_uri' => config('services.hubspot.redirect_uri'),
        'code' => $code,
      ]);
      if ($tokenResponse->failed()) {
        throw new \Exception('Failed to retrieve access token: ' . $tokenResponse->body());
      }
      return $tokenResponse->json();
    }

    public function sync()
    {
      $this->getContacts();
    }

    public function getContacts(int $limit = 50, int $offset = 0)
    {
        $response = $this->hubspot->crm()->contacts()->basicApi()->getPage(
          $limit,
          null,
          ['firstname', 'lastname', 'email'],
          ['notes', 'companies']
        );
        $contacts = [];
        foreach ($response->getResults() as $contact) {
            $contacts[] = $this->processContact($contact);
        }
        return $contacts;
    }

    public function processContact($contact)
    {
      $data = $contact->getProperties();
      $hsContact = $this->user->hubspotContacts()->where('hubspot_id', $contact->getId())->first();
      $notes = $this->getContactNotes($contact);
      $embedding = $this->openAiService->generateEmbedding($notes);
      if ($hsContact) {
        $hsContact->update([
          'first_name' => $data['firstname'],
          'last_name' => $data['lastname'],
          'email' => $data['email'],
          'hubspot_id' => $contact->getId(),
          'notes' => $notes,
          'embedding' => json_encode($embedding),
        ]);
      } else {
        $hsContact = $this->user->hubspotContacts()->create([
          'first_name' => $data['firstname'],
          'last_name' => $data['lastname'],
          'email' => $data['email'],
          'hubspot_id' => $contact->getId(),
          'notes' => $notes,
          'embedding' => json_encode($embedding),
        ]);
      }
      return $hsContact;
    }

    public function getContactNotes($contact): string
    {
      $notes = '';
      if ($contact->getAssociations() && isset($contact->getAssociations()['notes'])) {
        $noteIds = array_map(fn ($note) => $note->getId(), $contact->getAssociations()['notes']->getResults());

        if (!empty($noteIds)) {
          $notesApi = $this->hubspot->crm()->objects()->notes();
        
          $batchReadRequest = new BatchReadInputSimplePublicObjectId();
          $batchReadRequest->setInputs(
              array_map(function($id) {
                  $input = new SimplePublicObjectId();
                  $input->setId($id);
                  return $input;
              }, $noteIds)
          );
          
          $batchReadRequest->setProperties(['hs_note_body', 'hs_timestamp', 'hubspot_owner_id']);
          
          $notesResponse = $notesApi->batchApi()->read($batchReadRequest);
          
          foreach ($notesResponse->getResults() as $note) {
              $notes .= "Note Content: " . $note->getProperties()['hs_note_body'] . "\n"
               . "Timestamp: " . $note->getProperties()['hs_timestamp'] . "\n";
          }
        }
      }
      return $notes;
    }

    public function createContact($data)
    {
      $contact = $this->hubspot->crm()->contacts()->basicApi()->create([
        'properties' => [
          'firstname' => $data['first_name'],
          'lastname' => $data['last_name'],
          'email' => $data['email'],
        ],
      ]);
      return $contact;
    }
}
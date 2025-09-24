<?php

require_once('../vendor/autoload.php');

use Bitrix24\SDK\Services\ServiceBuilderFactory;

$webhookUrl = '';
$b24client = ServiceBuilderFactory::createServiceBuilderFromWebhook($webhookUrl);

header('Content-Type: application/json; charset=utf-8');

$contacts = $b24client->getCRMScope()->contact()->list([], [], [
    'ID', 'NAME', 'LAST_NAME', 'SECOND_NAME', 'PHONE', 'EMAIL'
], 0)->getContacts();
$responseData = [
    'contacts' => []
];
foreach ($contacts as $contact) {
    $deals = $b24client->getCRMScope()->deal()->list([], ['CONTACT_ID' => $contact->ID], ['ID'])->getDeals();
    $responseData['contacts'][] = [
        'id' => $contact->ID,
        'fio' => implode(' ', [$contact->NAME, $contact->LAST_NAME, $contact->SECOND_NAME]),
        'phone' => $contact->PHONE,
        'email' => $contact->EMAIL,
        'dealsCount' => count($deals)
    ];
}

echo json_encode($responseData);
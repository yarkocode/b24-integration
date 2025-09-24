<?php

require_once('../vendor/autoload.php');

use Bitrix24\SDK\Services\ServiceBuilderFactory;

function generateClientName()
{
    $firstNames = ['Иван', 'Петр', 'Мария', 'Анна', 'Сергей', 'Ольга', 'Алексей', 'Екатерина'];
    $lastNames = ['Иванов', 'Петров', 'Сидоров', 'Смирнова', 'Кузнецова', 'Попов'];
    $middleNames = ['Иванович', 'Петрович', 'Сергеевна', 'Алексеевна', 'Олегович'];

    return [
        'lastName' => $lastNames[array_rand($lastNames)],
        'firstName' => $firstNames[array_rand($firstNames)],
        'middleName' => $middleNames[array_rand($middleNames)]
    ];
}

function generatePhone()
{
    return '+7' . rand(900, 999) . rand(1000000, 9999999);
}

function generateEmail($name)
{
    $domains = ['gmail.com', 'yandex.ru', 'mail.ru'];
    $translit = str_replace(' ', '.', $name);

    return mb_strtolower($translit) . '@' . $domains[array_rand($domains)];
}

function generateDealTitle()
{
    $dealTitles = ['Продажа сайта', 'Консультация', 'Договор на поддержку', 'Внедрение CRM'];
    return $dealTitles[array_rand($dealTitles)] . rand(0, 999);
}

$webhookUrl = '';
$b24client = ServiceBuilderFactory::createServiceBuilderFromWebhook($webhookUrl);

$contactIds = [];

for ($i = 1; $i <= 5; $i++) {
    $nameParts = generateClientName();
    $contact = $b24client->getCRMScope()->contact()->add([
        'NAME' => $nameParts['firstName'],
        'LAST_NAME' => $nameParts['lastName'],
        'SECOND_NAME' => $nameParts['middleName'],
        'PHONE' => [['VALUE' => generatePhone(), 'VALUE_TYPE' => 'WORK']],
        'EMAIL' => [['VALUE' => generateEmail($nameParts['firstName'] . ' ' . $nameParts['middleName']), 'VALUE_TYPE' => 'WORK']],
    ]);
    $contactIds[] = $contact->getId();
}

for ($i = 0; $i < 15; $i++) {
    $contactId = $contactIds[array_rand($contactIds)];
    $deal = $b24client->getCRMScope()->deal()->add([
        'TITLE' => generateDealTitle(),
        'CONTACT_ID' => (string) $contactId,
        'STAGE_ID' => 'NEW',
        'CURRENCY_ID' => 'RUB',
        'OPPORTUNITY' => (string) rand(4000, 100000)
    ]);
    print_r($deal->getCoreResponse()->getResponseData()->getResult());
}


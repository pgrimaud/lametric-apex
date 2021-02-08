<?php

declare(strict_types=1);

namespace Apex;

use Apex\Exception\{InternalErrorException, MissingParameterException};
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Api
{
    const PLATFORMS = [
        'XBOX1' => '1',
        'PS4'   => '2',
        'PC'    => '5',
    ];

    /**
     * @var string
     */
    private string $apiKey;

    /**
     * @var Validator
     */
    private Validator $validator;

    /**
     * @param Validator $validator
     * @param string $apiKey
     */
    public function __construct(Validator $validator, string $apiKey)
    {
        $this->apiKey    = $apiKey;
        $this->validator = $validator;
    }

    /**
     * @return array
     *
     * @throws InternalErrorException
     * @throws MissingParameterException
     */
    public function fetchData(): array
    {
        $platform = self::PLATFORMS[$this->validator->getParameters()['platform']];

        try {
            $endpoint = 'https://public-api.tracker.gg/apex/v1/standard/profile/' .
                $platform . '/' .
                $this->validator->getParameters()['player'];

            $client = new Client();
            $res    = $client->request('GET', $endpoint, [
                'headers' => [
                    'TRN-Api-Key' => $this->apiKey,
                ],
            ]);

            $json = (string)$res->getBody();
            $data = json_decode($json);
        } catch (\Exception $e) {
            throw new MissingParameterException($e->getMessage());
        } catch (GuzzleException $e) {
            throw new InternalErrorException('Internal error');
        }

        return $this->formatData($data);
    }

    /**
     * @param object $data
     *
     * @return array
     */
    private function formatData(object $data): array
    {
        $dataToReturn = [
            'name' => $data->data->metadata->platformUserHandle,
        ];

        foreach ($data->data->stats as $stat) {
            $name = strtolower($stat->metadata->name);

            switch ($name) {
                case 'level':
                    $dataToReturn[$name] = 'LVL ' . $stat->value;
                    break;
                case 'headshots':
                    $dataToReturn[$name] = $stat->value . ' HS';
                    break;
                case 'kills':
                case 'damage':
                case 'rank score':
                    $dataToReturn[$name] = $stat->value . ' ' . $name;
                    break;
            }
        }

        return $dataToReturn;
    }
}

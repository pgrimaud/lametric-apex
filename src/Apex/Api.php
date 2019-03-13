<?php

namespace Apex;

use Apex\Exception\ConfigException;
use Apex\Exception\InternalErrorException;
use Apex\Exception\MissingParameterException;
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
     * @var array
     */
    private $credentials;

    /**
     * @var Validator
     */
    private $validator;

    /**
     * Api constructor.
     * @param Validator $validator
     * @throws ConfigException
     */
    public function __construct(Validator $validator)
    {
        if (!is_file(__DIR__ . '/../../config/credentials.php')) {
            throw new ConfigException('Internal error: missing config file');
        } else {
            $this->credentials = require __DIR__ . '/../../config/credentials.php';
        }

        $this->validator = $validator;
    }

    /**
     * @return array
     * @throws InternalErrorException
     * @throws MissingParameterException
     */
    public function fetchData()
    {
        $platform = self::PLATFORMS[$this->validator->getParameters()['platform']];

        try {

            $endpoint = 'https://public-api.tracker.gg/apex/v1/standard/profile/' .
                $platform . '/' .
                $this->validator->getParameters()['player'];

            $client = new Client();
            $res    = $client->request('GET', $endpoint, [
                'headers' => [
                    'TRN-Api-Key' => $this->credentials['api-key'],
                ]
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
     * @param $data
     * @return array
     */
    private function formatData($data)
    {
        $dataToReturn = [
            'name' => $data->data->metadata->platformUserHandle,
        ];

        foreach ($data->data->stats as $stat) {
            $name = strtolower($stat->metadata->name);
            if ($name === 'level') {
                $dataToReturn[$name] = 'LVL ' . $stat->value;
            } else {
                $dataToReturn[$name] = $stat->value . ' ' . $name;
            }
        }

        return $dataToReturn;
    }
}

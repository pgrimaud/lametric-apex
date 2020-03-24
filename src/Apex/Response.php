<?php

namespace Apex;

class Response
{
    const ICON = 'i27565';

    /**
     * Response constructor.
     */
    public function __construct()
    {
        header("Content-Type: application/json");
    }

    /**
     * @param null $text
     * @return string
     */
    public function returnError($text = null)
    {
        return $this->asJson([
            'frames' => [
                [
                    'index' => 0,
                    'text'  => $text ?: 'Please check app configuration',
                    'icon'  => self::ICON
                ]
            ]
        ]);
    }

    /**
     * @param Validator $validator
     * @param           $data
     * @return string
     */
    public function returnData(Validator $validator, $data)
    {
        $frames['frames'][] = [
            'index' => 0,
            'text'  => $data['name'],
            'icon'  => self::ICON
        ];

        $frames['frames'][] = [
            'index' => 1,
            'text'  => $data['level'],
            'icon'  => self::ICON
        ];

        $frames['frames'][] = [
            'index' => 2,
            'text'  => $data['kills'],
            'icon'  => self::ICON
        ];

        $i = 2;

        foreach (['headshots', 'damage', 'rank score'] as $stat) {
            if (isset($data[$stat])) {
                $frames['frames'][] = [
                    'index' => ++$i,
                    'text'  => $data[$stat],
                    'icon'  => self::ICON
                ];
            }
        }


        return $this->asJson($frames);
    }

    /**
     * @param array $data
     * @return string
     */
    public function asJson($data = [])
    {
        return json_encode($data, JSON_PRETTY_PRINT);
    }
}

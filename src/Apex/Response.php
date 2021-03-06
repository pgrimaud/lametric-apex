<?php

declare(strict_types=1);

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
     * @param string|null $text
     * @return string
     */
    public function returnError(string|null $text = null): string
    {
        return $this->asJson([
            'frames' => [
                [
                    'index' => 0,
                    'text'  => $text ?: 'Please check app configuration',
                    'icon'  => self::ICON,
                ],
            ],
        ]);
    }

    /**s
     * @param array $data
     *
     * @return string
     */
    public function returnData(array $data): string
    {
        $frames['frames'][] = [
            'index' => 0,
            'text'  => $data['name'],
            'icon'  => self::ICON,
        ];

        $frames['frames'][] = [
            'index' => 1,
            'text'  => $data['level'],
            'icon'  => self::ICON,
        ];

        $i = 1;

        if (isset($data['kills'])) {
            $frames['frames'][] = [
                'index' => 2,
                'text'  => $data['kills'],
                'icon'  => self::ICON,
            ];
            $i++;
        }

        foreach (['headshots', 'damage', 'rank score'] as $stat) {
            if (isset($data[$stat])) {
                $frames['frames'][] = [
                    'index' => ++$i,
                    'text'  => $data[$stat],
                    'icon'  => self::ICON,
                ];
            }
        }


        return $this->asJson($frames);
    }

    /**
     * @param array $data
     *
     * @return string
     */
    public function asJson(array $data = []): string
    {
        return json_encode($data);
    }
}

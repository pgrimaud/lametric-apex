<?php

declare(strict_types=1);

namespace Apex;

use Apex\Exception\MissingParameterException;

class Validator
{
    /**
     * @var array
     */
    private array $parameters = [];

    /**
     * @var array
     */
    private array $mandatoryParameters = [
        'platform',
        'player',
    ];

    /**
     * Validator constructor.
     * @param $parameters
     */
    public function __construct($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @throws MissingParameterException
     */
    public function check()
    {
        foreach ($this->mandatoryParameters as $parameter) {
            if (empty($this->parameters[$parameter])) {
                throw new MissingParameterException('Missing ' . $parameter . ' name');
            }
        }
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }
}

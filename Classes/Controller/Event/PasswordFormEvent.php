<?php

declare(strict_types=1);

namespace Evoweb\SfRegister\Controller\Event;

/*
 * This file is developed by evoWeb.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Evoweb\SfRegister\Domain\Model\Password;

final class PasswordFormEvent
{
    protected Password $password;

    protected array $settings = [];

    public function __construct(Password $password, array $settings)
    {
        $this->password = $password;
        $this->settings = $settings;
    }

    public function getPassword(): Password
    {
        return $this->password;
    }

    public function getSettings(): array
    {
        return $this->settings;
    }
}

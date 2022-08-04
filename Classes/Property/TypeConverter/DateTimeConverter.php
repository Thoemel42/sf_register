<?php

namespace Evoweb\SfRegister\Property\TypeConverter;

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

use TYPO3\CMS\Extbase\Property\Exception\TypeConverterException;
use TYPO3\CMS\Extbase\Property\PropertyMappingConfigurationInterface;

class DateTimeConverter extends \TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter
{
    /**
     * @var string
     */
    public const CONFIGURATION_USER_DATA = '1';

    /**
     * @var int
     */
    protected $priority = 2;

    /**
     * Actually convert from $source to $targetType, taking into account the fully
     * built $convertedChildProperties and $configuration.
     * The return value can be one of three types:
     * - an arbitrary object, or a simple type (which has been created while mapping)
     *   This is the normal case.
     * - NULL, indicating that this object should *not* be mapped
     *   (i.e. a "File Upload" Converter could return NULL if no file has been
     *   uploaded, and a silent failure should occur.
     * - An instance of \TYPO3\CMS\Extbase\Error\Error
     *   This will be a user-visible error message later on.
     * Furthermore, it should throw an Exception if an unexpected failure
     * (like a security error) occurred or a configuration issue happened.
     *
     * @param mixed $source
     * @param string $targetType
     * @param array $convertedChildProperties
     * @param ?PropertyMappingConfigurationInterface $configuration
     *
     * @return ?object target type, or an error object
     */
    public function convertFrom(
        $source,
        string $targetType,
        array $convertedChildProperties = [],
        ?PropertyMappingConfigurationInterface $configuration = null
    ): ?object {
        try {
            $date = parent::convertFrom($source, $targetType, $convertedChildProperties, $configuration);
        } catch (TypeConverterException $e) {
            $date = null;
        }

        if (!($date instanceof \DateTime)) {
            $date = new \DateTime();
            $date->setTimestamp(strtotime($source));
        }

        $userData = $configuration->getConfigurationValue(
            self::class,
            self::CONFIGURATION_USER_DATA
        );

        if (
            is_array($userData)
            && !empty($userData)
            && !empty($userData['dateOfBirthDay'] ?? '')
            && !empty($userData['dateOfBirthMonth'] ?? '')
            && !empty($userData['dateOfBirthYear'] ?? '')
        ) {
            $date->setTimestamp(strtotime(
                $userData['dateOfBirthYear'] . '-' . $userData['dateOfBirthMonth'] . '-' . $userData['dateOfBirthDay']
            ));
        }

        return $date;
    }
}

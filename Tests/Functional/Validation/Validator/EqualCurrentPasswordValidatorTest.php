<?php

namespace Evoweb\SfRegister\Tests\Functional\Validation\Validator;

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

use Evoweb\SfRegister\Domain\Repository\FrontendUserRepository;
use Evoweb\SfRegister\Tests\Functional\AbstractTestBase;
use Evoweb\SfRegister\Validation\Validator\EqualCurrentPasswordValidator;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;

class EqualCurrentPasswordValidatorTest extends AbstractTestBase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/pages.csv');
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/fe_groups.csv');
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/fe_users.csv');
        $this->setUpFrontendRootPage(
            1,
            [
                'constants' => [
                    'EXT:fluid_styled_content/Configuration/TypoScript/constants.typoscript',
                    'EXT:sf_register/Configuration/TypoScript/minimal/constants.typoscript',
                ],
                'setup' => [
                    'EXT:fluid_styled_content/Configuration/TypoScript/setup.typoscript',
                    'EXT:sf_register/Configuration/TypoScript/minimal/setup.typoscript',
                    __DIR__ . '/../Fixtures/PageWithUserObjectUsingSlWithLLL.typoscript'
                ]
            ]
        );
        $this->writeSiteConfiguration(
            'website-local',
            $this->buildSiteConfiguration(1, 'http://localhost/'),
            [
                $this->buildDefaultLanguageConfiguration('EN', '/'),
            ]
        );

        $this->createEmptyFrontendUser();
        $this->initializeTypoScriptFrontendController();

        $this->typoScriptFrontendController->tmpl->setup['plugin.']['tx_sfregister.']['settings.']['badWordList'] =
            'god, sex, password';
    }

    /**
     * @test
     */
    public function settingsContainsValidTyposcriptSettings()
    {
        self::assertArrayHasKey(
            'badWordList',
            $this->typoScriptFrontendController->tmpl->setup['plugin.']['tx_sfregister.']['settings.']
        );
    }

    /**
     * @test
     */
    public function isUserLoggedInReturnsFalseIfNotLoggedIn()
    {
        /** @var Context $context */
        $context = GeneralUtility::makeInstance(Context::class);

        /** @var FrontendUserRepository|MockObject $repositoryMock */
        $repositoryMock = $this->createMock(FrontendUserRepository::class);

        /** @var ConfigurationManager|MockObject $repositoryMock */
        $configurationMock = $this->createMock(ConfigurationManager::class);

        $subject = new EqualCurrentPasswordValidator($context, $repositoryMock, $configurationMock);

        $method = $this->getPrivateMethod($subject, 'userIsLoggedIn');
        self::assertFalse($method->invoke($subject));
    }

    /**
     * @test
     */
    public function isUserLoggedInReturnsTrueIfLoggedIn()
    {
        $this->createAndLoginFrontEndUser('2', [
            'password' => 'testOld',
            'comments' => ''
        ]);
        $this->initializeTypoScriptFrontendController();

        /** @var Context $context */
        $context = GeneralUtility::makeInstance(Context::class);

        /** @var FrontendUserRepository|MockObject $repositoryMock */
        $repositoryMock = $this->createMock(FrontendUserRepository::class);

        /** @var ConfigurationManager|MockObject $repositoryMock */
        $configurationMock = $this->createMock(ConfigurationManager::class);

        $subject = new EqualCurrentPasswordValidator($context, $repositoryMock, $configurationMock);

        $method = $this->getPrivateMethod($subject, 'userIsLoggedIn');
        self::assertTrue($method->invoke($subject));
    }
}

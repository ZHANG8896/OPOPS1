<?php

declare(strict_types=1);

namespace PhpMyAdmin\Tests\Server;

use PhpMyAdmin\Server\Select;
use PhpMyAdmin\Tests\AbstractTestCase;
use PhpMyAdmin\Util;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

use function __;

#[CoversClass(Select::class)]
class SelectTest extends AbstractTestCase
{
    /**
     * Prepares environment for the test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        //$_REQUEST
        $_REQUEST['log'] = 'index1';
        $_REQUEST['pos'] = 3;

        //$GLOBALS
        $GLOBALS['cfg']['MaxRows'] = 10;
        $GLOBALS['server'] = 1;
        $GLOBALS['cfg']['ServerDefault'] = 'server';
        $GLOBALS['cfg']['RememberSorting'] = true;
        $GLOBALS['cfg']['SQP'] = [];
        $GLOBALS['cfg']['MaxCharactersInDisplayedSQL'] = 1000;
        $GLOBALS['cfg']['ShowSQL'] = true;
        $GLOBALS['cfg']['TableNavigationLinksMode'] = 'icons';
        $GLOBALS['cfg']['LimitChars'] = 100;

        $GLOBALS['table'] = 'table';

        $GLOBALS['cfg']['DefaultTabServer'] = 'welcome';

        $GLOBALS['cfg']['Servers'] = [
            '0' => [
                'host' => 'host0',
                'port' => 'port0',
                'only_db' => 'only_db0',
                'user' => 'user0',
                'auth_type' => 'config',
            ],
            '1' => [
                'host' => 'host1',
                'port' => 'port1',
                'only_db' => 'only_db1',
                'user' => 'user1',
                'auth_type' => 'config',
            ],
        ];
        //$_SESSION
    }

    /**
     * Test for Select::render
     */
    #[DataProvider('renderDataProvider')]
    public function testRender(bool $notOnlyOptions): void
    {
        if ($notOnlyOptions) {
            $GLOBALS['cfg']['DisplayServersList'] = null;
        }

        $html = Select::render($notOnlyOptions);
        $server = $GLOBALS['cfg']['Servers']['0'];

        if ($notOnlyOptions) {
            $this->assertStringContainsString(
                Util::getScriptNameForOption(
                    $GLOBALS['cfg']['DefaultTabServer'],
                    'server',
                ),
                $html,
            );

            $this->assertStringContainsString(
                __('Current server:'),
                $html,
            );
            $this->assertStringContainsString(
                '(' . __('Servers') . ')',
                $html,
            );
        }

        //server items
        $this->assertStringContainsString($server['host'], $html);
        $this->assertStringContainsString($server['port'], $html);
        $this->assertStringContainsString($server['only_db'], $html);
        $this->assertStringContainsString($server['user'], $html);
    }

    /** @return mixed[][] */
    public static function renderDataProvider(): array
    {
        return [
            'only options' => [false],
            'not only options' => [true],
        ];
    }
}

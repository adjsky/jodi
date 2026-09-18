<?php

declare(strict_types=1);

use Carbon\CarbonInterface;

return [
    'calendar' => [
        'sameDay' => '[Сегодня в] LT',
        'nextDay' => '[Завтра в] LT',
        'nextWeek' => static function (CarbonInterface $current, CarbonInterface $other) {
            if ($current->week !== $other->week) {
                switch ($current->dayOfWeek) {
                    case 0:
                        return '[В следующее] dddd [в] LT';
                    case 1:
                    case 2:
                    case 4:
                        return '[В следующий] dddd [в] LT';
                    case 3:
                    case 5:
                    case 6:
                        return '[В следующую] dddd [в] LT';
                }
            }

            if ($current->dayOfWeek === 2) {
                return '[Во] dddd [в] LT';
            }

            return '[В] dddd [в] LT';
        },
        'lastDay' => '[Вчера, в] LT',
        'lastWeek' => static function (CarbonInterface $current, CarbonInterface $other) {
            if ($current->week !== $other->week) {
                switch ($current->dayOfWeek) {
                    case 0:
                        return '[В прошлое] dddd [в] LT';
                    case 1:
                    case 2:
                    case 4:
                        return '[В прошлый] dddd [в] LT';
                    case 3:
                    case 5:
                    case 6:
                        return '[В прошлую] dddd [в] LT';
                }
            }

            if ($current->dayOfWeek === 2) {
                return '[Во] dddd [в] LT';
            }

            return '[В] dddd [в] LT';
        },
        'sameElse' => 'L [в] LT',
    ],
];

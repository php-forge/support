<?php

declare(strict_types=1);

namespace PHPForge\Support\Exception;

use function sprintf;

/**
 * Represents error message templates.
 *
 * This enum defines formatted error messages for various error conditions that may occur during operations such as
 * directory handling.
 *
 * It provides message templates that can be formatted at call sites.
 *
 * Each case represents a specific type of error, with a message template that can be populated with dynamic values
 * using the {@see Message::getMessage()} method.
 *
 * Each message template can be formatted with arguments.
 *
 * Key features.
 * - Can be used by exception call sites that need formatted messages.
 * - Defines message templates as enum cases.
 * - Formats templates with `sprintf()` via {@see Message::getMessage()}.
 * - Supports message formatting with dynamic parameters.
 * - Uses the enum case `value` as the template string.
 *
 * @copyright Copyright (C) 2025 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
enum Message: string
{
    /**
     * Error when a directory cannot be opened for reading.
     *
     * Format: 'Unable to open directory: %s'
     */
    case UNABLE_TO_OPEN_DIRECTORY = 'Unable to open directory: %s';

    /**
     * Returns the formatted message string for the error case.
     *
     * Retrieves and formats the error message string by interpolating the provided arguments.
     *
     * @param int|string ...$argument Dynamic arguments to insert into the message.
     *
     * @return string Error message with interpolated arguments.
     *
     * Usage example:
     * ```php
     * throw new InvalidArgumentException(Message::UNABLE_TO_OPEN_DIRECTORY->getMessage('/path/to/dir'));
     * ```
     */
    public function getMessage(int|string ...$argument): string
    {
        return sprintf($this->value, ...$argument);
    }
}

<?php declare(strict_types=1);

/**
 * This file is part of the phpv8/php-v8 PHP extension.
 *
 * Copyright (c) 2015-2018 Bogdan Padalko <thepinepain@gmail.com>
 *
 * Licensed under the MIT license: http://opensource.org/licenses/MIT
 *
 * For the full copyright and license information, please view the
 * LICENSE file that was distributed with this source or visit
 * http://opensource.org/licenses/MIT
 */


namespace V8;

/**
 * A single JavaScript stack frame.
 */
class StackFrame
{
    /**
     * @var int|null
     */
    private $line_number;
    /**
     * @var int|null
     */
    private $column;
    /**
     * @var int|null
     */
    private $script_id;
    /**
     * @var string
     */
    private $script_name;
    /**
     * @var string
     */
    private $script_name_or_source_url;
    /**
     * @var string
     */
    private $function_name;
    /**
     * @var bool
     */
    private $is_eval;
    /**
     * @var bool
     */
    private $is_constructor;
    /**
     * @var bool
     */
    private $is_wasm;

    /**
     * @param int|null $line_number
     * @param int|null $column
     * @param int|null $script_id
     * @param string   $script_name
     * @param string   $script_name_or_source_url
     * @param string   $function_name
     * @param bool     $is_eval
     * @param bool     $is_constructor
     * @param bool     $is_wasm
     */
    public function __construct(
        ?int $line_number = null,
        ?int $column = null,
        ?int $script_id = null,
        string $script_name = '',
        string $script_name_or_source_url = '',
        string $function_name = '',
        bool $is_eval = false,
        bool $is_constructor = false,
        bool $is_wasm = false
    ) {
    }

    /**
     * Returns the number, 1-based, of the line for the associate function call.
     * This method will return null if it is unable to
     * retrieve the line number, or if kLineNumber was not passed as an option
     * when capturing the StackTrace.
     *
     * @return int
     */
    public function getLineNumber(): ?int
    {
    }

    /**
     * Returns the 1-based column offset on the line for the associated function
     * call.
     * This method will return Message::kNoColumnInfo if it is unable to retrieve
     * the column number, or if kColumnOffset was not passed as an option when
     * capturing the StackTrace.
     *
     * @return int
     */
    public function getColumn(): ?int
    {
    }

    /**
     * Returns the id of the script for the function for this StackFrame.
     * This method will return Message::kNoScriptIdInfo if it is unable to
     * retrieve the script id, or if kScriptId was not passed as an option when
     * capturing the StackTrace.
     *
     * @return int
     */
    public function getScriptId(): ?int
    {
    }

    /**
     * Returns the name of the resource that contains the script for the
     * function for this StackFrame.
     *
     * @return string
     */
    public function getScriptName(): string
    {
    }

    /**
     * Returns the name of the resource that contains the script for the
     * function for this StackFrame or sourceURL value if the script name
     * is undefined and its source ends with //# sourceURL=... string or
     * deprecated //@ sourceURL=... string.
     *
     * @return string
     */
    public function getScriptNameOrSourceURL(): string
    {
    }

    /**
     * Returns the name of the function associated with this stack frame.
     *
     * @return string
     */
    public function getFunctionName(): string
    {
    }

    /**
     * Returns whether or not the associated function is compiled via a call to
     * eval().
     *
     * @return bool
     */
    public function isEval(): bool
    {
    }

    /**
     * Returns whether or not the associated function is called as a
     * constructor via "new".
     *
     * @return bool
     */
    public function isConstructor(): bool
    {
    }

    /**
     * Returns whether or not the associated functions is defined in wasm.
     *
     * @return bool
     */
    public function isWasm(): bool
    {
    }
}

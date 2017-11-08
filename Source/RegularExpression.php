<?php

declare(strict_types=1);

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2017, Hoa community. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the Hoa nor the names of its contributors may be
 *       used to endorse or promote products derived from this software without
 *       specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS AND CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

namespace Hoa\Iterator;

/**
 * Class \Hoa\Iterator\RegularExpression.
 *
 * Re-implement the SPL RegexIterator class.
 * There are too many bugs in php-src and HHVM, so we re-implement it from
 * scratch without extending the existing class.
 *
 * Inspired by hhvm://hphp/system/php/spl/iterators/RegexIterator.php
 */
class RegularExpression extends Filter
{
    /**
     * Flag: match the entry key instead of the entry value.
     */
    public const USE_KEY      = 1;

    /**
     * Flag: invert match.
     */
    public const INVERT_MATCH = 2;

    /**
     * Mode and preg flag: only execute match (filter) for the current entry.
     */
    public const MATCH        = 0;

    /**
     * Mode and preg flag: first match for the current entry.
     */
    public const GET_MATCH    = 1;

    /**
     * Mode and preg flag: all matches for the current entry.
     */
    public const ALL_MATCHES  = 2;

    /**
     * Mode and preg flag: split values for the current entry.
     */
    public const SPLIT        = 3;

    /**
     * Mode and preg flag: replace the current entry.
     */
    public const REPLACE      = 4;

    /**
     * The regular expression to match.
     */
    protected $_regex     = null;

    /**
     * Operation mode, see the \RegexIterator::setMode method for a list of
     * modes.
     */
    protected $_mode      = 0;

    /**
     * Special flags, see the \RegexIterator::setFlag method for a list of
     * available flags.
     */
    protected $_flags     = 0;

    /**
     * The regular expression flags. See constants.
     */
    protected $_pregFlags = 0;

    /**
     * Current key.
     */
    protected $_key       = 0;

    /**
     * Current value.
     */
    protected $_current   = null;

    /**
     * Replacement.
     */
    public $replacement   = null;



    /**
     * Constructor.
     */
    public function __construct(
        iterable $iterator,
        string $regex,
        int $mode      = self::MATCH,
        int $flags     = 0,
        int $pregFlags = 0
    ) {
        parent::__construct($iterator);

        $this->_regex = $regex;
        $this->setMode($mode);
        $this->setFlags($flags);
        $this->setPregFlags($pregFlags);
        $this->replacement = null;

        return;
    }

    /**
     * Get accept status.
     */
    public function accept(): bool
    {
        if (is_array(parent::current())) {
            return false;
        }

        $this->_key     = parent::key();
        $this->_current = parent::current();

        $matches = [];
        $useKey  = $this->_flags & self::USE_KEY;
        $subject = $useKey ? $this->_key : $this->_current;
        $out     = false;

        switch ($this->_mode) {

            case self::MATCH:
                $out = 0 !== preg_match(
                    $this->_regex,
                    $subject,
                    $matches,
                    $this->_pregFlags
                );

                break;

            case self::GET_MATCH:
                $this->_current = [];
                $out            = 0 !== preg_match(
                    $this->_regex,
                    $subject,
                    $this->_current,
                    $this->_pregFlags
                );

                break;

            case self::ALL_MATCHES:
                $this->_current = [];
                $out            = 0 < preg_match_all(
                    $this->_regex,
                    $subject,
                    $this->_current,
                    $this->_pregFlags
                );

                break;

            case self::SPLIT:
                $this->_current = preg_split(
                    $this->_regex,
                    $subject,
                    null,
                    $this->_pregFlags
                );

                $out =
                    is_array($this->_current) &&
                    1 < count($this->_current);

                break;

            case self::REPLACE:
                $numberOfReplacement = 0;
                $result              = preg_replace(
                    $this->_regex,
                    $this->replacement,
                    $subject,
                    -1,
                    $numberOfReplacement
                );

                if (null === $result || 0 === $numberOfReplacement) {
                    $out = false;

                    break;
                }

                if (0 !== $useKey) {
                    $this->_key = $result;
                    $out        = true;

                    break;
                }

                $this->_current = $result;
                $out            = true;

                break;

            default:
                $out = false;

                break;
        }

        if (0 !== ($this->_flags & self::INVERT_MATCH)) {
            return false === $out;
        }

        return $out;
    }

    /**
     * Get current key.
     */
    public function key(): int
    {
        return $this->_key;
    }

    /**
     * Get current value.
     */
    public function current(): string
    {
        return $this->_current;
    }

    /**
     * Set mode.
     */
    public function setMode(int $mode): void
    {
        if ($mode < self::MATCH || $mode > self::REPLACE) {
            throw new \InvalidArgumentException(
                'Illegal mode ' . $mode . '.'
            );
        }

        $this->_mode = $mode;
    }

    /**
     * Set flags.
     */
    public function setFlags(int $flags): void
    {
        $this->_flags = $flags;
    }

    /**
     * Set preg flags.
     */
    public function setPregFlags(int $pregFlags): void
    {
        $this->_pregFlags = $pregFlags;
    }

    /**
     * Get regular expression.
     */
    public function getRegex(): ?string
    {
        return $this->_regex;
    }

    /**
     * Get mode.
     */
    public function getMode(): int
    {
        return $this->_mode;
    }

    /**
     * Get flags.
     */
    public function getFlags(): int
    {
        return $this->_flags;
    }

    /**
     * Get preg flags.
     */
    public function getPregFlags(): int
    {
        return $this->_pregFlags;
    }
}

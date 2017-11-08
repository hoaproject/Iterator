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
 * Class \Hoa\Iterator\Counter.
 *
 * A counter.
 */
class Counter implements Iterator
{
    /**
     * From (lower bound).
     */
    protected $_from = 0;

    /**
     * Current key.
     */
    protected $_key  = 0;

    /**
     * Current index.
     */
    protected $_i    = 0;

    /**
     * To (upper bound).
     */
    protected $_to   = 0;

    /**
     * Step.
     */
    protected $_step = 0;



    /**
     * Constructor.
     * Equivalent to:
     *     for($i = $from; $i < $to; $i += $step)
     */
    public function __construct(int $from, int $to, int $step)
    {
        if ($step <= 0) {
            throw new Exception(
                'The step must be non-negative; given %d.',
                0,
                $step
            );
        }

        $this->_from = $from;
        $this->_to   = $to;
        $this->_step = $step;

        return;
    }

    /**
     * Return the current element.
     */
    public function current(): int
    {
        return $this->_i;
    }

    /**
     * Return the key of the current element.
     */
    public function key(): int
    {
        return $this->_key;
    }

    /**
     * Move forward to next element.
     */
    public function next(): void
    {
        ++$this->_key;
        $this->_i += $this->_step;
    }

    /**
     * Rewind the iterator to the first element.
     */
    public function rewind(): void
    {
        $this->_key = 0;
        $this->_i   = $this->_from;
    }

    /**
     * Check if current position is valid.
     */
    public function valid(): bool
    {
        return $this->_i < $this->_to;
    }
}

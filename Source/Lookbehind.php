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
 * Class \Hoa\Iterator\Lookbehind.
 *
 * Look behind iterator.
 */
class Lookbehind extends IteratorIterator implements Outer
{
    /**
     * Current iterator.
     */
    protected $_iterator        = null;

    /**
     * Previous key.
     */
    protected $_previousKey     = -1;

    /**
     * Previous value.
     */
    protected $_previousCurrent = null;



    /**
     * Construct.
     */
    public function __construct(iterable $iterator)
    {
        $this->_iterator = $iterator;

        return;
    }

    /**
     * Get inner iterator.
     */
    public function getInnerIterator(): iterable
    {
        return $this->_iterator;
    }

    /**
     * Return the current element.
     */
    public function current()
    {
        return $this->getInnerIterator()->current();
    }

    /**
     * Return the key of the current element.
     */
    public function key()
    {
        return $this->getInnerIterator()->key();
    }

    /**
     * Move forward to next element.
     */
    public function next(): void
    {
        $this->_previousKey     = $this->key();
        $this->_previousCurrent = $this->current();

        $this->getInnerIterator()->next();
    }

    /**
     * Rewind the iterator to the first element.
     */
    public function rewind(): void
    {
        $this->_previousKey     = -1;
        $this->_previousCurrent = null;

        $this->getInnerIterator()->rewind();
    }

    /**
     * Check if current position is valid.
     */
    public function valid(): bool
    {
        return $this->getInnerIterator()->valid();
    }

    /**
     * Check whether there is a previous element.
     */
    public function hasPrevious(): bool
    {
        return -1 !== $this->_previousKey;
    }

    /**
     * Get previous value.
     */
    public function getPrevious()
    {
        return $this->_previousCurrent;
    }

    /**
     * Get previous key.
     */
    public function getPreviousKey()
    {
        return $this->_previousKey;
    }
}

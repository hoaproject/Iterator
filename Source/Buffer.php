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
 * Class \Hoa\Iterator\Buffer.
 *
 * Buffer iterator: Can go backward up to a certain limit, and forward.
 */
class Buffer extends IteratorIterator implements Outer
{
    /**
     * Buffer key index.
     */
    protected const BUFFER_KEY   = 0;

    /**
     * Buffer value index.
     */
    protected const BUFFER_VALUE = 1;

    /**
     * Current iterator.
     */
    protected $_iterator   = null;

    /**
     * Buffer.
     */
    protected $_buffer     = null;

    /**
     * Maximum buffer size.
     */
    protected $_bufferSize = 1;



    /**
     * Construct.
     */
    public function __construct(iterable $iterator, int $bufferSize)
    {
        $this->_iterator   = $iterator;
        $this->_bufferSize = max($bufferSize, 1);
        $this->_buffer     = new \SplDoublyLinkedList();

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
     * Get buffer.
     */
    protected function getBuffer(): \SplDoublyLinkedList
    {
        return $this->_buffer;
    }

    /**
     * Get buffer size.
     */
    public function getBufferSize(): int
    {
        return $this->_bufferSize;
    }

    /**
     * Return the current element.
     */
    public function current()
    {
        return $this->getBuffer()->current()[self::BUFFER_VALUE];
    }

    /**
     * Return the key of the current element.
     */
    public function key()
    {
        return $this->getBuffer()->current()[self::BUFFER_KEY];
    }

    /**
     * Move forward to next element.
     */
    public function next(): void
    {
        $innerIterator = $this->getInnerIterator();
        $buffer        = $this->getBuffer();

        $buffer->next();

        // End of the buffer, need a new value.
        if (false === $buffer->valid()) {
            for (
                $bufferSize        = count($buffer),
                $maximumBufferSize = $this->getBufferSize();
                $bufferSize >= $maximumBufferSize;
                --$bufferSize
            ) {
                $buffer->shift();
            }

            $innerIterator->next();

            $buffer->push([
                self::BUFFER_KEY   => $innerIterator->key(),
                self::BUFFER_VALUE => $innerIterator->current()
            ]);

            // Seek to the end of the buffer.
            $buffer->setIteratorMode($buffer::IT_MODE_LIFO | $buffer::IT_MODE_KEEP);
            $buffer->rewind();
            $buffer->setIteratorMode($buffer::IT_MODE_FIFO | $buffer::IT_MODE_KEEP);
        }
    }

    /**
     * Move backward to previous element.
     */
    public function previous(): void
    {
        $this->getBuffer()->prev();
    }

    /**
     * Rewind the iterator to the first element.
     */
    public function rewind(): void
    {
        $innerIterator = $this->getInnerIterator();
        $buffer        = $this->getBuffer();

        $innerIterator->rewind();

        if (true === $buffer->isEmpty()) {
            $buffer->push([
                self::BUFFER_KEY   => $innerIterator->key(),
                self::BUFFER_VALUE => $innerIterator->current()
            ]);
        }

        $buffer->rewind();

        return;
    }

    /**
     * Check if current position is valid.
     */
    public function valid(): bool
    {
        return
            $this->getBuffer()->valid() &&
            $this->getInnerIterator()->valid();
    }
}

<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2013, Ivan Enderlin. All rights reserved.
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

namespace {

from('Hoa')

/**
 * \Hoa\Iterator\Exception
 */
-> import('Iterator.Exception')

/**
 * \Hoa\Iterator
 */
-> import('Iterator.~')

/**
 * \Hoa\Iterator\Seekable
 */
-> import('Iterator.Seekable');

}

namespace Hoa\Iterator {

/**
 * Class \Hoa\Iterator\Basic.
 *
 * Make a simple and quick iterator of any types.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2013 Ivan Enderlin.
 * @license    New BSD License
 */

class Basic implements Iterator, Seekable, \Countable {

    /**
     * Collection of elements.
     *
     * @var \Hoa\Iterator array
     */
    protected $_collection = array();



    /**
     * Built the iterator.
     * If given a string, it will be transform to an array, else, if it is not
     * an array, an error will be thrown.
     *
     * @access  public
     * @param   mixed  $collection    Could be a string or an array.
     * @return  void
     * @throw   \Hoa\Iterator\Exception
     */
    public function __construct ( $collection ) {

        if(is_string($collection))
            $collection = array($collection);

        if(!is_array($collection))
            throw new Exception(
                'Cannot make a stable iterator. Need a well-formed collection, ' .
                'i.e. an array (or a list); given %s.', 0, gettype($collection));

        $this->setCollection($collection);

        return;
    }

    /**
     * Set collection.
     *
     * @access  protected
     * @param   array      $collection    The collection.
     * @return  array
     */
    protected function setCollection ( Array $collection ) {

        $old               = $this->_collection;
        $this->_collection = $collection;
        reset($this->_collection);

        return $old;
    }

    /**
     * Get the current collection for the iterator.
     *
     * @access  public
     * @return  mixed
     */
    public function current ( ) {

        return current($this->_collection);
    }

    /**
     * Get the current collection name for the iterator.
     *
     * @access  public
     * @return  mixed
     */
    public function key ( ) {

        return key($this->_collection);
    }

    /**
     * Advance the internal collection pointer, and return the current
     * collection.
     *
     * @access  public
     * @return  mixed
     */
    public function next ( ) {

        return next($this->_collection);
    }

    /**
     * Rewind the internal collection pointer, and return the first collection.
     *
     * @access  public
     * @return  mixed
     */
    public function rewind ( ) {

        return reset($this->_collection);
    }

    /**
     * Check if there is a current element after calls to the rewind() or the
     * next() methods.
     *
     * @access  public
     * @return  bool
     */
    public function valid ( ) {

        if(empty($this->_collection))
            return false;

        $key    = key($this->_collection);
        $return = (bool) next($this->_collection);
        prev($this->_collection);

        if(false === $return) {

            end($this->_collection);

            if($key === key($this->_collection))
                $return = true;
        }

        return $return;
    }

    /**
     * Seek to a position.
     *
     * @access  public
     * @param   mixed   $position    Position to seek.
     * @return  void
     */
    public function seek ( $position ) {

        if(!array_key_exists($position, $this->_collection))
            return;

        $this->rewind();

        while($position != $this->key())
            $this->next();

        return;
    }

    /**
     * Count number of elements in collection.
     *
     * @access  public
     * @return  int
     */
    public function count ( ) {

        return count($this->_collection);
    }
}

}

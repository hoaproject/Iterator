<?php

/**
 * Hoa Framework
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of Hoa Open Accessibility.
 * Copyright (c) 2007, 2010 Ivan ENDERLIN. All rights reserved.
 *
 * HOA Open Accessibility is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * HOA Open Accessibility is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with HOA Open Accessibility; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 *
 * @category    Framework
 * @package     Hoa_Iterator
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Iterator_Exception
 */
import('Iterator.Exception');

/**
 * Hoa_Iterator_Interface_Iterator
 */
import('Iterator.Interface.Iterator');

/**
 * Hoa_Iterator_Interface_Seekable
 */
import('Iterator.Interface.Seekable');

/**
 * Class Hoa_Iterator.
 *
 * Make a simple and quick iterator of any types.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2010 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Iterator
 */

class Hoa_Iterator implements Hoa_Iterator_Interface_Iterator,
                              Hoa_Iterator_Interface_Seekable,
                              Countable {

    /**
     * Collection of elements.
     *
     * @var Hoa_Iterator array
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
     * @throw   Hoa_Iterator_Exception
     */
    public function __construct ( $collection ) {

        if(is_string($collection))
            $collection = array($collection);

        if(!is_array($collection))
            throw new Hoa_Iterator_Exception(
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
     * Check if there is a current element after calls to the rewind or the next
     * methods.
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

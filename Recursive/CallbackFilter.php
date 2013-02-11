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

/**
 * Offering a PHP5.4 feature to prior versions.
 */
if(PHP_VERSION_ID < 50400) {

/**
 * Class RecursiveCallbackFilterIterator.
 *
 * A recursive callback filter iterator.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2013 Ivan Enderlin.
 * @license    New BSD License
 */
class RecursiveCallbackFilterIterator extends RecursiveFilterIterator {

    /**
     * Callback.
     *
     * @var Closure object
     */
    protected $_callback = null;



    /**
     * Create a filtered iterator from another iterator.
     *
     * @access  public
     * @param   \Iterator  $iterator    The iterator to be filtered.
     * @param   \Closure   $callback    The callback, which should return true
     *                                  to accept the current item false
     *                                  otherwise.
     * @return  void
     */
    public function __construct ( Iterator $iterator,
                                  Closure  $callback = null ) {

        $this->_callback = $callback;
        parent::__construct($iterator);

        return;
    }

    /**
     * Cals the callback with the current value, the current key and the inner
     * iterator as arguments.
     *
     * @access  public
     * @return  bool
     */
    public function accept ( ) {

        $callback = $this->_callback;

        return $callback(
            $this->current(),
            $this->key(),
            $this->getInnerIterator()
        );
    }

    /**
     * Return the inner iterator's children contained in a
     * RecursiveCallbackFilterIterator.
     *
     * @access  public
     * @return  \RecursiveCallbackFilterIterator
     */
    public function getChildren ( ) {

        return new static(
            $this->getInnerIterator()->getChildren(),
            $this->_callback
        );
    }
}

}

}

namespace Hoa\Iterator\Recursive {

/**
 * Class \Hoa\Iterator\Recursive\CallbackFilter.
 *
 * Extending the SPL RecursiveCallbackFilterIterator class.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2013 Ivan Enderlin.
 * @license    New BSD License
 */

class CallbackFilter extends \RecursiveCallbackFilterIterator { }

}

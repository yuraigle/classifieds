<?php
/**
 * Copyright (C) 2011 by Pieter Vogelaar (pietervogelaar.nl) and Kees Schepers (keesschepers.nl)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * This interface should be implemented by data sources for usage in Pike_Grid
 * 
 * @category   PiKe
 * @copyright  Copyright (C) 2011 by Pieter Vogelaar (pietervogelaar.nl) and Kees Schepers (keesschepers.nl)
 * @license    MIT
 */
interface Pike_Grid_DataSource_Interface
{
    /*
     * Returns a JSON encoded string of the data to be send
     */
    public function getJson();

    /**
     * Returns an array indicating on which field and which order the grid is by default sorted on.
     */
    public function getDefaultSorting();

    /**
     * Sets a column name which identifies every row in the grid
     */
    public function setIdentifierColumn($column);

    /**
     * Sets the closure for auto escaping column strings in the grid result
     */
    public function setAutoEscapeClosure(closure $closure);

    /**
     * Sets the jqGrid posted params
     */
    public function setParameters(array $params);

    /**
     * Specifies how many data is returned per 'page'
     */
    public function setResultsPerPage($num);

    /**
     * Defines what happends when the grid is sorted by the server. Return value depends on the
     * type of data source.
     *
     */
    public function setEventSort(Closure $function);

    /**
     * Defines what happends when the user filters data with jqGrid and send to the server. Return
     * value depends on the type of data source
     */
    public function setEventFilter(Closure $function);
}
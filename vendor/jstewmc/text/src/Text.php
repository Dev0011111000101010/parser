<?php
/**
 * The file for a text object
 *
 * @author     Jack Clayton <clayjs0@gmail.com>
 * @copyright  2016 Jack Clayton
 * @license    MIT
 */

namespace Jstewmc\Text;

/**
 * A text object
 *
 * @since  0.1.0
 */
class Text
{
    /* !Private properties */
    
    /**
     * @var    string  the object's text
     * @since  0.1.0
     */
    protected $text;
    
    
    /* !Magic methods */
    
    /**
     * Called when the object is constructed
     *
     * @param  string  $text  the object's text
     * @since  0.1.0 
     */
    public function __construct(string $text)
    {
        $this->text = $text;
    }
    
    /**
     * Called when the object is treated like a string
     *
     * @return  string
     * @since   0.1.0
     */
    public function __toString(): string
    {
        return $this->text;
    }
    
    
    /* !Get methods */
    
    /**
     * Returns the object's text
     *
     * @return  string
     * @since   0.1.0
     */
    public function getText(): string
    {
        return $this->text;
    }
}

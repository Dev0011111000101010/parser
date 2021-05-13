<?php 
/**
 * The file for the text object tests
 *
 * @author     Jack Clayton <clayjs0@gmail.com>
 * @copyright  2016 Jack Clayton
 * @license    MIT
 */

namespace Jstewmc\Text;

use Jstewmc\TestCase\TestCase;


/**
 * Tests for the text object
 */
class TextTest extends TestCase
{
    /* !__construct() */
    
    /**
     * __construct() should set the object's text
     */
    public function testConstruct()
    {
        $text = new Text('foo');
        
        $this->assertEquals('foo', $this->getProperty('text', $text));
        
        return;
    }
    
    /* !__toString() */
    
    /**
     * __toString() should return string
     */
    public function testToString()
    {
        $text = new Text('foo');
        
        $this->setProperty('text', $text, 'foo');
        
        $this->assertEquals('foo', (string) $text);
        
        return;
    }
    
    
    /* !getText() */ 
    
    /**
     * getText() should return string
     */
    public function testGetText()
    {
        $text = new Text('foo');
        
        $this->setProperty('text', $text, 'foo');
        
        $this->assertEquals('foo', $text->getText());
        
        return;
    }
}

<?php
/**
 * Simple HTML DOM Parser
 * 
 * @package SimpleHtmlDom
 * @link http://simplehtmldom.sourceforge.net
 * @license MIT
 */

/**
 * A HTML DOM parser written in PHP5+ let you manipulate HTML in a very easy way!
 * Require PHP 5+.
 * 
 * Copyright (c) 2009 S.C. Chen <me578022@gmail.com>
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
include_once 'simple_html_dom_helper.php';
include_once 'simple_html_dom_node.php';
include_once 'simple_html_dom_simple_html_dom.php';

/**
 * get html dom from file
 * 
 * @param string $url
 * @param bool $use_include_path
 * @param null|resource $context
 * @param int $offset
 * @param null|int $maxLen
 * @param bool $lowercase
 * @param bool $forceTagsClosed
 * @param string $target_charset
 * @param bool $stripRN
 * @param string $defaultBRText
 * @param string $defaultSpanText
 * 
 * @return simple_html_dom
 */
function file_get_html(
    $url,
    $use_include_path = false,
    $context = null,
    $offset = 0,
    $maxLen = null,
    $lowercase = true,
    $forceTagsClosed = true,
    $target_charset = DEFAULT_TARGET_CHARSET,
    $stripRN = true,
    $defaultBRText = DEFAULT_BR_TEXT,
    $defaultSpanText = DEFAULT_SPAN_TEXT)
{
    // Ensure we got a valid context.
    if (!isset($context)) {
        $context = stream_context_create(array());
    }

    // Send a request to get the content.
    $contents = file_get_contents($url, $use_include_path, $context, $offset);

    if (empty($contents) || ($maxLen > 0 && strlen($contents) > $maxLen)) {
        return false;
    }

    // Create a new simple_html_dom object and return it.
    return str_get_html(
        $contents,
        $lowercase,
        $forceTagsClosed,
        $target_charset,
        $stripRN,
        $defaultBRText,
        $defaultSpanText
    );
}

/**
 * get html dom from string
 * 
 * @param string $str
 * @param bool $lowercase
 * @param bool $forceTagsClosed
 * @param string $target_charset
 * @param bool $stripRN
 * @param string $defaultBRText
 * @param string $defaultSpanText
 * 
 * @return simple_html_dom
 */
function str_get_html(
    $str,
    $lowercase = true,
    $forceTagsClosed = true,
    $target_charset = DEFAULT_TARGET_CHARSET,
    $stripRN = true,
    $defaultBRText = DEFAULT_BR_TEXT,
    $defaultSpanText = DEFAULT_SPAN_TEXT)
{
    $dom = new simple_html_dom(
        null,
        $lowercase,
        $forceTagsClosed,
        $target_charset,
        $stripRN,
        $defaultBRText,
        $defaultSpanText
    );

    if (empty($str) || strlen($str) > MAX_FILE_SIZE) {
        $dom->clear();
        return false;
    }

    $dom->load($str, $lowercase, $stripRN);
    return $dom;
}

/**
 * dump html dom tree
 * 
 * @param object $node
 * @param bool $show_attr
 * @param int $deep
 * 
 * @return void
 */
function dump_html_tree($node, $show_attr = true, $deep = 0)
{
    $node->dump($node);
}
?>

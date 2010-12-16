<?php
/**
 * @package Guild News Feed Module for joomla 1.5
 * @author Brad Cimbura
 * @copyright (C) 2010- Brad Cimbura
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link http://soylentcode.com/
**/

print("<ul class=\"guildnewsfeed\">\n");
foreach($data as $y) {
  print("<li>$y</li>\n");
}
print("</ul>\n");
?>
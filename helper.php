<?php
/**
 * @package Guild News Feed Module for joomla 1.5
 * @author Brad Cimbura
 * @copyright (C) 2010- Brad Cimbura
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link http://soylentcode.com/
**/

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

class guildnewsfeed {

 //$params->get('realm'); //not setup yet
 //$params->get('region')
 //$params->get('updatetimer')
 //$params->get('displaycount') 
 
 private static $feedurl="http://us.battle.net/wow/en/guild/whisperwind/warped%20welcome/news";
 private static $cachefile="news.txt";
 private static $updatetime=10;
 private static $itemstodisplay=10;

  public function getdata() {
    guildnewsfeed::update_news();
    $fp=fopen(guildnewsfeed::$cachefile,"r");
    $i=0;
    $data=array();
    while(!feof($fp)) {
      $data[]=htmlentities(fgets($fp));
	  $i++;
      if($i>=guildnewsfeed::$itemstodisplay)
        break;
    }
    print("</ul>\n");
    fclose($fp);
	return $data;
  }

  private function clean_line($input) {
    $input=utf8_decode($input);
    $input=preg_replace("/<div id=\"news-tooltip[^>]*>/","|",$input);
    $input=preg_replace("/<dt>/","|",$input);
    $input=preg_replace ('/<[^>]*>/', '', $input);
    return $input;
  }

  private function update_news() {
    if(file_exists(guildnewsfeed::$cachefile))
      if(time()-guildnewsfeed::$updatetime*60< filemtime(guildnewsfeed::$cachefile))
	    return;
    $fp=fopen(guildnewsfeed::$feedurl,"r");
    $inline=0;
    $inli=0;
    $lines=0;
    $iline=array();
    while(!feof($fp)) {
      $line=fgets($fp);
      if(preg_match("/<ul class=\"activity-feed activity-feed-wide\">/",$line))
        $inline=1;
      if($inline && preg_match("/<\/ul>/",$line))
        $inline=0;
      if($inline) {
        if(preg_match("/<li[^>]*>/",$line))
          $inli=1;
        if($inli && preg_match("/<\/li>/",$line)) {
          $inli=0;
          $lines++;
        }
        if($inli) {
          $iline[$lines] .= trim(guildnewsfeed::clean_line($line));
        }
      }
    }
    fclose($fp);
    $fp=fopen(guildnewsfeed::$cachefile,"w");
    foreach($iline as $x) {
      $y=explode("|",$x);
      if(isset($y[2]))
        fwrite($fp,$y[0] . "|" . $y[2] . "\n");
      else
        fwrite($fp,$y[0] . "|" . $y[1] . "\n");
    }
    fclose($fp);
  }
}
?>

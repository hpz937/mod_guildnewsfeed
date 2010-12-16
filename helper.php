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
 //$params->get('guild');
 //$params->get('region')
 //$params->get('updatetimer')
 //$params->get('displaycount') 
 
 private static $feedurl="http://us.battle.net/wow/en/guild/whisperwind/warped%20welcome/news";
 private static $cachefile="modules/mod_guildnewsfeed/news.txt";
 private static $updatetime=10;
 private static $itemstodisplay=10;

  public function getdata() {
    guildnewsfeed::update_news();
    $fp=fopen(guildnewsfeed::$cachefile,"r");
    $i=0;
    $data=array();
    while(!feof($fp)) {
      $data[]=trim(fgets($fp));
	  $i++;
      if($i>=guildnewsfeed::$itemstodisplay)
        break;
    }
    print("</ul>\n");
    fclose($fp);
	return $data;
  }
  public function setvars($realm,$guild,$updatetime,$displaycount) {
	guildnewsfeed::$feedurl="http://us.battle.net/wow/en/guild/" . rawurlencode(strtolower($realm)) . "/" . rawurlencode(strtolower($guild)) . "/news";
	guildnewsfeed::$updatetime=$updatetime;
    guildnewsfeed::$itemstodisplay=$displaycount;
  }

  private function update_news() {
    if(file_exists(guildnewsfeed::$cachefile))
      if(time()-guildnewsfeed::$updatetime*60< filemtime(guildnewsfeed::$cachefile))
	    return;
	
    $fp=fopen(guildnewsfeed::$feedurl,"r");
	$inul=0;
    $inli=0;
    $line=0;
    $li=array();
    while(!feof($fp)) {
      $input=fgets($fp);
      if(preg_match("/<ul class=\"activity-feed activity-feed-wide\">/",$input))
        $inul=1;
      if($inul && preg_match("/<\/ul>/",$input))
        $inul=0;
      if($inul) {
        if(preg_match("/<li[^>]*>/",$input))
          $inli=1;
        if($inli) {
	      $li[$line] .= trim($input);
	    }
	    if($inli && preg_match("/<\/li>/",$input)) {
          $inli=0;
	      $line++;
	    }
      }
    }
    fclose($fp);
    $fp=fopen(guildnewsfeed::$cachefile,"w");
    foreach($li as $x) {
      fwrite($fp,guildnewsfeed::process($x) . "\n");
    }
    fclose($fp);
  }
  
  private function process($input) {
    preg_match("/<dt>([^<]*)<\/dt>/",$input,$dtmatch);
    if(preg_match("/player-ach|item-crafted|item-looted|item-purchased/",$input)) {
      preg_match("/<a href=\"([^\"]*)\"[^>]*>([^<]*)<\/a>([^<]*)<a href=\"([^\"]*)\"[^>]*>([^<]*)<\/a>/",$input,$matches);
      return "<a href=\"http://us.battle.net/" . $matches[1] . "\">" . htmlentities(utf8_decode($matches[2])) . "</a>" . $matches[3] . "<a href=\"http://us.battle.net/" . $matches[4] . "\">" . $matches[5] . "</a>. " . $dtmatch[1];
    }
    elseif(preg_match("/guild-ach/",$input)) {
      preg_match("/<\/a>([^<]*)<a href=\"([^\"]*)\"[^>]*>([^<]*)<\/a>/",$input,$matches);
      return $matches[1] . "<a href=\"http://us.battle.net/" . $matches[2] . "\">" . $matches[3] . "</a>. " . $dtmatch[1];
    }
    else {
      return "";
    }
}
}
?>

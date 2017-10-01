<?php

/**
 * Classe de gestion des chaines
 */
 
class tool_string {
	protected static $placeholders = array();

	public static function sort_utf8($lang, &$array) {
		setlocale(LC_COLLATE, $lang."UTF-8"); 
		usort($array, "strcoll");  
	}

	public static function ksort_utf8($lang, &$array) {
		setlocale(LC_COLLATE, $lang."UTF-8"); 
		uksort($array, "strcoll");  
	}

	/* CakePHP : https://github.com/cakephp/cakephp/blob/master/src/Utility/Text.php */
    public static function autoLinkUrls($text, $args = array()) {
        self::$placeholders = array();
        $pattern = '/(?:(?<!href="|src="|">)
            (?>
                (
                    (?<left>[\[<(]) # left paren,brace
                    (?>
                        # Lax match URL
                        (?<url>(?:https?|ftp|nntp):\/\/[\p{L}0-9.\-_:]+(?:[\/?][\p{L}0-9.\-_:\/?=&>\[\]()#@\+~%]+)?)
                        (?<right>[\])>]) # right paren,brace
                    )
                )
                |
                (?<url_bare>(?P>url)) # A bare URL. Use subroutine
            )
            )/ixu';
        $text = preg_replace_callback(
            $pattern,
            array('tool_string', 'insertPlaceholder'),
            $text
        );
        $text = preg_replace_callback(
            '#(?<!href="|">)(?<!\b[[:punct:]])(?<!http://|https://|ftp://|nntp://)www\.[^\s\n\%\ <]+[^\s<\n\%\,\.\ <](?<!\))#i',
            array('tool_string', 'insertPlaceholder'),
            $text
        );

        return self::_linkUrls($text, $args);
    }
    /**
     * Saves the placeholder for a string, for later use. This gets around double
     * escaping content in URL's..
     */
    protected static function insertPlaceHolder($matches) {
        $match = $matches[0];
        $envelope = array('', '');
        if (isset($matches['url'])) {
            $match = $matches['url'];
            $envelope = array($matches['left'], $matches['right']);
        }
        if (isset($matches['url_bare'])) {
            $match = $matches['url_bare'];
        }
        $key = md5($match);
        self::$placeholders[$key] = array(
            'content' => $match,
            'envelope' => $envelope
        );
        return $key;
    }

    /**
     * Replace placeholders with links.
     */
    protected static function _linkUrls($text, $args = array()) {
        $replace = array();
        foreach (self::$placeholders as $hash => $content) {
            $link = $url = $content['content'];
            $envelope = $content['envelope'];
            if (!preg_match('#^[a-z]+\://#i', $url)) {
                $url = 'http://' . $url;
            }
			$call_args = array_merge(array($link, "href", $url, "target", "_blank"), $args);
			$www = call_user_func_array(array('o', 'a_a'), $call_args);
            $replace[$hash] = $envelope[0] .$www . $envelope[1];
        }
        return strtr($text, $replace);
    }

    /**
     * Links email addresses
     */
    protected static function _linkEmails($text, $args = array()) {
        $replace = array();
        foreach (self::$placeholders as $hash => $content) {
            $url = $content['content'];
            $envelope = $content['envelope'];
			$call_args = array_merge(array($url, "href", "mailto:".$url), $args);
			$link = call_user_func_array(array('o', 'a_a'), $call_args);
            $replace[$hash] = $envelope[0] . $link . $envelope[1];
        }
        return strtr($text, $replace);
    }

    /**
     * Adds email links (<a href="mailto:....) to a given text.
     */
    public static function autoLinkEmails($text, $args = array()) {
        self::$placeholders = array();
        $atom = '[\p{L}0-9!#$%&\'*+\/=?^_`{|}~-]';
        $text = preg_replace_callback(
            '/(?<=\s|^|\(|\>|\;)(' . $atom . '*(?:\.' . $atom . '+)*@[\p{L}0-9-]+(?:\.[\p{L}0-9-]+)+)/ui',
            array('tool_string', 'insertPlaceholder'),
            $text
        );

        return self::_linkEmails($text, $args);
    }

    /**
     * Convert all links and email addresses to HTML links.
     */
    public static function autolink($text, $args = array()) {
        $text = self::autoLinkUrls($text, $args);
        return self::autoLinkEmails($text, $args);
    }

	/* CakePHP : https://github.com/cakephp/cakephp/blob/master/src/Utility/Text.php */
	public static function truncate($text, $length = 100, $ending = "&hellip;", $exact = true, $considerHtml = true) {
		if (is_array($ending)) {extract($ending);}
		if ($considerHtml) {
			if (mb_strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
				return $text;
			}
			$totalLength = mb_strlen($ending);
			$openTags = array();
			$truncate = '';
			preg_match_all('/(<\/?([\w+]+)[^>]*>)?([^<>]*)/', $text, $tags, PREG_SET_ORDER);
			foreach ($tags as $tag) {
				if (!preg_match('/img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param/s', $tag[2])) {
					if (preg_match('/<[\w]+[^>]*>/s', $tag[0])) {
						array_unshift($openTags, $tag[2]);
					} else if (preg_match('/<\/([\w]+)[^>]*>/s', $tag[0], $closeTag)) {
						$pos = array_search($closeTag[1], $openTags);
						if ($pos !== false) {
							array_splice($openTags, $pos, 1);
						}
					}
				}
				$truncate .= $tag[1];
				$contentLength = mb_strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $tag[3]));
				if ($contentLength + $totalLength > $length) {
					$left = $length - $totalLength;
					$entitiesLength = 0;
					if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $tag[3], $entities, PREG_OFFSET_CAPTURE)) {
						foreach ($entities[0] as $entity) {
							if ($entity[1] + 1 - $entitiesLength <= $left) {
								$left--;
								$entitiesLength += mb_strlen($entity[0]);
							} else {
								break;
							}
						}
					}
					$truncate .= mb_substr($tag[3], 0 , $left + $entitiesLength);
					break;
				} else {
					$truncate .= $tag[3];
					$totalLength += $contentLength;
				}
				if ($totalLength >= $length) {
					break;
				}
			}
		} 
		else {
			if (mb_strlen($text) <= $length) {
				return $text;
			}
			else {
				$truncate = mb_substr($text, 0, $length - strlen($ending));
			}
		}
		if (!$exact) {
			$spacepos = mb_strrpos($truncate, ' ');
			if (isset($spacepos)) {
				if ($considerHtml) {
					$bits = mb_substr($truncate, $spacepos);
					preg_match_all('/<\/([a-z]+)>/', $bits, $droppedTags, PREG_SET_ORDER);
					if (!empty($droppedTags)) {
						foreach ($droppedTags as $closingTag) {
							if (!in_array($closingTag[1], $openTags)) {
								array_unshift($openTags, $closingTag[1]);
							}
						}
					}
				}
				$truncate = mb_substr($truncate, 0, $spacepos);
			}
		}
		$truncate .= $ending;
		if ($considerHtml) {
			foreach ($openTags as $tag) {
				$truncate .= '</'.$tag.'>';
			}
		}
		return $truncate;
	}
}
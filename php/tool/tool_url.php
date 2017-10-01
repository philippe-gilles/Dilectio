<?php

class tool_url {
	private $scheme = null;
	private $url = null;
	private $host = null;
	private $html = null;
	private $dom = null;
	private $tags = array();

	private $mime_type = null;
	private $html_mime_type = array("text/html");
	private $image_mime_type = array("image/jpeg", "image/png", "image/gif");
	
	private $site = null;
	private $title = null;
	private $descr = null;
	private $image = null;
	
	public function __construct($url) {
		if ($ret = @parse_url($url)) {
			if (isset($ret["scheme"]) ) {
				$this->scheme = $ret["scheme"];
			}
			else {
				$this->scheme = "http";
				if (!(strncmp($url, "//", 2))) {
					$url = $this->scheme.":".$url;
				}
				else {
					$url = $this->scheme."://".$url;
				}
			}
			$this->host = isset($ret["host"])?$ret["host"]:null;
		}
		$this->url = $url;
	}

	/* Getters */
	public function get_url() {return $this->url;}
	public function get_scheme() {return $this->scheme;}
	public function get_host() {return $this->host;}
	public function get_mime_type() {return $this->mime_type;}
	public function is_html() {
		$ret = false;
		if (strlen($this->mime_type) > 0) {
			$ret = in_array($this->mime_type, $this->html_mime_type);
		}
		return $ret;
	}
	public function is_image() {
		$ret = false;
		if (strlen($this->mime_type) > 0) {
			$ret = in_array($this->mime_type, $this->image_mime_type);
		}
		return $ret;
	}

	/* Infos */
	public function get_site() {return $this->site;}
	public function get_title() {return $this->title;}
	public function get_description() {return $this->descr;}
	public function get_image() {return $this->image;}
	
	public function preload() {
		/* Download avec file_get_contents */
		$this->html = @file_get_contents($this->url);
		if ($this->html === false) {
			/* En cas d'échec on tente de passer par CURL */
			$ch = @curl_init();
			@curl_setopt($ch, CURLOPT_URL, $this->url);
			@curl_setopt($ch, CURLOPT_HEADER, true);
			@curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			@curl_setopt($ch, CURLOPT_USERAGENT, "Dilectio");
			@curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$this->html = @curl_exec($ch);
			if (@curl_errno($ch) > 0) {@curl_close($ch);return false;}
			$this->mime_type = strtolower(@curl_getinfo($ch, CURLINFO_CONTENT_TYPE));
			@curl_close($ch);
		}
		else {
			/* Récup du mime type */
			$finfo = new finfo(FILEINFO_MIME_TYPE);
			$this->mime_type = strtolower($finfo->buffer($this->html));
		}

		/* Elimination des mime types avec point virgule */
		$composite = strpos($this->mime_type, ";");
		if ($composite !== false) {$this->mime_type = substr($this->mime_type, 0, $composite);}
		
		return true;
	}

	public function load() {
		/* Preload */
		$ret = $this->preload();
		if (($ret) && ($this->is_html())) {
			/* Chargement dans le DOM */
			$old_libxml_error = libxml_use_internal_errors(true);
			$this->dom = new DOMDocument();
			$ret = @$this->dom->loadHTML(mb_convert_encoding($this->html, 'HTML-ENTITIES', 'UTF-8'));
			libxml_use_internal_errors($old_libxml_error);
			
			/* Récupération des métas (! avec name seulement) */
			if ($ret) {
				$tags = @get_meta_tags($this->url);
				if ($tags !== false) {$this->tags = $tags;}
			}
		}

		return $ret;
	}
	
	public function copy_to_local($chemin, $basename) {
		$ret = null;
		if ((!(is_null($this->html))) && ($this->html !== false)) {
			switch ($this->mime_type) {
				case "image/jpeg" :
					$extension = "jpg";break;
				case "image/png" :
					$extension = "png";break;
				case "image/gif" :
					$extension = "gif";break;
				default :
					$extension = null;
			}
			if (!(is_null($extension))) {
				$dest = $chemin.$basename.".".$extension;
				$copy = @file_put_contents($dest, $this->html);
				if ($copy !== false) {
					$ret = $extension;
				}
			}
		}
		return $ret;
	}

	public function retrieve_info() {
		if ((is_null($this->html)) || ($this->html === false) || (is_null($this->dom))) {
			return;
		}
		
		/* TODO : pour les images, voir aussi dans les favicons le cas échéant. */
		/*        Cela dit, même facebook ou hotmail ne le font pas !           */
		$this->retrieve_opengraph();
		$this->retrieve_twitter();
		$this->retrieve_others();
	}

	public function retrieve_opengraph() {
		$metas = $this->dom->getElementsByTagName("meta");
		foreach($metas as $meta) {
			$property = $meta->getAttribute("property");
			if (!(strncmp($property, "og:", 3))) { 
				$content = $meta->getAttribute("content");
				switch($property) {
					case "og:site_name" :
						if (is_null($this->site)) {$this->site = $content;}
						break;
					case "og:title" :
						if (is_null($this->title)) {$this->title = $content;}
						break;					
					case "og:description" :
						if (is_null($this->descr)) {$this->descr = $content;}
						break;	
					case "og:image" :
						if (is_null($this->image)) {$this->image = $content;}
						break;
					default:
						break;
				}
			}
		}
	}
	
	public function retrieve_twitter() {
		foreach ($this->tags as $name => $content) {
			if (!(strncmp($name, "twitter:", 8))) {
				switch($name) {
					case "twitter:site" :
						if (is_null($this->site)) {$this->site = $content;}
						break;
					case "twitter:title" :
						if (is_null($this->title)) {$this->title = $content;}
						break;					
					case "twitter:description" :
						if (is_null($this->descr)) {$this->descr = $content;}
						break;	
					case "twitter:image" :
						if (is_null($this->image)) {$this->image = $content;}
						break;
					default:
						break;
				}
			}
		}
	}
	
	public function retrieve_others() {
		/* Site */
		if ((strlen($this->host) > 0) && (is_null($this->site))) {$this->site = $this->host;}
		
		/* Title */
		$titles = $this->dom->getElementsByTagName("title");
		$nb_titles = count($titles);
		if ($nb_titles > 0) {
			$title = $titles[($nb_titles - 1)];
			if (!(is_null($title))) {
				$content = $title->nodeValue;
				if ((strlen($content) > 0) && (is_null($this->title))) {$this->title = $content;}
			}
		}
		
		/* Description + ??? */
		foreach ($this->tags as $name => $content) {
			if (strncmp($name, "twitter:", 8)) {
				switch($name) {				
					case "description" :
						if (is_null($this->descr)) {$this->descr = $content;}
						break;
					default:
						break;
				}
			}
		}
	}

	/* From Stackoverflow (forgot the link :-(  ) */
	public function is_valid() {
		if (!$this->url || !is_string($this->url)) {
			return false;
		}

		// quick check url is roughly a valid http request: ( http://blah/... ) 
		if (! preg_match('/^http(s)?:\/\/[a-z0-9-]+(\.[a-z0-9-]+)*(:[0-9]+)?(\/.*)?$/i', $this->url)) {
			return false;
		}

		// all good!
		return true;
	}

	public function is_broken() {
		return ($this->get_response() != 200);
	}

	/* From Stackoverflow (forgot the link :-(  ) */
	public function get_response($followredirects = true) {
		if (! $this->url || ! is_string($this->url)) {return false;}

		$headers = @get_headers($this->url);
		if ($headers && is_array($headers)) {
			if ($followredirects) {
				// we want the the last errorcode, reverse array so we start at the end:
				$headers = array_reverse($headers);
			}

			foreach($headers as $hline) {
				// search for things like "HTTP/1.1 200 OK" , "HTTP/1.0 200 OK" , "HTTP/1.1 301 PERMANENTLY MOVED" , "HTTP/1.1 400 Not Found" , etc.
				// note that the exact syntax/version/output differs, so there is some string magic involved here
				if (preg_match('/^HTTP\/\S+\s+([1-9][0-9][0-9])\s+.*/', $hline, $matches) ) { // "HTTP/*** ### ***"
					$code = $matches[1];
					return $code;
				}
			}

			// no HTTP/xxx found in headers:
			return false;
		}

		// no headers :
		return false;
	}

}
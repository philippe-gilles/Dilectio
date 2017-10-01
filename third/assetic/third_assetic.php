<?php

/**
 * Dilectio : Classe pour l'interfaçage du tiers assetic
 */

/* Autoloader supplémentaire pour gérer les espaces de nom d'Assetic */
spl_autoload_register(array("third_assetic", "dilectio_assetic_autoload"));

class third_assetic {
	private $chemin = null;
	private $cible = null;
	public $collection = null;

	public function __construct($chemin, $cible) {
		$this->chemin = $chemin;
		$this->cible = $cible;
		$this->collection = new Assetic\Asset\AssetCollection();
		$this->collection->setTargetPath($this->cible); 
	}

	public function add_glob($pattern) {
		$asset = new Assetic\Asset\GlobAsset($pattern);
		$this->collection->add($asset);
	}
	
	public function add_file($file) {
		$asset = new Assetic\Asset\FileAsset($file);
		$this->collection->add($asset);
	}
	
	public function save() {
		$manager = new Assetic\AssetManager();
		$cache = new Assetic\Asset\AssetCache($this->collection, new Assetic\Cache\FilesystemCache("assets/cache"));
		$manager->set("collection", $cache);

		$writer = new Assetic\AssetWriter($this->chemin);
		$writer->writeManagerAssets($manager);
	}

	public function dump() {
		echo $this->collection->dump();
	}

	public static function dilectio_assetic_autoload($nom_classe) {
		if (!(strncmp($nom_classe, "Assetic\\", 8))) {
			$script = "";$namespace = "";
			if ($lastNsPos = strripos($nom_classe, '\\')) {
				$namespace = substr($nom_classe, 0, $lastNsPos);
				$nom_classe = substr($nom_classe, $lastNsPos + 1);
				$script = str_replace("\\", "/", $namespace)."/";
			}
			$script .= $nom_classe.".php";
			require_once(__DILECTIO_THIRD."assetic/".$script);
		}
	}
}


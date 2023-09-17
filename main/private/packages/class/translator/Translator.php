<?php
	 /*
	 * @see 	   https://github.com/benfiratkaya/Translator
	 * @author 	Fırat Kaya
	 * @version    1.0
	 * @copyright  2019 Fırat Kaya
	 */
	 namespace Translator;
	 use Translator\Generator;
 	class Translator extends Register {
 		private $exception;
 		private $type;
 		private $lang;
 		private $path;
 		private $themePath;
 		private $file;
 		private $themeFile;
 		private $text;
 		
 		public function __construct($exception = null, $type = null, $lang = null, $path = null, $themePath = null) {
 			$this->exception = (isset($exception)) ? $exception : false;
 			$this->type = (isset($type)) ? $type : 'php';
 			$this->lang = (isset($lang)) ? $lang : 'en_US';
 			$this->path = (isset($path)) ? rtrim($path, '/') : __DIR__.'/languages';
 			$this->themePath = (isset($themePath)) ? rtrim($themePath, '/') : null;
 			$this->setFile();
 		}
 		
 		private function setFile() {
 			$supportedTypes = array('php', 'json', 'ini');
 			if (!in_array($this->type, $supportedTypes)) {
 				if ($this->exception === true) {
 					throw new Exception('This type is not supported!');
 				}
 				return false;
 			}
 			$this->file = $this->path.'/'.$this->lang.'.'.$this->type;
 			$this->themeFile = ($this->themePath !== null) ? $this->themePath.'/'.$this->lang.'.'.$this->type : null;
 			if (!is_file($this->file)) {
 				if ($this->exception === true) {
 					throw new Exception('File not found!');
 				}
 				return false;
 			}
      if (!is_file($this->themeFile)) {
        $this->themeFile = null;
      }
 			$this->text = Generator::read($this->type, $this->file, $this->themeFile);
 		}
 		
 		public function setType($type = null) {
 			$this->type = (isset($type)) ? $type : 'php';
 		}
 		
 		public function setLang($lang = null) {
 			$this->lang = (isset($lang)) ? $lang : 'en_US';
 		}
 		
 		public function setPath($path = null) {
 			$this->path = (isset($path)) ? rtrim($path, '/') : __DIR__.'/languages';
 		}
 		
 		public function setException($exception = null) {
 			$this->exception = (isset($exception)) ? $exception : false;
 		}
 		
 		public function update() {
 			$this->setFile();
 		}
 		
 		public function translate($text = null, $variable = null) {
 			if (isset($variable)) {
 				return strtr(((isset($this->text[$text])) ? $this->text[$text] : $text), $variable);
 			}
 			return ((isset($this->text[$text])) ? $this->text[$text] : $text);
  		}
 	}
    

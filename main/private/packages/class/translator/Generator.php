<?php
	namespace Translator;
	abstract class Generator {
		public static function read($type = null, $file = null, $themeFile = null) {
			if ($type === 'php') {
				return include_once $file;
			}
			if ($type === 'json') {
        $text = json_decode(file_get_contents($file), true);
        $themeText = ($themeFile !== null) ? json_decode(file_get_contents($themeFile), true) : null;
        if ($themeText !== null) {
          $text = array_merge($text, $themeText);
        }
				return $text;
			}
			if ($type === 'ini') {
				return parse_ini_file($file);
			}
			return false;
		}
	}

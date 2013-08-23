<?php

/**
 * MeCab for PHP5
 * https://github.com/odoku/MeCab-for-PHP5/blob/master/MeCab.php
 * php_mecabをextensionとしてインストールするのはあきらめた。
 * これでなんとかなるっしょ。
 */

/**
 * このクラスは、PHP5からMeCabを操作する為の機能を提供します。
 * PHPから mecab コマンドをコール出来る環境である必要があります。
 *
 * @package mecab
 * @author odoku <odoku@shamoo.org>
 * @copyright Copyright (c) 2011, odoku.net
 * @since PHP 5.1
 * @version 1.00
 * @since 2011/05/11
 */
class MeCab {
	/**
	 * MeCab コマンドのパス
	 */
	private static $command = '/usr/local/bin/mecab';
	/**
	 * MeCab コマンドのパスを取得します
	 *
	 * @access public
	 * @static
	 * @return string
	 */
	public static function getCommandPath() {
		return self::$command;
	}
	/**
	 * MeCab コマンドのパスを設定します
	 *
	 * @access public
	 * @static
	 * @param string $path
	 * @return string
	 */
	public static function setCommandPath($path) {
		return self::$command = $path;
	}
	/**
	 * 指定した文字列を MeCab にて形態素解析します
	 *
	 * @access public
	 * @static
	 * @param string $string
	 * @return array
	 */
	public static function analyze($string) {
		$lines = self::_command(self::$command, $string);
		$items = array();
		foreach ($lines as $num => $line) {
			$line = str_replace(array("\r\n", "\r", "\n"), '', $line);
			if (strcasecmp($line, 'EOS') === 0) break;

			$line = str_replace("\t", ',', $line);
			$item = explode(',', $line);

			$items[] = $item;
		}

		return $items;
	}
	/**
	 * 指定した文字列を MeCab にて分かち書きにします
	 *
	 * @access public
	 * @static
	 * @param string $string
	 * @return string
	 */
	public static function split($string) {
		$lines = self::_command(self::$command . ' -O wakati', $string);
		return implode(PHP_EOL, $lines);
	}
	/**
	 * コマンドを実行して、出力結果を取得します
	 *
	 * @access public
	 * @static
	 * @param string $command
	 * @param string $input
	 * @param int    $status
	 * @param string $error
	 * @return mixed
	 */
	private static function _command($command, $input, &$status = 0, &$error = '') {
		$descriptorspec = array(
			0 => array('pipe', 'r'),
			1 => array('pipe', 'w'),
			2 => array('pipe', 'w'),
		);
		$proc = proc_open($command, $descriptorspec, $pipe);
		if (!is_resource($proc)) return false;

		fwrite($pipe[0], $input);
		fclose($pipe[0]);

		$lines = array();
		while ($line = fgets($pipe[1])) $lines[] = str_replace(array("\r\n", "\r", "\n"), '', $line);
		fclose($pipe[1]);

		fwrite($pipe[2], $error);
		fclose($pipe[2]);

		$status = proc_close($proc);

		return ($status === 0) ? $lines : false;
	}
}
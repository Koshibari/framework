<?php

namespace Koshiba\Framework\Core\Abstracts;

use JetBrains\PhpStorm\ArrayShape;
use Koshiba\Libs\Static\Helper;

class View {
	/**
	 * Current route and params(controller, action, params)
	 * @var array
	 */
	public readonly array $route;

	/**
	 * Current View
	 * @var string
	 */
	public readonly string $view;

	/**
	 * Current Layout
	 * @var string|bool
	 */
	public readonly string|bool $layout;

	/**
	 * @var array
	 */
	protected readonly array $config;

	/**
	 * Array of script tags that was cut from <b>View</b>
	 * @var array
	 */
	protected static array $scripts = [];

	/**
	 * Array of Stylesheets tags that was cut from <b>View</b>
	 * @var array
	 */
	protected static array $styleSheets = [];

	/**
	 * Default loaded MetaData
	 * @var array
	 */
	protected static array $meta = [];

	/**
	 * @param array       $route
	 * @param string|bool $layout
	 * @param string      $view
	 */
	public function __construct(array $route, string|bool $layout = '', string $view = '') {
		$this->route  = $route;
		$this->config = Helper::coreConfig()->app;
		if ($layout === false) {
			$this->layout = false;
		} else {
			$this->layout = $layout ?: $this->config['defaultLayout'];
		}
		$this->view = $view;
	}

	#[ArrayShape([
		"layout"  => "false|string",
		"content" => "false|string"
	])] public function render(array $vars): array {
		$prefix    = $this->route['prefix'];
		$file_view = path('view', $prefix) . DS . $this->route['controller'] . DS . $this->view . '.php';
		extract($vars);
		ob_start();
		if ($this->layout !== false) {
			if (is_file($file_view)) {
				require $file_view;
			} else {
				throw new \Exception("<p class='p-3'>View <b>{$file_view}</b> was not found</p>", 404);
			}
		}
		$content = ob_get_clean();


		if (false !== $this->layout) {
			$file_layout = path('view', $prefix) . DS . "layouts" . DS . $this->layout . '.php';
			$this->cutScripts($content);
			$this->cutStylesheets($content);
			ob_start();
			if (is_file($file_layout)) {
				require_once $file_layout;
			} else {
				throw new \Exception("<p class='p-3'>Layout <b>{$file_layout}</b> was not found", 404);
			}
			return [
				"layout"  => ob_get_clean(),
				"content" => $content
			];
		}

		return [
			'layout'  => '',
			'content' => ''
		];
	}

	/**
	 * Cuts scripts from View and saves them in View scripts field
	 *
	 * @param string $content
	 *
	 * @return void
	 */
	protected function cutScripts(string &$content): void {
		$pattern = "#<script.*?>.*?</script>#is";
		preg_match_all($pattern, $content, self::$scripts);
		if (!empty(self::$scripts)) {
			$content = preg_replace($pattern, '', $content);
		}
	}

	/**
	 * Cuts stylesheets from View and saves them in View stylesheets field
	 *
	 * @param string $content
	 *
	 * @return void
	 */
	protected function cutStylesheets(string &$content): void {
		$patternLink  = "#<link.*?stylesheet.*?>#is";
		$patternStyle = "#<style>.*?</style>#is";
		$link         = [];
		$style        = [];
		preg_match_all($patternLink, $content, $link);
		preg_match_all($patternStyle, $content, $style);
		if (!empty($link)) {
			$content = preg_replace($patternLink, "", $content);
		}
		if (!empty($style)) {
			$content = preg_replace($patternStyle, "", $content);
		}

		self::$styleSheets = array_merge($link, $style);
	}

	/**
	 * Usable only inside View
	 * @return void
	 */
	public static function loadScripts(): void {
		if (!empty(self::$scripts[0])) {
			$scripts = self::$scripts[0];
			foreach ($scripts as $script) {
				echo $script;
			}
		}
	}

	/**
	 * Usable only inside View
	 * @return void
	 */
	public static function loadStyle(): void {
		if (!empty(self::$styleSheets[0])) {
			$styles = self::$styleSheets[0];
			foreach ($styles as $style) {
				echo $style;
			}
		}
		if (!empty(self::$styleSheets[1])) {
			$styles = self::$styleSheets[1];
			foreach ($styles as $style) {
				echo $style;
			}
		}
	}

	public static function setMeta(string $title = '', ?string $desc = '', ?string $keywords = ''): void {
		$config                 = Helper::coreConfig()->app['meta'];
		self::$meta['title']    = $title === '' ? $config['title'] : $title;
		self::$meta['desc']     = $desc === '' ? $config['desc'] : $desc;
		self::$meta['keywords'] = $keywords === '' ? $config['keywords'] : $keywords;
	}

	public static function getMeta(): array {
		return self::$meta;
	}

	public static function loadMeta(): void {
		$title    = self::$meta['title'];
		$desc     = self::$meta['desc'];
		$keywords = self::$meta['keywords'];
		echo <<<END
<title>$title</title>
<meta name="description" content="$desc" />
<meta name="keywords" content="$keywords" />
END;
	}
}
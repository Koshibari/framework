<?php

namespace Koshiba\Framework\Widgets\Menu;

use Koshiba\Framework\Core\Abstracts\Widget;

class Menu extends Widget {

	protected array    $categories;
	protected array    $tree;
	protected string   $menuHtml;
	protected string   $template;
	protected string   $container;
	protected string   $table;
	protected int|bool $cache;

	/**
	 * @param array $options
	 *
	 * @throws \Exception
	 */
	public function __construct(array $options = [
		'cache'     => null,
		'container' => null,
		'table'     => null,
		'template'  => null
	]) {
		parent::__construct();
		$this->options($options);

		$this->container = $this->getOption('container');
		$template        = $this->getOption('template');
		$this->template  = $this->getTemplate($template);
		$this->table     = $this->getOption('table');
		$this->cache     = $this->getOption('cache');

		$this->run();
	}

	protected function run(): void {
		if (!$this->cache) {
			$this->categories = \R::getAssoc("SELECT * FROM $this->table");
			$this->tree       = $this->getTree();
			$this->menuHtml   = $this->getMenuHtml($this->tree);
		} else {
			$this->menuHtml = $this->registry->cache->get("koshiba.menu.{$this->container}.{$this->table}");

			if (!$this->menuHtml) {
				$this->categories = \R::getAssoc("SELECT * FROM $this->table");
				$this->tree       = $this->getTree();
				$this->menuHtml   = $this->getMenuHtml($this->tree);
				$this->registry->cache->set("koshiba.menu.{$this->container}.{$this->table}", $this->menuHtml, $this->cache);
			}
		}


		$this->render();
	}

	protected function render(): void {
		echo "<$this->container>";
		echo "$this->menuHtml";
		echo "</$this->container>";
	}

	protected function getTree(): array {
		$tree = [];
		$data = $this->categories;
		foreach ($data as $id => &$node) {
			if (!$node['parent']) {
				$tree[$id] = &$node;
			} else {
				$data[$node['parent']]['childs'][$id] = &$node;
			}
		}

		return $tree;
	}


	protected function getMenuHtml(array $tree, string $tab = ''): string {
		$string = "";
		foreach ($tree as $id => $category) {
			$string .= $this->catToTemplate($category, $tab, $id);
		}
		return $string;
	}

	protected function catToTemplate(array $category, string $tab, int $id): string {
		ob_start();
		require $this->template;
		return ob_get_clean();
	}

	private function getTemplate(string $temp): string {
		$file = __DIR__ . DS . DS . $this->container . "_" . $temp . ".php";
		if (is_file($file)) {
			return $file;
		}
		throw new \Exception("Template {$file} does not exist", 404);
	}
}
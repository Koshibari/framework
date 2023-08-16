<?php

namespace Koshiba\Framework\Core\Abstracts;

use JetBrains\PhpStorm\ArrayShape;
use Koshiba\Framework\Core\Http\Request;
use Koshiba\Framework\Libs\Static\Helper;
use Koshiba\Framework\Core\Registry\Registry;

abstract class Controller {

	/**
	 * Current route and params(controller, action, params)
	 * @var array
	 */
	protected readonly array $route;

	/**
	 * Current View object
	 * @var string
	 */
	protected string $view;

	/**
	 * Current Layout with ability of not being rendered as False
	 * @var string|bool
	 */
	protected string|bool $layout;

	/**
	 * App configuration array
	 * @var array
	 */
	protected readonly array $config;

	/**
	 * Local Variables that can be accessed in View
	 * @var array
	 */
	protected array $vars;

	/**
	 * Got Request
	 * @var Request
	 */
	protected Request $request;

	public function __construct(array $route) {
		#echo $this::class;
		$this->route  = $route;
		$this->config = Helper::coreConfig()->app;
		$this->vars   = [];
		$this->store(['config' => $this->config]);
		$this->view   = $route['action'];
		$this->layout = '';
		View::setMeta();
	}

	#[ArrayShape([
		"layout"  => "false|string",
		"content" => "false|string"
	])] public function getView(): array {
		$viewEntity = new View($this->route, $this->layout, $this->view);
		return $viewEntity->render($this->vars);
	}

	/**
	 * Stores Variables in Array and lets <b>View</b> and <b>Layout</b> see them
	 *
	 * @param array $vars
	 *
	 * @return void
	 */
	public function store(array $vars): void {
		if (!empty($this->vars)) {
			$this->vars = array_merge($this->vars, $vars);
		} else {
			$this->vars = $vars;
		}
	}

	/**
	 * Sets $this->request to current Request
	 *
	 * @param Request $request
	 *
	 * @return void
	 */
	public function setRequest(Request $request): void {
		$this->request = $request;
	}

	public function loadView(string $view, array $vars = []): void {
		extract($vars);
		require path('view') . DS . $view . '.php';
	}
}
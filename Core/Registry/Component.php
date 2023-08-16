<?php

namespace Koshiba\Framework\Core\Registry;

use Koshiba\Core\Abstracts\Singletone;

trait Component {
	use Singletone;

	public function getMethods(): void {
		$methodsOut       = [];
		$methods          = get_class_methods($this);
		$componentMethods = get_class_methods(Component::class);
		foreach ($methods as $method) {
			if (in_array($method, $componentMethods))
				continue;
			$reflection = new \ReflectionMethod($this, $method);
			if ($reflection->isPublic()) {
				$vars = [];
				foreach ($reflection->getParameters() as $parameter) {
					$vars[] = [
						'type'    => $parameter->getType(),
						'name'    => $parameter->name,
						'default' => $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null
					];

				}
				$phpDoc              = $reflection->getDocComment();
				$phpDoc              = implode("\n", array_map('trim', explode("\n", $phpDoc)));
				$methodsOut[$method] = [
					'vars'   => $vars,
					'return' => $reflection->getReturnType() ?? "void",
					'phpDoc' => sprintf("    <pre style='font-weight: bold; font-style: italic'>\r\n%s\r\n    </pre>\r\n", print_r($phpDoc, true))
				];
			}
		}
		$this->printMethods($methodsOut);
	}

	private function printMethods(array $methods): void {
		$content = "<div class='row align-items-baseline m-0' style='color: #fff'>\r\n";
		$i       = 1;
		foreach ($methods as $methodName => $data) {
			$content .= "  <div class='col-5' style='margin-bottom: 10px; border-radius: 3px;background: #e53e4b; padding: 8px'>\r\n";
			$content .= "    <h2 style='color: #1a1e21'>{$methodName}::<span style='color: #ccc'>{$data['return']}</span></h2>\r\n";
			$content .= $data['phpDoc'];
			foreach ($data['vars'] as $var) {
				$name = $var['name'];
				unset($var['name']);
				$content .= "\${$name}: <br>";
				$content .= "-->type: <b style='color: #1a1e21'>{$var['type']}</b><br>";
				$content .= "-->default: <b style='color: #1a1e21'>{$var['default']}</b><br><br>";
			}
			$content .= "  </div>\r\n";
			if ($i % 2 != 0) {
				$content .= "<div class='col-2'></div>";
			}
			$i++;
		}
		$content .= "</div>";

		echo $content;
	}
}
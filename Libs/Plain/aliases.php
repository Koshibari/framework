<?php

# Core

class_alias(\Koshiba\Framework\Core\Router::class, 'Router');
class_alias(\Koshiba\Framework\Core\Http\Response::class, 'Response');
class_alias(\Koshiba\Framework\Core\Http\Request::class, 'Request');
class_alias(\Koshiba\Framework\Core\Http\Kernel::class, 'Kernel');
class_alias(\Koshiba\Framework\Core\Abstracts\View::class, 'View');
class_alias(\Koshiba\Framework\Core\App::class, 'App');

# Libs
class_alias(\Koshiba\Framework\Libs\Static\DataUtils::class, 'DataUtils');
class_alias(\Koshiba\Framework\Libs\Static\Helper::class, 'Helper');
class_alias(\Koshiba\Framework\Libs\Static\ConstHelper::class, 'Koshiba');

# Widgets
class_alias(\Koshiba\Framework\Widgets\Menu\Menu::class, 'MenuWidget');
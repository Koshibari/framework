<?php

# Core

class_alias(\Koshiba\Core\Router::class, 'Router');
class_alias(\Koshiba\Core\Http\Response::class, 'Response');
class_alias(\Koshiba\Core\Http\Request::class, 'Request');
class_alias(\Koshiba\Core\Http\Kernel::class, 'Kernel');
class_alias(\Koshiba\Core\Abstracts\View::class, 'View');
class_alias(\Koshiba\Core\App::class, 'App');

# Libs
class_alias(\Koshiba\Libs\Static\DataUtils::class, 'DataUtils');
class_alias(\Koshiba\Libs\Static\Helper::class, 'Helper');
class_alias(\Koshiba\Libs\Static\ConstHelper::class, 'Koshiba');

# Widgets
class_alias(\Koshiba\Widgets\Menu\Menu::class, 'MenuWidget');
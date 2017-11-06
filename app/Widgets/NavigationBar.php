<?php
namespace App\Widgets;

use App\Services\Navigation;
use App\Support\Widget\AbstractWidget;

class NavigationBar extends AbstractWidget
{
    public function getData(array $params = [])
    {
        $navigation = app(Navigation::class);
        //dd($navigation->getActiveTopNav());
        return [
            'navigation' => $navigation,
            'allNav' => $navigation->getAllNav(),
            'activeTopNav' => $navigation->getActiveTopNav(),
        ];
    }
}

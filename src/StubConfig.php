<?php
/**
 * StubConfig
 *
 * スタブにアクセスがあった際に使われるDIコンフィグ。
 */

use Aura\Di\Container;
use Aura\Di\ContainerConfig;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Relay\Middleware\ResponseSender;

class StubConfig extends ContainerConfig
{
    
    public function define(Container $di)
    {
        // 当ソフト固有のRouteオブジェクトをルータにインジェクト
        $di->setters['Aura\Router\RouterContainer']['setRouteFactory'] = $di->newFactory('tyam\radarx\Route');
        $di->params['tyam\radarx\Route'][0] = 'Web';
    }
    
    public function modify(Container $di)
    {
        $adr = $di->get('radar/adr:adr');

        // ミドルウェアの設定
        $adr->middle(new ResponseSender());
        $adr->middle(['Web\StubExecInput', 'carryResponse']);
        $adr->middle('Radar\Adr\Handler\RoutingHandler');
        $adr->middle('Radar\Adr\Handler\ActionHandler');

        // ルートは、すべてStubExecが実行されるように設定
        $adr->route('StubExec', '/', 'App\StubExec')->wildcard('x')
            ->allows(['GET', 'PUT', 'POST', 'DELETE', 'PATCH']);
    }
}
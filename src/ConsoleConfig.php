<?php
/**
 * ConsoleConfig
 *
 * 「コンソール」は、ユーザがスタブを管理する画面のこと。
 * このクラスはコンソールに特化したDIコンフィグ。
 * HTTPミドルウェアやradarのカスタマイズ、ルーティングの設定など。
 */

use Aura\Di\Container;
use Aura\Di\ContainerConfig;
use Relay\Middleware\ExceptionHandler;
use Relay\Middleware\ResponseSender;
use Relay\Middleware\SessionHeadersHandler;
use Relay\Middleware\JsonContentHandler;
use Zend\Diactoros\Response as Response;

class ConsoleConfig extends ContainerConfig
{
    public function define(Container $di)
    {
        // 当ソフト固有のRouteオブジェクトをルータにインジェクト
        $di->setters['Aura\Router\RouterContainer']['setRouteFactory'] = $di->newFactory('tyam\radarx\Route');
        $di->params['tyam\radarx\Route'][0] = 'Web';
        
        // RunDomainトレイトのレゾルバをインジェクト
        $di->setters['tyam\radarx\RunDomain']['setResolve'] = $di->lazyNew('Aura\Di\ResolutionHelper');
    }
    
    public function modify(Container $di)
    {
        $adr = $di->get('radar/adr:adr');

        // ミドルウェアの設定
        //---------------------------------------
        $adr->middle(new ResponseSender());
        $adr->middle(new SessionHeadersHandler());
        $adr->middle(new JsonContentHandler());
        $adr->middle(new ExceptionHandler(new Response()));
        $adr->middle('tyam\radarx\XMethodHandler');
        $adr->middle('Radar\Adr\Handler\RoutingHandler');
        $adr->middle('Radar\Adr\Handler\ActionHandler');

        $adr->input('Web\ConsoleInput');

        // ルートの設定
        //---------------------------------------
        $base = '/' . getEnv('USER_PATH');
        $adr->get(   'StubList',      $base,                  'Link\StubList');
        $adr->post(  'StubInit',      $base,                  'Link\StubInit');
        $adr->put(   'StubOrdering',  $base,                  'Link\StubOrdering');
        $adr->post(  'StubEntry',     $base.'/new',           'Link\StubEntry');
        $adr->get(   'StubRef',       $base.'/{stub}',        'Link\StubRef');
        $adr->put(   'StubUpdate',    $base.'/{stub}',        'Link\StubUpdate');
        $adr->delete('StubRemoval',   $base.'/{stub}',        'Link\StubRemoval');
        $adr->get(   'AccessRef',     $base.'/access',        'Link\AccessRef');
        $adr->get(   'StubAccessRef', $base.'/{stub}/access', 'Link\StubAccessRef');
    }
}
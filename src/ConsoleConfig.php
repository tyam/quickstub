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

        // fadocコンバータをサービス化
        $di->set('fadoc', $di->lazyNew('tyam\fadoc\Converter'));

        // radarx\Inputにfadocコンバータをインジェクト
        $di->params['tyam\radarx\Input'][0] = $di->lazyGet('fadoc');
        
        // RunDomainトレイトのレゾルバをインジェクト
        $di->setters['tyam\radarx\RunDomain']['setResolve'] = $di->lazyNew('Aura\Di\ResolutionHelper');

        // bambooのセットアップ
        $di->params['tyam\bamboo\Engine'][0] = [__DIR__.'/Web/templates'];

        // AbstractResponderのセットアップ
        $di->params['Web\AbstractResponder'][1] = $di->lazyGet('session');
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

        $adr->input('Web\Input');

        // ルートの設定
        //---------------------------------------
        $base = '/' . getEnv('USER_PATH');
        $adr->get(   'StubList',      $base,                  'Link\StubList');
        $adr->post(  'StubInit',      $base,                  'Link\StubInit');
        $adr->put(   'StubOrdering',  $base,                  'Link\StubOrdering');
        $adr->post(  'StubEntry',     $base.'/new',           'Link\StubEntry');
        $adr->get(   'StubRef',       $base.'/{stubId}',      'Link\StubRef');
        $adr->patch( 'StubUpdate',    $base.'/{stubId}',      'Link\StubUpdate');
        $adr->delete('StubRemoval',   $base.'/{stubId}',      'Link\StubRemoval');
        $adr->get(   'AccessRef',     $base.'/access',        'Link\AccessRef');
        $adr->get(   'StubAccessRef', $base.'/{stubId}/access',    'Link\StubAccessRef');
    }
}
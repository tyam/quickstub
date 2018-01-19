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
use tyam\radarx\CsrfTokenHandler;

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
        $adr->middle(new CsrfTokenHandler($di->get('session')));
        $adr->middle('tyam\radarx\XMethodHandler');
        $adr->middle('Radar\Adr\Handler\RoutingHandler');
        $adr->middle('Radar\Adr\Handler\ActionHandler');

        $adr->input('tyam\radarx\Input');

        // ルートの設定
        //---------------------------------------
        $base = '/' . getEnv('USER_PATH');
        $adr->get(   'StubList',      $base,                  'App\StubList');
        $adr->put(   'StubOrdering',  $base,                  'App\StubOrdering');
        $adr->post(  'StubEntry',     $base.'/new',           'App\StubEntry');
        $adr->get(   'AccessRef',     $base.'/accesses',        'App\AccessRef');
        $adr->get(   'StubAccessRef', $base.'/{stubId}/accesses',    'App\AccessStubRef');
        $adr->get(   'StubRef',       $base.'/{stubId}',      'App\StubRef');
        $adr->patch( 'StubUpdate',    $base.'/{stubId}',      'App\StubUpdate');
        $adr->delete('StubRemoval',   $base.'/{stubId}',      'App\StubRemoval');
    }
}
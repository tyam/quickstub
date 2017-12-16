<?php
/**
 * AbstractResponder
 *
 * 画面やJSONレンポンダの共通スーパークラス。
 * DIでテンプレートエンジンとセッションをインジェクトしている。
 */

namespace Web;

use tyam\bamboo\Engine;
use tyam\fadoc\Converter;
use tyam\bamboo\VariableProvider;

class AbstractResponder implements VariableProvider
{
    protected $bamboo;
    protected $session;
    protected $converter;

    public function __construct(Engine $bamboo, Session $session, Converter $converter)
    {
        $this->bamboo = $bamboo;
        $this->session = $session;
        $this->converter = $converter;

        $bamboo->setVariableProvider($this);
        $bamboo->loadFunctions();
    }

    public function provideVariables(string $template): array
    {
        return [];
    }
}
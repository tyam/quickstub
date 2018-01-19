<?php

namespace App;

use Domain\UserId;
use Domain\Stub;
use Domain\StubList;
use Domain\StubRepository;
use tyam\fadoc\Converter;
use tyam\radarx\PayloadFactory;

class StubOrdering
{
    private $stubRepo;
    private $converter;

    public function __construct(StubRepository $stubRepo, Converter $converter)
    {
        $this->stubRepo = $stubRepo;
        $this->converter = $converter;
    }

    public function __invoke($form, $payloadFactory)
    {
        \Logger::debug('stubOrdering');
        $userId = \Session::getCurrentUser();
        if (is_null($userId)) {
            return $payloadFactory->notAuthenticated();
        }
        $stubList = $this->stubRepo->searchByOwner($userId);

        $cd = $this->converter->objectize([$stubList, 'moveItem'], $form);
        if (! $cd()) {
            return $payloadFactory->notValid($stubList, $cd->describe());
        }

        list($stubId, $index) = $cd->get();
        $result = $stubList->moveItem($stubId, $index);
        if (! $result) {
            // 指定されたスタブIDがリスト内に無い、または指定された添字が範囲外。
            // どちらの場合も、操作の最中にリストが変更されたと考えられるので、
            // 再試行すべき。
            return $payloadFactory->failure($stubList, ['reason' => 'unspecified']);
        }

        $this->stubRepo->storeOrdering($stubList);
        return $payloadFactory->success($stubList, null);
    }
}
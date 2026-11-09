<?php
namespace humhub\modules\space\models {
    class Space {
        public $settings;
        public function isAdmin() { return false; }
        public function isMember() { return false; }
    }
}
namespace humhub\modules\space\controllers {
    class MembershipController { public $contentContainer; }
}
namespace yii\web { class ForbiddenHttpException extends \Exception {} }
namespace {
    class Yii { public static $app; }
    require dirname(__DIR__) . '/Policy.php';
    require dirname(__DIR__) . '/Events.php';
    Yii::$app = (object)['user' => new class {
        public $isGuest = false;
        public function isAdmin() { return false; }
    }];
    $space = new \humhub\modules\space\models\Space();
    $space->settings = new class {
        public $policy = 'admins';
        public function get($key) { return $this->policy; }
    };
    $controller = new \humhub\modules\space\controllers\MembershipController();
    $controller->contentContainer = $space;
    $checks = 0;
    foreach ([null, 'users', 'members', 'admins', 'invalid'] as $policy) {
        $space->settings->policy = $policy;
        foreach (['members-list', 'search', 'invite', 'revoke-membership'] as $action) {
            $event = (object)['sender' => $controller, 'action' => (object)['id' => $action]];
            $denied = false;
            try { \humhub\modules\selbstseinCommunity\Events::beforeAction($event); }
            catch (\yii\web\ForbiddenHttpException $e) { $denied = true; }
            $expected = $policy !== null && $policy !== 'users' && in_array($action, ['members-list', 'search'], true);
            if ($denied !== $expected) { throw new \RuntimeException('Route guard failure'); }
            $checks++;
        }
    }
    echo "$checks route guard checks passed\n";
}

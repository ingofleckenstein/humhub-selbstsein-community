<?php
namespace humhub\modules\space\models {
    class Space {
        public $settings;
        public bool $admin = false;
        public bool $member = false;
        public function isAdmin() { return $this->admin; }
        public function isMember() { return $this->member; }
    }
}
namespace {
    class Yii { public static $app; }
    require dirname(__DIR__) . '/Policy.php';
    $user = new class {
        public bool $isGuest = false;
        public bool $admin = false;
        public function isAdmin() { return $this->admin; }
    };
    Yii::$app = (object)['user' => $user];
    $space = new \humhub\modules\space\models\Space();
    $space->settings = new class {
        public $policy;
        public function get($key) { return $this->policy; }
    };
    $checks = 0;
    foreach ([null, 'users', 'members', 'admins', 'invalid'] as $policy) {
        foreach ([false, true] as $guest) {
            foreach ([false, true] as $globalAdmin) {
                foreach ([false, true] as $spaceAdmin) {
                    foreach ([false, true] as $member) {
                        $user->isGuest = $guest;
                        $user->admin = $globalAdmin;
                        $space->admin = $spaceAdmin;
                        $space->member = $member;
                        $space->settings->policy = $policy;
                        $expected = !$guest && ($globalAdmin || $spaceAdmin || $policy === 'users' || ($policy === 'members' && $member));
                        if (\humhub\modules\selbstseinCommunity\Policy::allowed($space) !== $expected) {
                            throw new \RuntimeException('Policy matrix failed');
                        }
                        $checks++;
                    }
                }
            }
        }
    }
    echo "$checks access checks passed\n";
}

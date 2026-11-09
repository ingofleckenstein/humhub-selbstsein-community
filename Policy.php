<?php
namespace humhub\modules\selbstseinCommunity;

use Yii;
use humhub\modules\space\models\Space;

class Policy
{
    public const OPTIONS = [
        'users' => 'Alle angemeldeten Personen',
        'members' => 'Nur Space-Mitglieder',
        'admins' => 'Nur Space-Admins und Community-Admins',
    ];

    public static function get(Space $space): ?string
    {
        return $space->settings->get('selbstseinMemberVisibility');
    }

    public static function admin(Space $space): bool
    {
        return !Yii::$app->user->isGuest && (Yii::$app->user->isAdmin() || $space->isAdmin());
    }

    public static function allowed(Space $space): bool
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        if (self::admin($space)) {
            return true;
        }
        return match (self::get($space)) {
            'users' => true,
            'members' => $space->isMember(),
            default => false,
        };
    }
}

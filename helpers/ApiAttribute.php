<?php
/**
 * ApiAttribute.php
 * @author Revin Roman
 * @link https://rmrevin.com
 */

namespace cookyii\helpers;

use cookyii\Facade as F;

/**
 * Class ApiAttribute
 * @package cookyii\helpers
 */
class ApiAttribute
{

    /**
     * @var array
     */
    public static $relativeFormats = [
        'short' => 'd MMM HH:mm',
        'long' => 'd MMM y',
    ];

    /**
     * @var array
     */
    public static $datetimeFormats = [
        'raw' => 'raw',
        'relative' => 'relative',
        'format' => 'd MMM HH:mm',
        'normal' => 'dd.MM.yyyy HH:mm',
    ];

    /**
     * @var array
     */
    public static $dateFormats = [
        'raw' => 'raw',
        'relative' => 'relative',
        'format' => 'd MMM',
        'normal' => 'dd.MM.yyyy',
    ];

    /**
     * @var array
     */
    public static $timeFormats = [
        'raw' => 'raw',
        'relative' => 'relative',
        'normal' => 'HH:mm',
    ];

    /**
     * @param array $fields
     * @param string $attribute
     * @param array|null $formats
     */
    public static function relativeFormat(array &$fields, $attribute, $formats = [])
    {
        $formats = empty($formats) && $formats !== null
            ? static::$relativeFormats
            : $formats;

        if (empty($formats)) {
            $fields[$attribute] = function (\yii\db\BaseActiveRecord $Model) use ($attribute) {
                return $Model->hasAttribute($attribute)
                    ? (string)$Model->getAttribute($attribute)
                    : null;
            };
        } else {
            $fields[$attribute] = function (\yii\db\BaseActiveRecord $Model) use ($attribute, $formats) {
                $value = $Model->hasAttribute($attribute)
                    ? (string)$Model->getAttribute($attribute)
                    : null;

                $value = empty($value)
                    ? time()
                    : F::Formatter()->asTimestamp($value);

                $is_milliseconds = mb_strlen($value, 'utf-8') === 13;
                $value = $is_milliseconds ? floor($value / 1000) : $value;

                $delta = time() - $value;

                $same_year = F::Formatter()->asDate($value, 'y') === F::Formatter()->asDate(time(), 'y');

                if (!$same_year) {
                    $result = F::Formatter()->asDatetime($value, $formats['long']);
                } elseif ($delta > 86400 && $delta <= (86400 * 7)) {
                    $result = F::Formatter()->asDatetime($value, $formats['short']);
                } elseif ($delta < 60) {
                    $result = \Yii::t('cookyii', 'just now');
                } else {
                    $result = F::Formatter()->asRelativeTime($value);
                }

                return $result;
            };
        }
    }

    /**
     * @param array $fields
     * @param string $attribute
     * @param array|null $formats
     */
    public static function datetimeFormat(array &$fields, $attribute, $formats = [])
    {
        $formats = empty($formats) && $formats !== null
            ? static::$datetimeFormats
            : $formats;

        if (empty($formats)) {
            $fields[$attribute] = function (\yii\db\BaseActiveRecord $Model) use ($attribute) {
                return $Model->hasAttribute($attribute)
                    ? (string)$Model->getAttribute($attribute)
                    : null;
            };
        } else {
            $fields[$attribute] = function (\yii\db\BaseActiveRecord $Model) use ($attribute, $formats) {
                $result = [];

                $value = $Model->hasAttribute($attribute)
                    ? (string)$Model->getAttribute($attribute)
                    : null;

                $value = empty($value)
                    ? time()
                    : F::Formatter()->asTimestamp($value);

                $is_milliseconds = mb_strlen($value, 'utf-8') === 13;
                $value = $is_milliseconds ? floor($value / 1000) : $value;

                foreach ($formats as $key => $format) {
                    switch ($format) {
                        case 'raw':
                            $result[$key] = empty($value) ? null : $value;
                            break;
                        case 'relative':
                            $result[$key] = empty($value)
                                ? \Yii::t('yii', '(not set)')
                                : F::Formatter()->asRelativeTime($value);
                            break;
                        default:
                            $result[$key] = empty($value)
                                ? \Yii::t('yii', '(not set)')
                                : F::Formatter()->asDatetime($value, $format);
                            break;
                    }
                }

                return $result;
            };
        }
    }

    /**
     * @param array $fields
     * @param string $attribute
     */
    public static function dateFormat(array &$fields, $attribute)
    {
        static::datetimeFormat($fields, $attribute, static::$dateFormats);
    }

    /**
     * @param array $fields
     * @param string $attribute
     */
    public static function timeFormat(array &$fields, $attribute)
    {
        static::datetimeFormat($fields, $attribute, static::$timeFormats);
    }
}

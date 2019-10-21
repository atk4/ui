
<?php
namespace atk4\ui;
class Locale
{
    public function __construct()
    {
        throw new Exception('Class Locale is needed only for locating the default translations');
    }
    /**
     * Get absolute Path of default translations.
     *
     * @return string
     */
    public static function getPath()
    {
        return implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'locale']).DIRECTORY_SEPARATOR;
    }
}

<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Data\Model;

require_once __DIR__ . '/../init-autoloader.php';

$sqliteFile = __DIR__ . '/db.sqlite';
if (file_exists($sqliteFile)) {
    unlink($sqliteFile);
}

$persistence = new \Atk4\Data\Persistence\Sql('sqlite:' . $sqliteFile);

$model = new Model($persistence, ['table' => 'client']);
$model->addField('xxx9_name', ['type' => 'string']);
$model->addField('xxx_addresses', ['type' => 'text']);
$model->addField('xxx_accounts', ['type' => 'text']);
(new \Atk4\Schema\Migration($model))->dropIfExists()->create();
$model->import([
    ['id' => 1, 'xxx9_name' => 'John', 'xxx_addresses' => null, 'xxx_accounts' => null],
    ['id' => 2, 'xxx9_name' => 'Jane', 'xxx_addresses' => null, 'xxx_accounts' => null],
]);

$model = new Model($persistence, ['table' => 'country']);
$model->addField('xxx_iso', ['type' => 'string']); // should be CHAR(2) NOT NULL
$model->addField('yyy_name', ['type' => 'string']);
$model->addField('yyy_nicename', ['type' => 'string']);
$model->addField('xxx_iso3', ['type' => 'string']); // should be CHAR(3) NOT NULL
$model->addField('xxx_numcode', ['type' => 'smallint']);
$model->addField('xxx_phonecode', ['type' => 'integer']);
(new \Atk4\Schema\Migration($model))->dropIfExists()->create();
$model->import([
    ['id' => 1, 'xxx_iso' => 'AF', 'yyy_name' => 'AFGHANISTAN', 'yyy_nicename' => 'Afghanistan', 'xxx_iso3' => 'AFG', 'xxx_numcode' => 4, 'xxx_phonecode' => 93],
    ['id' => 2, 'xxx_iso' => 'AL', 'yyy_name' => 'ALBANIA', 'yyy_nicename' => 'Albania', 'xxx_iso3' => 'ALB', 'xxx_numcode' => 8, 'xxx_phonecode' => 355],
    ['id' => 3, 'xxx_iso' => 'DZ', 'yyy_name' => 'ALGERIA', 'yyy_nicename' => 'Algeria', 'xxx_iso3' => 'DZA', 'xxx_numcode' => 12, 'xxx_phonecode' => 213],
    ['id' => 4, 'xxx_iso' => 'AS', 'yyy_name' => 'AMERICAN SAMOA', 'yyy_nicename' => 'American Samoa', 'xxx_iso3' => 'ASM', 'xxx_numcode' => 16, 'xxx_phonecode' => 1684],
    ['id' => 5, 'xxx_iso' => 'AD', 'yyy_name' => 'ANDORRA', 'yyy_nicename' => 'Andorra', 'xxx_iso3' => 'AND', 'xxx_numcode' => 20, 'xxx_phonecode' => 376],
    ['id' => 6, 'xxx_iso' => 'AO', 'yyy_name' => 'ANGOLA', 'yyy_nicename' => 'Angola', 'xxx_iso3' => 'AGO', 'xxx_numcode' => 24, 'xxx_phonecode' => 244],
    ['id' => 7, 'xxx_iso' => 'AI', 'yyy_name' => 'ANGUILLA', 'yyy_nicename' => 'Anguilla', 'xxx_iso3' => 'AIA', 'xxx_numcode' => 660, 'xxx_phonecode' => 1264],
    ['id' => 8, 'xxx_iso' => 'AQ', 'yyy_name' => 'ANTARCTICA', 'yyy_nicename' => 'Antarctica', 'xxx_iso3' => '', 'xxx_numcode' => '', 'xxx_phonecode' => 0],
    ['id' => 9, 'xxx_iso' => 'AG', 'yyy_name' => 'ANTIGUA AND BARBUDA', 'yyy_nicename' => 'Antigua and Barbuda', 'xxx_iso3' => 'ATG', 'xxx_numcode' => 28, 'xxx_phonecode' => 1268],
    ['id' => 10, 'xxx_iso' => 'AR', 'yyy_name' => 'ARGENTINA', 'yyy_nicename' => 'Argentina', 'xxx_iso3' => 'ARG', 'xxx_numcode' => 32, 'xxx_phonecode' => 54],
    ['id' => 11, 'xxx_iso' => 'AM', 'yyy_name' => 'ARMENIA', 'yyy_nicename' => 'Armenia', 'xxx_iso3' => 'ARM', 'xxx_numcode' => 51, 'xxx_phonecode' => 374],
    ['id' => 12, 'xxx_iso' => 'AW', 'yyy_name' => 'ARUBA', 'yyy_nicename' => 'Aruba', 'xxx_iso3' => 'ABW', 'xxx_numcode' => 533, 'xxx_phonecode' => 297],
    ['id' => 13, 'xxx_iso' => 'AU', 'yyy_name' => 'AUSTRALIA', 'yyy_nicename' => 'Australia', 'xxx_iso3' => 'AUS', 'xxx_numcode' => 36, 'xxx_phonecode' => 61],
    ['id' => 14, 'xxx_iso' => 'AT', 'yyy_name' => 'AUSTRIA', 'yyy_nicename' => 'Austria', 'xxx_iso3' => 'AUT', 'xxx_numcode' => 40, 'xxx_phonecode' => 43],
    ['id' => 15, 'xxx_iso' => 'AZ', 'yyy_name' => 'AZERBAIJAN', 'yyy_nicename' => 'Azerbaijan', 'xxx_iso3' => 'AZE', 'xxx_numcode' => 31, 'xxx_phonecode' => 994],
    ['id' => 16, 'xxx_iso' => 'BS', 'yyy_name' => 'BAHAMAS', 'yyy_nicename' => 'Bahamas', 'xxx_iso3' => 'BHS', 'xxx_numcode' => 44, 'xxx_phonecode' => 1242],
    ['id' => 17, 'xxx_iso' => 'BH', 'yyy_name' => 'BAHRAIN', 'yyy_nicename' => 'Bahrain', 'xxx_iso3' => 'BHR', 'xxx_numcode' => 48, 'xxx_phonecode' => 973],
    ['id' => 18, 'xxx_iso' => 'BD', 'yyy_name' => 'BANGLADESH', 'yyy_nicename' => 'Bangladesh', 'xxx_iso3' => 'BGD', 'xxx_numcode' => 50, 'xxx_phonecode' => 880],
    ['id' => 19, 'xxx_iso' => 'BB', 'yyy_name' => 'BARBADOS', 'yyy_nicename' => 'Barbados', 'xxx_iso3' => 'BRB', 'xxx_numcode' => 52, 'xxx_phonecode' => 1246],
    ['id' => 20, 'xxx_iso' => 'BY', 'yyy_name' => 'BELARUS', 'yyy_nicename' => 'Belarus', 'xxx_iso3' => 'BLR', 'xxx_numcode' => 112, 'xxx_phonecode' => 375],
    ['id' => 21, 'xxx_iso' => 'BE', 'yyy_name' => 'BELGIUM', 'yyy_nicename' => 'Belgium', 'xxx_iso3' => 'BEL', 'xxx_numcode' => 56, 'xxx_phonecode' => 32],
    ['id' => 22, 'xxx_iso' => 'BZ', 'yyy_name' => 'BELIZE', 'yyy_nicename' => 'Belize', 'xxx_iso3' => 'BLZ', 'xxx_numcode' => 84, 'xxx_phonecode' => 501],
    ['id' => 23, 'xxx_iso' => 'BJ', 'yyy_name' => 'BENIN', 'yyy_nicename' => 'Benin', 'xxx_iso3' => 'BEN', 'xxx_numcode' => 204, 'xxx_phonecode' => 229],
    ['id' => 24, 'xxx_iso' => 'BM', 'yyy_name' => 'BERMUDA', 'yyy_nicename' => 'Bermuda', 'xxx_iso3' => 'BMU', 'xxx_numcode' => 60, 'xxx_phonecode' => 1441],
    ['id' => 25, 'xxx_iso' => 'BT', 'yyy_name' => 'BHUTAN', 'yyy_nicename' => 'Bhutan', 'xxx_iso3' => 'BTN', 'xxx_numcode' => 64, 'xxx_phonecode' => 975],
    ['id' => 26, 'xxx_iso' => 'BO', 'yyy_name' => 'BOLIVIA', 'yyy_nicename' => 'Bolivia', 'xxx_iso3' => 'BOL', 'xxx_numcode' => 68, 'xxx_phonecode' => 591],
    ['id' => 27, 'xxx_iso' => 'BA', 'yyy_name' => 'BOSNIA AND HERZEGOVINA', 'yyy_nicename' => 'Bosnia and Herzegovina', 'xxx_iso3' => 'BIH', 'xxx_numcode' => 70, 'xxx_phonecode' => 387],
    ['id' => 28, 'xxx_iso' => 'BW', 'yyy_name' => 'BOTSWANA', 'yyy_nicename' => 'Botswana', 'xxx_iso3' => 'BWA', 'xxx_numcode' => 72, 'xxx_phonecode' => 267],
    ['id' => 29, 'xxx_iso' => 'BV', 'yyy_name' => 'BOUVET ISLAND', 'yyy_nicename' => 'Bouvet Island', 'xxx_iso3' => '', 'xxx_numcode' => '', 'xxx_phonecode' => 0],
    ['id' => 30, 'xxx_iso' => 'BR', 'yyy_name' => 'BRAZIL', 'yyy_nicename' => 'Brazil', 'xxx_iso3' => 'BRA', 'xxx_numcode' => 76, 'xxx_phonecode' => 55],
    ['id' => 31, 'xxx_iso' => 'IO', 'yyy_name' => 'BRITISH INDIAN OCEAN TERRITORY', 'yyy_nicename' => 'British Indian Ocean Territory', 'xxx_iso3' => '', 'xxx_numcode' => '', 'xxx_phonecode' => 246],
    ['id' => 32, 'xxx_iso' => 'BN', 'yyy_name' => 'BRUNEI DARUSSALAM', 'yyy_nicename' => 'Brunei Darussalam', 'xxx_iso3' => 'BRN', 'xxx_numcode' => 96, 'xxx_phonecode' => 673],
    ['id' => 33, 'xxx_iso' => 'BG', 'yyy_name' => 'BULGARIA', 'yyy_nicename' => 'Bulgaria', 'xxx_iso3' => 'BGR', 'xxx_numcode' => 100, 'xxx_phonecode' => 359],
    ['id' => 34, 'xxx_iso' => 'BF', 'yyy_name' => 'BURKINA FASO', 'yyy_nicename' => 'Burkina Faso', 'xxx_iso3' => 'BFA', 'xxx_numcode' => 854, 'xxx_phonecode' => 226],
    ['id' => 35, 'xxx_iso' => 'BI', 'yyy_name' => 'BURUNDI', 'yyy_nicename' => 'Burundi', 'xxx_iso3' => 'BDI', 'xxx_numcode' => 108, 'xxx_phonecode' => 257],
    ['id' => 36, 'xxx_iso' => 'KH', 'yyy_name' => 'CAMBODIA', 'yyy_nicename' => 'Cambodia', 'xxx_iso3' => 'KHM', 'xxx_numcode' => 116, 'xxx_phonecode' => 855],
    ['id' => 37, 'xxx_iso' => 'CM', 'yyy_name' => 'CAMEROON', 'yyy_nicename' => 'Cameroon', 'xxx_iso3' => 'CMR', 'xxx_numcode' => 120, 'xxx_phonecode' => 237],
    ['id' => 38, 'xxx_iso' => 'CA', 'yyy_name' => 'CANADA', 'yyy_nicename' => 'Canada', 'xxx_iso3' => 'CAN', 'xxx_numcode' => 124, 'xxx_phonecode' => 1],
    ['id' => 39, 'xxx_iso' => 'CV', 'yyy_name' => 'CAPE VERDE', 'yyy_nicename' => 'Cape Verde', 'xxx_iso3' => 'CPV', 'xxx_numcode' => 132, 'xxx_phonecode' => 238],
    ['id' => 40, 'xxx_iso' => 'KY', 'yyy_name' => 'CAYMAN ISLANDS', 'yyy_nicename' => 'Cayman Islands', 'xxx_iso3' => 'CYM', 'xxx_numcode' => 136, 'xxx_phonecode' => 1345],
    ['id' => 41, 'xxx_iso' => 'CF', 'yyy_name' => 'CENTRAL AFRICAN REPUBLIC', 'yyy_nicename' => 'Central African Republic', 'xxx_iso3' => 'CAF', 'xxx_numcode' => 140, 'xxx_phonecode' => 236],
    ['id' => 42, 'xxx_iso' => 'TD', 'yyy_name' => 'CHAD', 'yyy_nicename' => 'Chad', 'xxx_iso3' => 'TCD', 'xxx_numcode' => 148, 'xxx_phonecode' => 235],
    ['id' => 43, 'xxx_iso' => 'CL', 'yyy_name' => 'CHILE', 'yyy_nicename' => 'Chile', 'xxx_iso3' => 'CHL', 'xxx_numcode' => 152, 'xxx_phonecode' => 56],
    ['id' => 44, 'xxx_iso' => 'CN', 'yyy_name' => 'CHINA', 'yyy_nicename' => 'China', 'xxx_iso3' => 'CHN', 'xxx_numcode' => 156, 'xxx_phonecode' => 86],
    ['id' => 45, 'xxx_iso' => 'CX', 'yyy_name' => 'CHRISTMAS ISLAND', 'yyy_nicename' => 'Christmas Island', 'xxx_iso3' => '', 'xxx_numcode' => '', 'xxx_phonecode' => 61],
    ['id' => 46, 'xxx_iso' => 'CC', 'yyy_name' => 'COCOS (KEELING) ISLANDS', 'yyy_nicename' => 'Cocos (Keeling) Islands', 'xxx_iso3' => '', 'xxx_numcode' => '', 'xxx_phonecode' => 672],
    ['id' => 47, 'xxx_iso' => 'CO', 'yyy_name' => 'COLOMBIA', 'yyy_nicename' => 'Colombia', 'xxx_iso3' => 'COL', 'xxx_numcode' => 170, 'xxx_phonecode' => 57],
    ['id' => 48, 'xxx_iso' => 'KM', 'yyy_name' => 'COMOROS', 'yyy_nicename' => 'Comoros', 'xxx_iso3' => 'COM', 'xxx_numcode' => 174, 'xxx_phonecode' => 269],
    ['id' => 49, 'xxx_iso' => 'CG', 'yyy_name' => 'CONGO', 'yyy_nicename' => 'Congo', 'xxx_iso3' => 'COG', 'xxx_numcode' => 178, 'xxx_phonecode' => 242],
    ['id' => 50, 'xxx_iso' => 'CD', 'yyy_name' => 'CONGO, THE DEMOCRATIC REPUBLIC OF THE', 'yyy_nicename' => 'Congo, the Democratic Republic of the', 'xxx_iso3' => 'COD', 'xxx_numcode' => 180, 'xxx_phonecode' => 243],
    ['id' => 51, 'xxx_iso' => 'CK', 'yyy_name' => 'COOK ISLANDS', 'yyy_nicename' => 'Cook Islands', 'xxx_iso3' => 'COK', 'xxx_numcode' => 184, 'xxx_phonecode' => 682],
    ['id' => 52, 'xxx_iso' => 'CR', 'yyy_name' => 'COSTA RICA', 'yyy_nicename' => 'Costa Rica', 'xxx_iso3' => 'CRI', 'xxx_numcode' => 188, 'xxx_phonecode' => 506],
    ['id' => 53, 'xxx_iso' => 'CI', 'yyy_name' => 'COTE D\'IVOIRE', 'yyy_nicename' => 'Cote D\'Ivoire', 'xxx_iso3' => 'CIV', 'xxx_numcode' => 384, 'xxx_phonecode' => 225],
    ['id' => 54, 'xxx_iso' => 'HR', 'yyy_name' => 'CROATIA', 'yyy_nicename' => 'Croatia', 'xxx_iso3' => 'HRV', 'xxx_numcode' => 191, 'xxx_phonecode' => 385],
    ['id' => 55, 'xxx_iso' => 'CU', 'yyy_name' => 'CUBA', 'yyy_nicename' => 'Cuba', 'xxx_iso3' => 'CUB', 'xxx_numcode' => 192, 'xxx_phonecode' => 53],
    ['id' => 56, 'xxx_iso' => 'CY', 'yyy_name' => 'CYPRUS', 'yyy_nicename' => 'Cyprus', 'xxx_iso3' => 'CYP', 'xxx_numcode' => 196, 'xxx_phonecode' => 357],
    ['id' => 57, 'xxx_iso' => 'CZ', 'yyy_name' => 'CZECH REPUBLIC', 'yyy_nicename' => 'Czech Republic', 'xxx_iso3' => 'CZE', 'xxx_numcode' => 203, 'xxx_phonecode' => 420],
    ['id' => 58, 'xxx_iso' => 'DK', 'yyy_name' => 'DENMARK', 'yyy_nicename' => 'Denmark', 'xxx_iso3' => 'DNK', 'xxx_numcode' => 208, 'xxx_phonecode' => 45],
    ['id' => 59, 'xxx_iso' => 'DJ', 'yyy_name' => 'DJIBOUTI', 'yyy_nicename' => 'Djibouti', 'xxx_iso3' => 'DJI', 'xxx_numcode' => 262, 'xxx_phonecode' => 253],
    ['id' => 60, 'xxx_iso' => 'DM', 'yyy_name' => 'DOMINICA', 'yyy_nicename' => 'Dominica', 'xxx_iso3' => 'DMA', 'xxx_numcode' => 212, 'xxx_phonecode' => 1767],
    ['id' => 61, 'xxx_iso' => 'DO', 'yyy_name' => 'DOMINICAN REPUBLIC', 'yyy_nicename' => 'Dominican Republic', 'xxx_iso3' => 'DOM', 'xxx_numcode' => 214, 'xxx_phonecode' => 1809],
    ['id' => 62, 'xxx_iso' => 'EC', 'yyy_name' => 'ECUADOR', 'yyy_nicename' => 'Ecuador', 'xxx_iso3' => 'ECU', 'xxx_numcode' => 218, 'xxx_phonecode' => 593],
    ['id' => 63, 'xxx_iso' => 'EG', 'yyy_name' => 'EGYPT', 'yyy_nicename' => 'Egypt', 'xxx_iso3' => 'EGY', 'xxx_numcode' => 818, 'xxx_phonecode' => 20],
    ['id' => 64, 'xxx_iso' => 'SV', 'yyy_name' => 'EL SALVADOR', 'yyy_nicename' => 'El Salvador', 'xxx_iso3' => 'SLV', 'xxx_numcode' => 222, 'xxx_phonecode' => 503],
    ['id' => 65, 'xxx_iso' => 'GQ', 'yyy_name' => 'EQUATORIAL GUINEA', 'yyy_nicename' => 'Equatorial Guinea', 'xxx_iso3' => 'GNQ', 'xxx_numcode' => 226, 'xxx_phonecode' => 240],
    ['id' => 66, 'xxx_iso' => 'ER', 'yyy_name' => 'ERITREA', 'yyy_nicename' => 'Eritrea', 'xxx_iso3' => 'ERI', 'xxx_numcode' => 232, 'xxx_phonecode' => 291],
    ['id' => 67, 'xxx_iso' => 'EE', 'yyy_name' => 'ESTONIA', 'yyy_nicename' => 'Estonia', 'xxx_iso3' => 'EST', 'xxx_numcode' => 233, 'xxx_phonecode' => 372],
    ['id' => 68, 'xxx_iso' => 'ET', 'yyy_name' => 'ETHIOPIA', 'yyy_nicename' => 'Ethiopia', 'xxx_iso3' => 'ETH', 'xxx_numcode' => 231, 'xxx_phonecode' => 251],
    ['id' => 69, 'xxx_iso' => 'FK', 'yyy_name' => 'FALKLAND ISLANDS (MALVINAS)', 'yyy_nicename' => 'Falkland Islands (Malvinas)', 'xxx_iso3' => 'FLK', 'xxx_numcode' => 238, 'xxx_phonecode' => 500],
    ['id' => 70, 'xxx_iso' => 'FO', 'yyy_name' => 'FAROE ISLANDS', 'yyy_nicename' => 'Faroe Islands', 'xxx_iso3' => 'FRO', 'xxx_numcode' => 234, 'xxx_phonecode' => 298],
    ['id' => 71, 'xxx_iso' => 'FJ', 'yyy_name' => 'FIJI', 'yyy_nicename' => 'Fiji', 'xxx_iso3' => 'FJI', 'xxx_numcode' => 242, 'xxx_phonecode' => 679],
    ['id' => 72, 'xxx_iso' => 'FI', 'yyy_name' => 'FINLAND', 'yyy_nicename' => 'Finland', 'xxx_iso3' => 'FIN', 'xxx_numcode' => 246, 'xxx_phonecode' => 358],
    ['id' => 73, 'xxx_iso' => 'FR', 'yyy_name' => 'FRANCE', 'yyy_nicename' => 'France', 'xxx_iso3' => 'FRA', 'xxx_numcode' => 250, 'xxx_phonecode' => 33],
    ['id' => 74, 'xxx_iso' => 'GF', 'yyy_name' => 'FRENCH GUIANA', 'yyy_nicename' => 'French Guiana', 'xxx_iso3' => 'GUF', 'xxx_numcode' => 254, 'xxx_phonecode' => 594],
    ['id' => 75, 'xxx_iso' => 'PF', 'yyy_name' => 'FRENCH POLYNESIA', 'yyy_nicename' => 'French Polynesia', 'xxx_iso3' => 'PYF', 'xxx_numcode' => 258, 'xxx_phonecode' => 689],
    ['id' => 76, 'xxx_iso' => 'TF', 'yyy_name' => 'FRENCH SOUTHERN TERRITORIES', 'yyy_nicename' => 'French Southern Territories', 'xxx_iso3' => '', 'xxx_numcode' => '', 'xxx_phonecode' => 0],
    ['id' => 77, 'xxx_iso' => 'GA', 'yyy_name' => 'GABON', 'yyy_nicename' => 'Gabon', 'xxx_iso3' => 'GAB', 'xxx_numcode' => 266, 'xxx_phonecode' => 241],
    ['id' => 78, 'xxx_iso' => 'GM', 'yyy_name' => 'GAMBIA', 'yyy_nicename' => 'Gambia', 'xxx_iso3' => 'GMB', 'xxx_numcode' => 270, 'xxx_phonecode' => 220],
    ['id' => 79, 'xxx_iso' => 'GE', 'yyy_name' => 'GEORGIA', 'yyy_nicename' => 'Georgia', 'xxx_iso3' => 'GEO', 'xxx_numcode' => 268, 'xxx_phonecode' => 995],
    ['id' => 80, 'xxx_iso' => 'DE', 'yyy_name' => 'GERMANY', 'yyy_nicename' => 'Germany', 'xxx_iso3' => 'DEU', 'xxx_numcode' => 276, 'xxx_phonecode' => 49],
    ['id' => 81, 'xxx_iso' => 'GH', 'yyy_name' => 'GHANA', 'yyy_nicename' => 'Ghana', 'xxx_iso3' => 'GHA', 'xxx_numcode' => 288, 'xxx_phonecode' => 233],
    ['id' => 82, 'xxx_iso' => 'GI', 'yyy_name' => 'GIBRALTAR', 'yyy_nicename' => 'Gibraltar', 'xxx_iso3' => 'GIB', 'xxx_numcode' => 292, 'xxx_phonecode' => 350],
    ['id' => 83, 'xxx_iso' => 'GR', 'yyy_name' => 'GREECE', 'yyy_nicename' => 'Greece', 'xxx_iso3' => 'GRC', 'xxx_numcode' => 300, 'xxx_phonecode' => 30],
    ['id' => 84, 'xxx_iso' => 'GL', 'yyy_name' => 'GREENLAND', 'yyy_nicename' => 'Greenland', 'xxx_iso3' => 'GRL', 'xxx_numcode' => 304, 'xxx_phonecode' => 299],
    ['id' => 85, 'xxx_iso' => 'GD', 'yyy_name' => 'GRENADA', 'yyy_nicename' => 'Grenada', 'xxx_iso3' => 'GRD', 'xxx_numcode' => 308, 'xxx_phonecode' => 1473],
    ['id' => 86, 'xxx_iso' => 'GP', 'yyy_name' => 'GUADELOUPE', 'yyy_nicename' => 'Guadeloupe', 'xxx_iso3' => 'GLP', 'xxx_numcode' => 312, 'xxx_phonecode' => 590],
    ['id' => 87, 'xxx_iso' => 'GU', 'yyy_name' => 'GUAM', 'yyy_nicename' => 'Guam', 'xxx_iso3' => 'GUM', 'xxx_numcode' => 316, 'xxx_phonecode' => 1671],
    ['id' => 88, 'xxx_iso' => 'GT', 'yyy_name' => 'GUATEMALA', 'yyy_nicename' => 'Guatemala', 'xxx_iso3' => 'GTM', 'xxx_numcode' => 320, 'xxx_phonecode' => 502],
    ['id' => 89, 'xxx_iso' => 'GN', 'yyy_name' => 'GUINEA', 'yyy_nicename' => 'Guinea', 'xxx_iso3' => 'GIN', 'xxx_numcode' => 324, 'xxx_phonecode' => 224],
    ['id' => 90, 'xxx_iso' => 'GW', 'yyy_name' => 'GUINEA-BISSAU', 'yyy_nicename' => 'Guinea-Bissau', 'xxx_iso3' => 'GNB', 'xxx_numcode' => 624, 'xxx_phonecode' => 245],
    ['id' => 91, 'xxx_iso' => 'GY', 'yyy_name' => 'GUYANA', 'yyy_nicename' => 'Guyana', 'xxx_iso3' => 'GUY', 'xxx_numcode' => 328, 'xxx_phonecode' => 592],
    ['id' => 92, 'xxx_iso' => 'HT', 'yyy_name' => 'HAITI', 'yyy_nicename' => 'Haiti', 'xxx_iso3' => 'HTI', 'xxx_numcode' => 332, 'xxx_phonecode' => 509],
    ['id' => 93, 'xxx_iso' => 'HM', 'yyy_name' => 'HEARD ISLAND AND MCDONALD ISLANDS', 'yyy_nicename' => 'Heard Island and Mcdonald Islands', 'xxx_iso3' => '', 'xxx_numcode' => '', 'xxx_phonecode' => 0],
    ['id' => 94, 'xxx_iso' => 'VA', 'yyy_name' => 'HOLY SEE (VATICAN CITY STATE)', 'yyy_nicename' => 'Holy See (Vatican City State)', 'xxx_iso3' => 'VAT', 'xxx_numcode' => 336, 'xxx_phonecode' => 39],
    ['id' => 95, 'xxx_iso' => 'HN', 'yyy_name' => 'HONDURAS', 'yyy_nicename' => 'Honduras', 'xxx_iso3' => 'HND', 'xxx_numcode' => 340, 'xxx_phonecode' => 504],
    ['id' => 96, 'xxx_iso' => 'HK', 'yyy_name' => 'HONG KONG', 'yyy_nicename' => 'Hong Kong', 'xxx_iso3' => 'HKG', 'xxx_numcode' => 344, 'xxx_phonecode' => 852],
    ['id' => 97, 'xxx_iso' => 'HU', 'yyy_name' => 'HUNGARY', 'yyy_nicename' => 'Hungary', 'xxx_iso3' => 'HUN', 'xxx_numcode' => 348, 'xxx_phonecode' => 36],
    ['id' => 98, 'xxx_iso' => 'IS', 'yyy_name' => 'ICELAND', 'yyy_nicename' => 'Iceland', 'xxx_iso3' => 'ISL', 'xxx_numcode' => 352, 'xxx_phonecode' => 354],
    ['id' => 99, 'xxx_iso' => 'IN', 'yyy_name' => 'INDIA', 'yyy_nicename' => 'India', 'xxx_iso3' => 'IND', 'xxx_numcode' => 356, 'xxx_phonecode' => 91],
    ['id' => 100, 'xxx_iso' => 'ID', 'yyy_name' => 'INDONESIA', 'yyy_nicename' => 'Indonesia', 'xxx_iso3' => 'IDN', 'xxx_numcode' => 360, 'xxx_phonecode' => 62],
    ['id' => 101, 'xxx_iso' => 'IR', 'yyy_name' => 'IRAN, ISLAMIC REPUBLIC OF', 'yyy_nicename' => 'Iran, Islamic Republic of', 'xxx_iso3' => 'IRN', 'xxx_numcode' => 364, 'xxx_phonecode' => 98],
    ['id' => 102, 'xxx_iso' => 'IQ', 'yyy_name' => 'IRAQ', 'yyy_nicename' => 'Iraq', 'xxx_iso3' => 'IRQ', 'xxx_numcode' => 368, 'xxx_phonecode' => 964],
    ['id' => 103, 'xxx_iso' => 'IE', 'yyy_name' => 'IRELAND', 'yyy_nicename' => 'Ireland', 'xxx_iso3' => 'IRL', 'xxx_numcode' => 372, 'xxx_phonecode' => 353],
    ['id' => 104, 'xxx_iso' => 'IL', 'yyy_name' => 'ISRAEL', 'yyy_nicename' => 'Israel', 'xxx_iso3' => 'ISR', 'xxx_numcode' => 376, 'xxx_phonecode' => 972],
    ['id' => 105, 'xxx_iso' => 'IT', 'yyy_name' => 'ITALY', 'yyy_nicename' => 'Italy', 'xxx_iso3' => 'ITA', 'xxx_numcode' => 380, 'xxx_phonecode' => 39],
    ['id' => 106, 'xxx_iso' => 'JM', 'yyy_name' => 'JAMAICA', 'yyy_nicename' => 'Jamaica', 'xxx_iso3' => 'JAM', 'xxx_numcode' => 388, 'xxx_phonecode' => 1876],
    ['id' => 107, 'xxx_iso' => 'JP', 'yyy_name' => 'JAPAN', 'yyy_nicename' => 'Japan', 'xxx_iso3' => 'JPN', 'xxx_numcode' => 392, 'xxx_phonecode' => 81],
    ['id' => 108, 'xxx_iso' => 'JO', 'yyy_name' => 'JORDAN', 'yyy_nicename' => 'Jordan', 'xxx_iso3' => 'JOR', 'xxx_numcode' => 400, 'xxx_phonecode' => 962],
    ['id' => 109, 'xxx_iso' => 'KZ', 'yyy_name' => 'KAZAKHSTAN', 'yyy_nicename' => 'Kazakhstan', 'xxx_iso3' => 'KAZ', 'xxx_numcode' => 398, 'xxx_phonecode' => 7],
    ['id' => 110, 'xxx_iso' => 'KE', 'yyy_name' => 'KENYA', 'yyy_nicename' => 'Kenya', 'xxx_iso3' => 'KEN', 'xxx_numcode' => 404, 'xxx_phonecode' => 254],
    ['id' => 111, 'xxx_iso' => 'KI', 'yyy_name' => 'KIRIBATI', 'yyy_nicename' => 'Kiribati', 'xxx_iso3' => 'KIR', 'xxx_numcode' => 296, 'xxx_phonecode' => 686],
    ['id' => 112, 'xxx_iso' => 'KP', 'yyy_name' => 'KOREA, DEMOCRATIC PEOPLE\'S REPUBLIC OF', 'yyy_nicename' => 'Korea, Democratic People\'s Republic of', 'xxx_iso3' => 'PRK', 'xxx_numcode' => 408, 'xxx_phonecode' => 850],
    ['id' => 113, 'xxx_iso' => 'KR', 'yyy_name' => 'KOREA, REPUBLIC OF', 'yyy_nicename' => 'Korea, Republic of', 'xxx_iso3' => 'KOR', 'xxx_numcode' => 410, 'xxx_phonecode' => 82],
    ['id' => 114, 'xxx_iso' => 'KW', 'yyy_name' => 'KUWAIT', 'yyy_nicename' => 'Kuwait', 'xxx_iso3' => 'KWT', 'xxx_numcode' => 414, 'xxx_phonecode' => 965],
    ['id' => 115, 'xxx_iso' => 'KG', 'yyy_name' => 'KYRGYZSTAN', 'yyy_nicename' => 'Kyrgyzstan', 'xxx_iso3' => 'KGZ', 'xxx_numcode' => 417, 'xxx_phonecode' => 996],
    ['id' => 116, 'xxx_iso' => 'LA', 'yyy_name' => 'LAO PEOPLE\'S DEMOCRATIC REPUBLIC', 'yyy_nicename' => 'Lao People\'s Democratic Republic', 'xxx_iso3' => 'LAO', 'xxx_numcode' => 418, 'xxx_phonecode' => 856],
    ['id' => 117, 'xxx_iso' => 'LV', 'yyy_name' => 'LATVIA', 'yyy_nicename' => 'Latvia', 'xxx_iso3' => 'LVA', 'xxx_numcode' => 428, 'xxx_phonecode' => 371],
    ['id' => 118, 'xxx_iso' => 'LB', 'yyy_name' => 'LEBANON', 'yyy_nicename' => 'Lebanon', 'xxx_iso3' => 'LBN', 'xxx_numcode' => 422, 'xxx_phonecode' => 961],
    ['id' => 119, 'xxx_iso' => 'LS', 'yyy_name' => 'LESOTHO', 'yyy_nicename' => 'Lesotho', 'xxx_iso3' => 'LSO', 'xxx_numcode' => 426, 'xxx_phonecode' => 266],
    ['id' => 120, 'xxx_iso' => 'LR', 'yyy_name' => 'LIBERIA', 'yyy_nicename' => 'Liberia', 'xxx_iso3' => 'LBR', 'xxx_numcode' => 430, 'xxx_phonecode' => 231],
    ['id' => 121, 'xxx_iso' => 'LY', 'yyy_name' => 'LIBYAN ARAB JAMAHIRIYA', 'yyy_nicename' => 'Libyan Arab Jamahiriya', 'xxx_iso3' => 'LBY', 'xxx_numcode' => 434, 'xxx_phonecode' => 218],
    ['id' => 122, 'xxx_iso' => 'LI', 'yyy_name' => 'LIECHTENSTEIN', 'yyy_nicename' => 'Liechtenstein', 'xxx_iso3' => 'LIE', 'xxx_numcode' => 438, 'xxx_phonecode' => 423],
    ['id' => 123, 'xxx_iso' => 'LT', 'yyy_name' => 'LITHUANIA', 'yyy_nicename' => 'Lithuania', 'xxx_iso3' => 'LTU', 'xxx_numcode' => 440, 'xxx_phonecode' => 370],
    ['id' => 124, 'xxx_iso' => 'LU', 'yyy_name' => 'LUXEMBOURG', 'yyy_nicename' => 'Luxembourg', 'xxx_iso3' => 'LUX', 'xxx_numcode' => 442, 'xxx_phonecode' => 352],
    ['id' => 125, 'xxx_iso' => 'MO', 'yyy_name' => 'MACAO', 'yyy_nicename' => 'Macao', 'xxx_iso3' => 'MAC', 'xxx_numcode' => 446, 'xxx_phonecode' => 853],
    ['id' => 126, 'xxx_iso' => 'MK', 'yyy_name' => 'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF', 'yyy_nicename' => 'Macedonia, the Former Yugoslav Republic of', 'xxx_iso3' => 'MKD', 'xxx_numcode' => 807, 'xxx_phonecode' => 389],
    ['id' => 127, 'xxx_iso' => 'MG', 'yyy_name' => 'MADAGASCAR', 'yyy_nicename' => 'Madagascar', 'xxx_iso3' => 'MDG', 'xxx_numcode' => 450, 'xxx_phonecode' => 261],
    ['id' => 128, 'xxx_iso' => 'MW', 'yyy_name' => 'MALAWI', 'yyy_nicename' => 'Malawi', 'xxx_iso3' => 'MWI', 'xxx_numcode' => 454, 'xxx_phonecode' => 265],
    ['id' => 129, 'xxx_iso' => 'MY', 'yyy_name' => 'MALAYSIA', 'yyy_nicename' => 'Malaysia', 'xxx_iso3' => 'MYS', 'xxx_numcode' => 458, 'xxx_phonecode' => 60],
    ['id' => 130, 'xxx_iso' => 'MV', 'yyy_name' => 'MALDIVES', 'yyy_nicename' => 'Maldives', 'xxx_iso3' => 'MDV', 'xxx_numcode' => 462, 'xxx_phonecode' => 960],
    ['id' => 131, 'xxx_iso' => 'ML', 'yyy_name' => 'MALI', 'yyy_nicename' => 'Mali', 'xxx_iso3' => 'MLI', 'xxx_numcode' => 466, 'xxx_phonecode' => 223],
    ['id' => 132, 'xxx_iso' => 'MT', 'yyy_name' => 'MALTA', 'yyy_nicename' => 'Malta', 'xxx_iso3' => 'MLT', 'xxx_numcode' => 470, 'xxx_phonecode' => 356],
    ['id' => 133, 'xxx_iso' => 'MH', 'yyy_name' => 'MARSHALL ISLANDS', 'yyy_nicename' => 'Marshall Islands', 'xxx_iso3' => 'MHL', 'xxx_numcode' => 584, 'xxx_phonecode' => 692],
    ['id' => 134, 'xxx_iso' => 'MQ', 'yyy_name' => 'MARTINIQUE', 'yyy_nicename' => 'Martinique', 'xxx_iso3' => 'MTQ', 'xxx_numcode' => 474, 'xxx_phonecode' => 596],
    ['id' => 135, 'xxx_iso' => 'MR', 'yyy_name' => 'MAURITANIA', 'yyy_nicename' => 'Mauritania', 'xxx_iso3' => 'MRT', 'xxx_numcode' => 478, 'xxx_phonecode' => 222],
    ['id' => 136, 'xxx_iso' => 'MU', 'yyy_name' => 'MAURITIUS', 'yyy_nicename' => 'Mauritius', 'xxx_iso3' => 'MUS', 'xxx_numcode' => 480, 'xxx_phonecode' => 230],
    ['id' => 137, 'xxx_iso' => 'YT', 'yyy_name' => 'MAYOTTE', 'yyy_nicename' => 'Mayotte', 'xxx_iso3' => '', 'xxx_numcode' => '', 'xxx_phonecode' => 269],
    ['id' => 138, 'xxx_iso' => 'MX', 'yyy_name' => 'MEXICO', 'yyy_nicename' => 'Mexico', 'xxx_iso3' => 'MEX', 'xxx_numcode' => 484, 'xxx_phonecode' => 52],
    ['id' => 139, 'xxx_iso' => 'FM', 'yyy_name' => 'MICRONESIA, FEDERATED STATES OF', 'yyy_nicename' => 'Micronesia, Federated States of', 'xxx_iso3' => 'FSM', 'xxx_numcode' => 583, 'xxx_phonecode' => 691],
    ['id' => 140, 'xxx_iso' => 'MD', 'yyy_name' => 'MOLDOVA, REPUBLIC OF', 'yyy_nicename' => 'Moldova, Republic of', 'xxx_iso3' => 'MDA', 'xxx_numcode' => 498, 'xxx_phonecode' => 373],
    ['id' => 141, 'xxx_iso' => 'MC', 'yyy_name' => 'MONACO', 'yyy_nicename' => 'Monaco', 'xxx_iso3' => 'MCO', 'xxx_numcode' => 492, 'xxx_phonecode' => 377],
    ['id' => 142, 'xxx_iso' => 'MN', 'yyy_name' => 'MONGOLIA', 'yyy_nicename' => 'Mongolia', 'xxx_iso3' => 'MNG', 'xxx_numcode' => 496, 'xxx_phonecode' => 976],
    ['id' => 143, 'xxx_iso' => 'MS', 'yyy_name' => 'MONTSERRAT', 'yyy_nicename' => 'Montserrat', 'xxx_iso3' => 'MSR', 'xxx_numcode' => 500, 'xxx_phonecode' => 1664],
    ['id' => 144, 'xxx_iso' => 'MA', 'yyy_name' => 'MOROCCO', 'yyy_nicename' => 'Morocco', 'xxx_iso3' => 'MAR', 'xxx_numcode' => 504, 'xxx_phonecode' => 212],
    ['id' => 145, 'xxx_iso' => 'MZ', 'yyy_name' => 'MOZAMBIQUE', 'yyy_nicename' => 'Mozambique', 'xxx_iso3' => 'MOZ', 'xxx_numcode' => 508, 'xxx_phonecode' => 258],
    ['id' => 146, 'xxx_iso' => 'MM', 'yyy_name' => 'MYANMAR', 'yyy_nicename' => 'Myanmar', 'xxx_iso3' => 'MMR', 'xxx_numcode' => 104, 'xxx_phonecode' => 95],
    ['id' => 147, 'xxx_iso' => 'NA', 'yyy_name' => 'NAMIBIA', 'yyy_nicename' => 'Namibia', 'xxx_iso3' => 'NAM', 'xxx_numcode' => 516, 'xxx_phonecode' => 264],
    ['id' => 148, 'xxx_iso' => 'NR', 'yyy_name' => 'NAURU', 'yyy_nicename' => 'Nauru', 'xxx_iso3' => 'NRU', 'xxx_numcode' => 520, 'xxx_phonecode' => 674],
    ['id' => 149, 'xxx_iso' => 'NP', 'yyy_name' => 'NEPAL', 'yyy_nicename' => 'Nepal', 'xxx_iso3' => 'NPL', 'xxx_numcode' => 524, 'xxx_phonecode' => 977],
    ['id' => 150, 'xxx_iso' => 'NL', 'yyy_name' => 'NETHERLANDS', 'yyy_nicename' => 'Netherlands', 'xxx_iso3' => 'NLD', 'xxx_numcode' => 528, 'xxx_phonecode' => 31],
    ['id' => 151, 'xxx_iso' => 'AN', 'yyy_name' => 'NETHERLANDS ANTILLES', 'yyy_nicename' => 'Netherlands Antilles', 'xxx_iso3' => 'ANT', 'xxx_numcode' => 530, 'xxx_phonecode' => 599],
    ['id' => 152, 'xxx_iso' => 'NC', 'yyy_name' => 'NEW CALEDONIA', 'yyy_nicename' => 'New Caledonia', 'xxx_iso3' => 'NCL', 'xxx_numcode' => 540, 'xxx_phonecode' => 687],
    ['id' => 153, 'xxx_iso' => 'NZ', 'yyy_name' => 'NEW ZEALAND', 'yyy_nicename' => 'New Zealand', 'xxx_iso3' => 'NZL', 'xxx_numcode' => 554, 'xxx_phonecode' => 64],
    ['id' => 154, 'xxx_iso' => 'NI', 'yyy_name' => 'NICARAGUA', 'yyy_nicename' => 'Nicaragua', 'xxx_iso3' => 'NIC', 'xxx_numcode' => 558, 'xxx_phonecode' => 505],
    ['id' => 155, 'xxx_iso' => 'NE', 'yyy_name' => 'NIGER', 'yyy_nicename' => 'Niger', 'xxx_iso3' => 'NER', 'xxx_numcode' => 562, 'xxx_phonecode' => 227],
    ['id' => 156, 'xxx_iso' => 'NG', 'yyy_name' => 'NIGERIA', 'yyy_nicename' => 'Nigeria', 'xxx_iso3' => 'NGA', 'xxx_numcode' => 566, 'xxx_phonecode' => 234],
    ['id' => 157, 'xxx_iso' => 'NU', 'yyy_name' => 'NIUE', 'yyy_nicename' => 'Niue', 'xxx_iso3' => 'NIU', 'xxx_numcode' => 570, 'xxx_phonecode' => 683],
    ['id' => 158, 'xxx_iso' => 'NF', 'yyy_name' => 'NORFOLK ISLAND', 'yyy_nicename' => 'Norfolk Island', 'xxx_iso3' => 'NFK', 'xxx_numcode' => 574, 'xxx_phonecode' => 672],
    ['id' => 159, 'xxx_iso' => 'MP', 'yyy_name' => 'NORTHERN MARIANA ISLANDS', 'yyy_nicename' => 'Northern Mariana Islands', 'xxx_iso3' => 'MNP', 'xxx_numcode' => 580, 'xxx_phonecode' => 1670],
    ['id' => 160, 'xxx_iso' => 'NO', 'yyy_name' => 'NORWAY', 'yyy_nicename' => 'Norway', 'xxx_iso3' => 'NOR', 'xxx_numcode' => 578, 'xxx_phonecode' => 47],
    ['id' => 161, 'xxx_iso' => 'OM', 'yyy_name' => 'OMAN', 'yyy_nicename' => 'Oman', 'xxx_iso3' => 'OMN', 'xxx_numcode' => 512, 'xxx_phonecode' => 968],
    ['id' => 162, 'xxx_iso' => 'PK', 'yyy_name' => 'PAKISTAN', 'yyy_nicename' => 'Pakistan', 'xxx_iso3' => 'PAK', 'xxx_numcode' => 586, 'xxx_phonecode' => 92],
    ['id' => 163, 'xxx_iso' => 'PW', 'yyy_name' => 'PALAU', 'yyy_nicename' => 'Palau', 'xxx_iso3' => 'PLW', 'xxx_numcode' => 585, 'xxx_phonecode' => 680],
    ['id' => 164, 'xxx_iso' => 'PS', 'yyy_name' => 'PALESTINIAN TERRITORY, OCCUPIED', 'yyy_nicename' => 'Palestinian Territory, Occupied', 'xxx_iso3' => '', 'xxx_numcode' => '', 'xxx_phonecode' => 970],
    ['id' => 165, 'xxx_iso' => 'PA', 'yyy_name' => 'PANAMA', 'yyy_nicename' => 'Panama', 'xxx_iso3' => 'PAN', 'xxx_numcode' => 591, 'xxx_phonecode' => 507],
    ['id' => 166, 'xxx_iso' => 'PG', 'yyy_name' => 'PAPUA NEW GUINEA', 'yyy_nicename' => 'Papua New Guinea', 'xxx_iso3' => 'PNG', 'xxx_numcode' => 598, 'xxx_phonecode' => 675],
    ['id' => 167, 'xxx_iso' => 'PY', 'yyy_name' => 'PARAGUAY', 'yyy_nicename' => 'Paraguay', 'xxx_iso3' => 'PRY', 'xxx_numcode' => 600, 'xxx_phonecode' => 595],
    ['id' => 168, 'xxx_iso' => 'PE', 'yyy_name' => 'PERU', 'yyy_nicename' => 'Peru', 'xxx_iso3' => 'PER', 'xxx_numcode' => 604, 'xxx_phonecode' => 51],
    ['id' => 169, 'xxx_iso' => 'PH', 'yyy_name' => 'PHILIPPINES', 'yyy_nicename' => 'Philippines', 'xxx_iso3' => 'PHL', 'xxx_numcode' => 608, 'xxx_phonecode' => 63],
    ['id' => 170, 'xxx_iso' => 'PN', 'yyy_name' => 'PITCAIRN', 'yyy_nicename' => 'Pitcairn', 'xxx_iso3' => 'PCN', 'xxx_numcode' => 612, 'xxx_phonecode' => 0],
    ['id' => 171, 'xxx_iso' => 'PL', 'yyy_name' => 'POLAND', 'yyy_nicename' => 'Poland', 'xxx_iso3' => 'POL', 'xxx_numcode' => 616, 'xxx_phonecode' => 48],
    ['id' => 172, 'xxx_iso' => 'PT', 'yyy_name' => 'PORTUGAL', 'yyy_nicename' => 'Portugal', 'xxx_iso3' => 'PRT', 'xxx_numcode' => 620, 'xxx_phonecode' => 351],
    ['id' => 173, 'xxx_iso' => 'PR', 'yyy_name' => 'PUERTO RICO', 'yyy_nicename' => 'Puerto Rico', 'xxx_iso3' => 'PRI', 'xxx_numcode' => 630, 'xxx_phonecode' => 1787],
    ['id' => 174, 'xxx_iso' => 'QA', 'yyy_name' => 'QATAR', 'yyy_nicename' => 'Qatar', 'xxx_iso3' => 'QAT', 'xxx_numcode' => 634, 'xxx_phonecode' => 974],
    ['id' => 175, 'xxx_iso' => 'RE', 'yyy_name' => 'REUNION', 'yyy_nicename' => 'Reunion', 'xxx_iso3' => 'REU', 'xxx_numcode' => 638, 'xxx_phonecode' => 262],
    ['id' => 176, 'xxx_iso' => 'RO', 'yyy_name' => 'ROMANIA', 'yyy_nicename' => 'Romania', 'xxx_iso3' => 'ROM', 'xxx_numcode' => 642, 'xxx_phonecode' => 40],
    ['id' => 177, 'xxx_iso' => 'RU', 'yyy_name' => 'RUSSIAN FEDERATION', 'yyy_nicename' => 'Russian Federation', 'xxx_iso3' => 'RUS', 'xxx_numcode' => 643, 'xxx_phonecode' => 7],
    ['id' => 178, 'xxx_iso' => 'RW', 'yyy_name' => 'RWANDA', 'yyy_nicename' => 'Rwanda', 'xxx_iso3' => 'RWA', 'xxx_numcode' => 646, 'xxx_phonecode' => 250],
    ['id' => 179, 'xxx_iso' => 'SH', 'yyy_name' => 'SAINT HELENA', 'yyy_nicename' => 'Saint Helena', 'xxx_iso3' => 'SHN', 'xxx_numcode' => 654, 'xxx_phonecode' => 290],
    ['id' => 180, 'xxx_iso' => 'KN', 'yyy_name' => 'SAINT KITTS AND NEVIS', 'yyy_nicename' => 'Saint Kitts and Nevis', 'xxx_iso3' => 'KNA', 'xxx_numcode' => 659, 'xxx_phonecode' => 1869],
    ['id' => 181, 'xxx_iso' => 'LC', 'yyy_name' => 'SAINT LUCIA', 'yyy_nicename' => 'Saint Lucia', 'xxx_iso3' => 'LCA', 'xxx_numcode' => 662, 'xxx_phonecode' => 1758],
    ['id' => 182, 'xxx_iso' => 'PM', 'yyy_name' => 'SAINT PIERRE AND MIQUELON', 'yyy_nicename' => 'Saint Pierre and Miquelon', 'xxx_iso3' => 'SPM', 'xxx_numcode' => 666, 'xxx_phonecode' => 508],
    ['id' => 183, 'xxx_iso' => 'VC', 'yyy_name' => 'SAINT VINCENT AND THE GRENADINES', 'yyy_nicename' => 'Saint Vincent and the Grenadines', 'xxx_iso3' => 'VCT', 'xxx_numcode' => 670, 'xxx_phonecode' => 1784],
    ['id' => 184, 'xxx_iso' => 'WS', 'yyy_name' => 'SAMOA', 'yyy_nicename' => 'Samoa', 'xxx_iso3' => 'WSM', 'xxx_numcode' => 882, 'xxx_phonecode' => 684],
    ['id' => 185, 'xxx_iso' => 'SM', 'yyy_name' => 'SAN MARINO', 'yyy_nicename' => 'San Marino', 'xxx_iso3' => 'SMR', 'xxx_numcode' => 674, 'xxx_phonecode' => 378],
    ['id' => 186, 'xxx_iso' => 'ST', 'yyy_name' => 'SAO TOME AND PRINCIPE', 'yyy_nicename' => 'Sao Tome and Principe', 'xxx_iso3' => 'STP', 'xxx_numcode' => 678, 'xxx_phonecode' => 239],
    ['id' => 187, 'xxx_iso' => 'SA', 'yyy_name' => 'SAUDI ARABIA', 'yyy_nicename' => 'Saudi Arabia', 'xxx_iso3' => 'SAU', 'xxx_numcode' => 682, 'xxx_phonecode' => 966],
    ['id' => 188, 'xxx_iso' => 'SN', 'yyy_name' => 'SENEGAL', 'yyy_nicename' => 'Senegal', 'xxx_iso3' => 'SEN', 'xxx_numcode' => 686, 'xxx_phonecode' => 221],
    ['id' => 190, 'xxx_iso' => 'SC', 'yyy_name' => 'SEYCHELLES', 'yyy_nicename' => 'Seychelles', 'xxx_iso3' => 'SYC', 'xxx_numcode' => 690, 'xxx_phonecode' => 248],
    ['id' => 191, 'xxx_iso' => 'SL', 'yyy_name' => 'SIERRA LEONE', 'yyy_nicename' => 'Sierra Leone', 'xxx_iso3' => 'SLE', 'xxx_numcode' => 694, 'xxx_phonecode' => 232],
    ['id' => 192, 'xxx_iso' => 'SG', 'yyy_name' => 'SINGAPORE', 'yyy_nicename' => 'Singapore', 'xxx_iso3' => 'SGP', 'xxx_numcode' => 702, 'xxx_phonecode' => 65],
    ['id' => 193, 'xxx_iso' => 'SK', 'yyy_name' => 'SLOVAKIA', 'yyy_nicename' => 'Slovakia', 'xxx_iso3' => 'SVK', 'xxx_numcode' => 703, 'xxx_phonecode' => 421],
    ['id' => 194, 'xxx_iso' => 'SI', 'yyy_name' => 'SLOVENIA', 'yyy_nicename' => 'Slovenia', 'xxx_iso3' => 'SVN', 'xxx_numcode' => 705, 'xxx_phonecode' => 386],
    ['id' => 195, 'xxx_iso' => 'SB', 'yyy_name' => 'SOLOMON ISLANDS', 'yyy_nicename' => 'Solomon Islands', 'xxx_iso3' => 'SLB', 'xxx_numcode' => 90, 'xxx_phonecode' => 677],
    ['id' => 196, 'xxx_iso' => 'SO', 'yyy_name' => 'SOMALIA', 'yyy_nicename' => 'Somalia', 'xxx_iso3' => 'SOM', 'xxx_numcode' => 706, 'xxx_phonecode' => 252],
    ['id' => 197, 'xxx_iso' => 'ZA', 'yyy_name' => 'SOUTH AFRICA', 'yyy_nicename' => 'South Africa', 'xxx_iso3' => 'ZAF', 'xxx_numcode' => 710, 'xxx_phonecode' => 27],
    ['id' => 198, 'xxx_iso' => 'GS', 'yyy_name' => 'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS', 'yyy_nicename' => 'South Georgia and the South Sandwich Islands', 'xxx_iso3' => '', 'xxx_numcode' => '', 'xxx_phonecode' => 0],
    ['id' => 199, 'xxx_iso' => 'ES', 'yyy_name' => 'SPAIN', 'yyy_nicename' => 'Spain', 'xxx_iso3' => 'ESP', 'xxx_numcode' => 724, 'xxx_phonecode' => 34],
    ['id' => 200, 'xxx_iso' => 'LK', 'yyy_name' => 'SRI LANKA', 'yyy_nicename' => 'Sri Lanka', 'xxx_iso3' => 'LKA', 'xxx_numcode' => 144, 'xxx_phonecode' => 94],
    ['id' => 201, 'xxx_iso' => 'SD', 'yyy_name' => 'SUDAN', 'yyy_nicename' => 'Sudan', 'xxx_iso3' => 'SDN', 'xxx_numcode' => 736, 'xxx_phonecode' => 249],
    ['id' => 202, 'xxx_iso' => 'SR', 'yyy_name' => 'SURINAME', 'yyy_nicename' => 'Suriname', 'xxx_iso3' => 'SUR', 'xxx_numcode' => 740, 'xxx_phonecode' => 597],
    ['id' => 203, 'xxx_iso' => 'SJ', 'yyy_name' => 'SVALBARD AND JAN MAYEN', 'yyy_nicename' => 'Svalbard and Jan Mayen', 'xxx_iso3' => 'SJM', 'xxx_numcode' => 744, 'xxx_phonecode' => 47],
    ['id' => 204, 'xxx_iso' => 'SZ', 'yyy_name' => 'SWAZILAND', 'yyy_nicename' => 'Swaziland', 'xxx_iso3' => 'SWZ', 'xxx_numcode' => 748, 'xxx_phonecode' => 268],
    ['id' => 205, 'xxx_iso' => 'SE', 'yyy_name' => 'SWEDEN', 'yyy_nicename' => 'Sweden', 'xxx_iso3' => 'SWE', 'xxx_numcode' => 752, 'xxx_phonecode' => 46],
    ['id' => 206, 'xxx_iso' => 'CH', 'yyy_name' => 'SWITZERLAND', 'yyy_nicename' => 'Switzerland', 'xxx_iso3' => 'CHE', 'xxx_numcode' => 756, 'xxx_phonecode' => 41],
    ['id' => 207, 'xxx_iso' => 'SY', 'yyy_name' => 'SYRIAN ARAB REPUBLIC', 'yyy_nicename' => 'Syrian Arab Republic', 'xxx_iso3' => 'SYR', 'xxx_numcode' => 760, 'xxx_phonecode' => 963],
    ['id' => 208, 'xxx_iso' => 'TW', 'yyy_name' => 'TAIWAN, PROVINCE OF CHINA', 'yyy_nicename' => 'Taiwan, Province of China', 'xxx_iso3' => 'TWN', 'xxx_numcode' => 158, 'xxx_phonecode' => 886],
    ['id' => 209, 'xxx_iso' => 'TJ', 'yyy_name' => 'TAJIKISTAN', 'yyy_nicename' => 'Tajikistan', 'xxx_iso3' => 'TJK', 'xxx_numcode' => 762, 'xxx_phonecode' => 992],
    ['id' => 210, 'xxx_iso' => 'TZ', 'yyy_name' => 'TANZANIA, UNITED REPUBLIC OF', 'yyy_nicename' => 'Tanzania, United Republic of', 'xxx_iso3' => 'TZA', 'xxx_numcode' => 834, 'xxx_phonecode' => 255],
    ['id' => 211, 'xxx_iso' => 'TH', 'yyy_name' => 'THAILAND', 'yyy_nicename' => 'Thailand', 'xxx_iso3' => 'THA', 'xxx_numcode' => 764, 'xxx_phonecode' => 66],
    ['id' => 212, 'xxx_iso' => 'TL', 'yyy_name' => 'TIMOR-LESTE', 'yyy_nicename' => 'Timor-Leste', 'xxx_iso3' => '', 'xxx_numcode' => '', 'xxx_phonecode' => 670],
    ['id' => 213, 'xxx_iso' => 'TG', 'yyy_name' => 'TOGO', 'yyy_nicename' => 'Togo', 'xxx_iso3' => 'TGO', 'xxx_numcode' => 768, 'xxx_phonecode' => 228],
    ['id' => 214, 'xxx_iso' => 'TK', 'yyy_name' => 'TOKELAU', 'yyy_nicename' => 'Tokelau', 'xxx_iso3' => 'TKL', 'xxx_numcode' => 772, 'xxx_phonecode' => 690],
    ['id' => 215, 'xxx_iso' => 'TO', 'yyy_name' => 'TONGA', 'yyy_nicename' => 'Tonga', 'xxx_iso3' => 'TON', 'xxx_numcode' => 776, 'xxx_phonecode' => 676],
    ['id' => 216, 'xxx_iso' => 'TT', 'yyy_name' => 'TRINIDAD AND TOBAGO', 'yyy_nicename' => 'Trinidad and Tobago', 'xxx_iso3' => 'TTO', 'xxx_numcode' => 780, 'xxx_phonecode' => 1868],
    ['id' => 217, 'xxx_iso' => 'TN', 'yyy_name' => 'TUNISIA', 'yyy_nicename' => 'Tunisia', 'xxx_iso3' => 'TUN', 'xxx_numcode' => 788, 'xxx_phonecode' => 216],
    ['id' => 218, 'xxx_iso' => 'TR', 'yyy_name' => 'TURKEY', 'yyy_nicename' => 'Turkey', 'xxx_iso3' => 'TUR', 'xxx_numcode' => 792, 'xxx_phonecode' => 90],
    ['id' => 219, 'xxx_iso' => 'TM', 'yyy_name' => 'TURKMENISTAN', 'yyy_nicename' => 'Turkmenistan', 'xxx_iso3' => 'TKM', 'xxx_numcode' => 795, 'xxx_phonecode' => 7370],
    ['id' => 220, 'xxx_iso' => 'TC', 'yyy_name' => 'TURKS AND CAICOS ISLANDS', 'yyy_nicename' => 'Turks and Caicos Islands', 'xxx_iso3' => 'TCA', 'xxx_numcode' => 796, 'xxx_phonecode' => 1649],
    ['id' => 221, 'xxx_iso' => 'TV', 'yyy_name' => 'TUVALU', 'yyy_nicename' => 'Tuvalu', 'xxx_iso3' => 'TUV', 'xxx_numcode' => 798, 'xxx_phonecode' => 688],
    ['id' => 222, 'xxx_iso' => 'UG', 'yyy_name' => 'UGANDA', 'yyy_nicename' => 'Uganda', 'xxx_iso3' => 'UGA', 'xxx_numcode' => 800, 'xxx_phonecode' => 256],
    ['id' => 223, 'xxx_iso' => 'UA', 'yyy_name' => 'UKRAINE', 'yyy_nicename' => 'Ukraine', 'xxx_iso3' => 'UKR', 'xxx_numcode' => 804, 'xxx_phonecode' => 380],
    ['id' => 224, 'xxx_iso' => 'AE', 'yyy_name' => 'UNITED ARAB EMIRATES', 'yyy_nicename' => 'United Arab Emirates', 'xxx_iso3' => 'ARE', 'xxx_numcode' => 784, 'xxx_phonecode' => 971],
    ['id' => 225, 'xxx_iso' => 'GB', 'yyy_name' => 'UNITED KINGDOM', 'yyy_nicename' => 'United Kingdom', 'xxx_iso3' => 'GBR', 'xxx_numcode' => 826, 'xxx_phonecode' => 44],
    ['id' => 226, 'xxx_iso' => 'US', 'yyy_name' => 'UNITED STATES', 'yyy_nicename' => 'United States', 'xxx_iso3' => 'USA', 'xxx_numcode' => 840, 'xxx_phonecode' => 1],
    ['id' => 227, 'xxx_iso' => 'UM', 'yyy_name' => 'UNITED STATES MINOR OUTLYING ISLANDS', 'yyy_nicename' => 'United States Minor Outlying Islands', 'xxx_iso3' => '', 'xxx_numcode' => '', 'xxx_phonecode' => 1],
    ['id' => 228, 'xxx_iso' => 'UY', 'yyy_name' => 'URUGUAY', 'yyy_nicename' => 'Uruguay', 'xxx_iso3' => 'URY', 'xxx_numcode' => 858, 'xxx_phonecode' => 598],
    ['id' => 229, 'xxx_iso' => 'UZ', 'yyy_name' => 'UZBEKISTAN', 'yyy_nicename' => 'Uzbekistan', 'xxx_iso3' => 'UZB', 'xxx_numcode' => 860, 'xxx_phonecode' => 998],
    ['id' => 230, 'xxx_iso' => 'VU', 'yyy_name' => 'VANUATU', 'yyy_nicename' => 'Vanuatu', 'xxx_iso3' => 'VUT', 'xxx_numcode' => 548, 'xxx_phonecode' => 678],
    ['id' => 231, 'xxx_iso' => 'VE', 'yyy_name' => 'VENEZUELA', 'yyy_nicename' => 'Venezuela', 'xxx_iso3' => 'VEN', 'xxx_numcode' => 862, 'xxx_phonecode' => 58],
    ['id' => 232, 'xxx_iso' => 'VN', 'yyy_name' => 'VIET NAM', 'yyy_nicename' => 'Viet Nam', 'xxx_iso3' => 'VNM', 'xxx_numcode' => 704, 'xxx_phonecode' => 84],
    ['id' => 233, 'xxx_iso' => 'VG', 'yyy_name' => 'VIRGIN ISLANDS, BRITISH', 'yyy_nicename' => 'Virgin Islands, British', 'xxx_iso3' => 'VGB', 'xxx_numcode' => 92, 'xxx_phonecode' => 1284],
    ['id' => 234, 'xxx_iso' => 'VI', 'yyy_name' => 'VIRGIN ISLANDS, U.S.', 'yyy_nicename' => 'Virgin Islands, U.s.', 'xxx_iso3' => 'VIR', 'xxx_numcode' => 850, 'xxx_phonecode' => 1340],
    ['id' => 235, 'xxx_iso' => 'WF', 'yyy_name' => 'WALLIS AND FUTUNA', 'yyy_nicename' => 'Wallis and Futuna', 'xxx_iso3' => 'WLF', 'xxx_numcode' => 876, 'xxx_phonecode' => 681],
    ['id' => 236, 'xxx_iso' => 'EH', 'yyy_name' => 'WESTERN SAHARA', 'yyy_nicename' => 'Western Sahara', 'xxx_iso3' => 'ESH', 'xxx_numcode' => 732, 'xxx_phonecode' => 212],
    ['id' => 237, 'xxx_iso' => 'YE', 'yyy_name' => 'YEMEN', 'yyy_nicename' => 'Yemen', 'xxx_iso3' => 'YEM', 'xxx_numcode' => 887, 'xxx_phonecode' => 967],
    ['id' => 238, 'xxx_iso' => 'ZM', 'yyy_name' => 'ZAMBIA', 'yyy_nicename' => 'Zambia', 'xxx_iso3' => 'ZMB', 'xxx_numcode' => 894, 'xxx_phonecode' => 260],
    ['id' => 239, 'xxx_iso' => 'ZW', 'yyy_name' => 'ZIMBABWE', 'yyy_nicename' => 'Zimbabwe', 'xxx_iso3' => 'ZWE', 'xxx_numcode' => 716, 'xxx_phonecode' => 263],
    ['id' => 240, 'xxx_iso' => 'RS', 'yyy_name' => 'SERBIA', 'yyy_nicename' => 'Serbia', 'xxx_iso3' => 'SRB', 'xxx_numcode' => 688, 'xxx_phonecode' => 381],
    ['id' => 241, 'xxx_iso' => 'AP', 'yyy_name' => 'ASIA PACIFIC REGION', 'yyy_nicename' => 'Asia / Pacific Region', 'xxx_iso3' => '0', 'xxx_numcode' => 0, 'xxx_phonecode' => 0],
    ['id' => 242, 'xxx_iso' => 'ME', 'yyy_name' => 'MONTENEGRO', 'yyy_nicename' => 'Montenegro', 'xxx_iso3' => 'MNE', 'xxx_numcode' => 499, 'xxx_phonecode' => 382],
    ['id' => 243, 'xxx_iso' => 'AX', 'yyy_name' => 'ALAND ISLANDS', 'yyy_nicename' => 'Aland Islands', 'xxx_iso3' => 'ALA', 'xxx_numcode' => 248, 'xxx_phonecode' => 358],
    ['id' => 244, 'xxx_iso' => 'BQ', 'yyy_name' => 'BONAIRE, SINT EUSTATIUS AND SABA', 'yyy_nicename' => 'Bonaire, Sint Eustatius and Saba', 'xxx_iso3' => 'BES', 'xxx_numcode' => 535, 'xxx_phonecode' => 599],
    ['id' => 245, 'xxx_iso' => 'CW', 'yyy_name' => 'CURACAO', 'yyy_nicename' => 'Curacao', 'xxx_iso3' => 'CUW', 'xxx_numcode' => 531, 'xxx_phonecode' => 599],
    ['id' => 246, 'xxx_iso' => 'GG', 'yyy_name' => 'GUERNSEY', 'yyy_nicename' => 'Guernsey', 'xxx_iso3' => 'GGY', 'xxx_numcode' => 831, 'xxx_phonecode' => 44],
    ['id' => 247, 'xxx_iso' => 'IM', 'yyy_name' => 'ISLE OF MAN', 'yyy_nicename' => 'Isle of Man', 'xxx_iso3' => 'IMN', 'xxx_numcode' => 833, 'xxx_phonecode' => 44],
    ['id' => 248, 'xxx_iso' => 'JE', 'yyy_name' => 'JERSEY', 'yyy_nicename' => 'Jersey', 'xxx_iso3' => 'JEY', 'xxx_numcode' => 832, 'xxx_phonecode' => 44],
    ['id' => 249, 'xxx_iso' => 'XK', 'yyy_name' => 'KOSOVO', 'yyy_nicename' => 'Kosovo', 'xxx_iso3' => '---', 'xxx_numcode' => 0, 'xxx_phonecode' => 381],
    ['id' => 250, 'xxx_iso' => 'BL', 'yyy_name' => 'SAINT BARTHELEMY', 'yyy_nicename' => 'Saint Barthelemy', 'xxx_iso3' => 'BLM', 'xxx_numcode' => 652, 'xxx_phonecode' => 590],
    ['id' => 251, 'xxx_iso' => 'MF', 'yyy_name' => 'SAINT MARTIN', 'yyy_nicename' => 'Saint Martin', 'xxx_iso3' => 'MAF', 'xxx_numcode' => 663, 'xxx_phonecode' => 590],
    ['id' => 252, 'xxx_iso' => 'SX', 'yyy_name' => 'SINT MAARTEN', 'yyy_nicename' => 'Sint Maarten', 'xxx_iso3' => 'SXM', 'xxx_numcode' => 534, 'xxx_phonecode' => 1],
    ['id' => 253, 'xxx_iso' => 'SS', 'yyy_name' => 'SOUTH SUDAN', 'yyy_nicename' => 'South Sudan', 'xxx_iso3' => 'SSD', 'xxx_numcode' => 728, 'xxx_phonecode' => 211],
]);

$model = new Model($persistence, ['table' => 'file']);
$model->addField('xxx2_name', ['type' => 'string']);
$model->addField('xxx_type', ['type' => 'string']);
$model->addField('xxx_is_folder', ['type' => 'boolean']);
$model->addField('xxx_parent_folder_id', ['type' => 'bigint']);
// KEY `fk_file_file_idx` (`xxx_parent_folder_id`)
(new \Atk4\Schema\Migration($model))->dropIfExists()->create();
$model->import([
    ['id' => 1, 'xxx2_name' => 'phpunit.xml', 'xxx_type' => 'xml', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => null],
    ['id' => 2, 'xxx2_name' => 'LICENSE', 'xxx_type' => '', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => null],
    ['id' => 3, 'xxx2_name' => 'Makefile', 'xxx_type' => '', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => null],
    ['id' => 4, 'xxx2_name' => 'tests', 'xxx_type' => '', 'xxx_is_folder' => 1, 'xxx_parent_folder_id' => null],
    ['id' => 5, 'xxx2_name' => 'TemplateTest.php', 'xxx_type' => 'php', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => 4],
    ['id' => 6, 'xxx2_name' => 'template', 'xxx_type' => '', 'xxx_is_folder' => 1, 'xxx_parent_folder_id' => null],
    ['id' => 7, 'xxx2_name' => 'semantic-ui', 'xxx_type' => '', 'xxx_is_folder' => 1, 'xxx_parent_folder_id' => 6],
    ['id' => 8, 'xxx2_name' => 'tree.html', 'xxx_type' => 'html', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => 7],
    ['id' => 9, 'xxx2_name' => 'element.html', 'xxx_type' => 'html', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => 7],
    ['id' => 10, 'xxx2_name' => 'button.html', 'xxx_type' => 'html', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => 7],
    ['id' => 11, 'xxx2_name' => 'icon.html', 'xxx_type' => 'html', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => 7],
    ['id' => 12, 'xxx2_name' => 'element.jade', 'xxx_type' => 'jade', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => 7],
    ['id' => 13, 'xxx2_name' => 'tree.jade', 'xxx_type' => 'jade', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => 7],
    ['id' => 14, 'xxx2_name' => 'icon.jade', 'xxx_type' => 'jade', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => 7],
    ['id' => 15, 'xxx2_name' => 'button.jade', 'xxx_type' => 'jade', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => 7],
    ['id' => 16, 'xxx2_name' => 'docs', 'xxx_type' => '', 'xxx_is_folder' => 1, 'xxx_parent_folder_id' => null],
    ['id' => 17, 'xxx2_name' => 'index.rst', 'xxx_type' => 'rst', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => 16],
    ['id' => 18, 'xxx2_name' => 'login.png', 'xxx_type' => 'png', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => 16],
    ['id' => 19, 'xxx2_name' => 'requirements.txt', 'xxx_type' => 'txt', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => 16],
    ['id' => 20, 'xxx2_name' => 'crud2.png', 'xxx_type' => 'png', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => 16],
    ['id' => 21, 'xxx2_name' => 'images', 'xxx_type' => '', 'xxx_is_folder' => 1, 'xxx_parent_folder_id' => 16],
    ['id' => 22, 'xxx2_name' => 'folders.png', 'xxx_type' => 'png', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => 21],
    ['id' => 23, 'xxx2_name' => 'layout.png', 'xxx_type' => 'png', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => 21],
    ['id' => 24, 'xxx2_name' => 'menu.png', 'xxx_type' => 'png', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => 21],
    ['id' => 25, 'xxx2_name' => 'Makefile', 'xxx_type' => '', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => 16],
    ['id' => 26, 'xxx2_name' => 'conf.py', 'xxx_type' => 'py', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => 16],
    ['id' => 27, 'xxx2_name' => 'README.md', 'xxx_type' => 'md', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => 16],
    ['id' => 28, 'xxx2_name' => 'quickstart.rst', 'xxx_type' => 'rst', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => 16],
    ['id' => 29, 'xxx2_name' => 'crud.png', 'xxx_type' => 'png', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => 16],
    ['id' => 30, 'xxx2_name' => 'layouts.png', 'xxx_type' => 'png', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => 16],
    ['id' => 31, 'xxx2_name' => 'template.rst', 'xxx_type' => 'rst', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => 16],
    ['id' => 32, 'xxx2_name' => 'README.md', 'xxx_type' => 'md', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => null],
    ['id' => 33, 'xxx2_name' => 'demos', 'xxx_type' => '', 'xxx_is_folder' => 1, 'xxx_parent_folder_id' => null],
    ['id' => 34, 'xxx2_name' => 'index.php', 'xxx_type' => 'php', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => 33],
    ['id' => 35, 'xxx2_name' => 'layout.php', 'xxx_type' => 'php', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => 33],
    ['id' => 36, 'xxx2_name' => 'templates', 'xxx_type' => '', 'xxx_is_folder' => 1, 'xxx_parent_folder_id' => 33],
    ['id' => 37, 'xxx2_name' => 'fixed.jade', 'xxx_type' => 'jade', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => 36],
    ['id' => 38, 'xxx2_name' => 'layout1.html', 'xxx_type' => 'html', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => 36],
    ['id' => 39, 'xxx2_name' => 'layout2.jade', 'xxx_type' => 'jade', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => 36],
    ['id' => 40, 'xxx2_name' => 'layout1.jade', 'xxx_type' => 'jade', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => 36],
    ['id' => 41, 'xxx2_name' => 'fixed.html', 'xxx_type' => 'html', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => 36],
    ['id' => 42, 'xxx2_name' => 'index1.html', 'xxx_type' => 'html', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => 36],
    ['id' => 43, 'xxx2_name' => 'layout2.html', 'xxx_type' => 'html', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => 36],
    ['id' => 44, 'xxx2_name' => 'button.php', 'xxx_type' => 'php', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => 33],
    ['id' => 45, 'xxx2_name' => 'composer.json', 'xxx_type' => 'json', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => null],
    ['id' => 46, 'xxx2_name' => 'src', 'xxx_type' => '', 'xxx_is_folder' => 1, 'xxx_parent_folder_id' => null],
    ['id' => 47, 'xxx2_name' => 'Icon.php', 'xxx_type' => 'php', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => 46],
    ['id' => 48, 'xxx2_name' => 'App.php', 'xxx_type' => 'php', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => 46],
    ['id' => 49, 'xxx2_name' => 'Label.php', 'xxx_type' => 'php', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => 46],
    ['id' => 50, 'xxx2_name' => 'Layout', 'xxx_type' => '', 'xxx_is_folder' => 1, 'xxx_parent_folder_id' => 46],
    ['id' => 51, 'xxx2_name' => 'App.php', 'xxx_type' => 'php', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => 50],
    ['id' => 52, 'xxx2_name' => 'MiniApp.php', 'xxx_type' => 'php', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => 46],
    ['id' => 53, 'xxx2_name' => 'Lister.php', 'xxx_type' => 'php', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => 46],
    ['id' => 54, 'xxx2_name' => 'Layout.php', 'xxx_type' => 'php', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => 46],
    ['id' => 55, 'xxx2_name' => 'Buttons.php', 'xxx_type' => 'php', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => 46],
    ['id' => 56, 'xxx2_name' => 'View.php', 'xxx_type' => 'php', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => 46],
    ['id' => 57, 'xxx2_name' => 'Tree.php', 'xxx_type' => 'php', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => 46],
    ['id' => 58, 'xxx2_name' => 'Template.php', 'xxx_type' => 'php', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => 46],
    ['id' => 59, 'xxx2_name' => 'Exception.php', 'xxx_type' => 'php', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => 46],
    ['id' => 60, 'xxx2_name' => 'Text.php', 'xxx_type' => 'php', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => 46],
    ['id' => 61, 'xxx2_name' => 'Button.php', 'xxx_type' => 'php', 'xxx_is_folder' => 0, 'xxx_parent_folder_id' => 46],
]);

$model = new Model($persistence, ['table' => 'stats']);
$model->addField('xxx_project_name', ['type' => 'string']);
$model->addField('xxx_project_code', ['type' => 'string']);
$model->addField('xxx_description', ['type' => 'text']);
$model->addField('xxx_client_name', ['type' => 'string']);
$model->addField('xxx_client_address', ['type' => 'text']);
$model->addField('xxx_client_country_iso', ['type' => 'string']); // should be CHAR(2)
$model->addField('xxx_is_commercial', ['type' => 'boolean']);
$model->addField('xxx_currency', ['type' => 'string']); // should be ENUM('EUR' ,'USD', 'GBP') or CHAR(3)
$model->addField('xxx_is_completed', ['type' => 'boolean']);
$model->addField('xxx_project_budget', ['type' => 'float']);
$model->addField('xxx_project_invoiced', ['type' => 'float']);
$model->addField('xxx_project_paid', ['type' => 'float']);
$model->addField('xxx_project_hour_cost', ['type' => 'float']);
$model->addField('xxx_project_hours_est', ['type' => 'integer']);
$model->addField('xxx_project_hours_reported', ['type' => 'integer']);
$model->addField('xxx_project_expenses_est', ['type' => 'float']);
$model->addField('xxx_project_expenses', ['type' => 'float']);
$model->addField('xxx_project_mgmt_cost_pct', ['type' => 'float']);
$model->addField('xxx_project_qa_cost_pct', ['type' => 'float']);
$model->addField('xxx_start_date', ['type' => 'date']);
$model->addField('xxx_finish_date', ['type' => 'date']);
$model->addField('xxx_finish_time', ['type' => 'time']);
$model->addField('xxx_created', ['type' => 'datetime']);
$model->addField('xxx_updated', ['type' => 'datetime']);
(new \Atk4\Schema\Migration($model))->dropIfExists()->create();
$model->import([
    ['id' => 1, 'xxx_project_name' => 'Agile DSQL', 'xxx_project_code' => 'at01', 'xxx_description' => 'DSQL is a composable SQL query builder. You can write multi-vendor queries in PHP profiting from better security, clean syntax and avoid human errors.', 'xxx_client_name' => 'Agile Toolkit', 'xxx_client_address' => 'Some Street,\nGarden City\nUK', 'xxx_client_country_iso' => 'GB', 'xxx_is_commercial' => 0, 'xxx_currency' => 'GBP', 'xxx_is_completed' => 1, 'xxx_project_budget' => 7000, 'xxx_project_invoiced' => 0, 'xxx_project_paid' => 0, 'xxx_project_hour_cost' => 0, 'xxx_project_hours_est' => 150, 'xxx_project_hours_reported' => 125, 'xxx_project_expenses_est' => 50, 'xxx_project_expenses' => 0, 'xxx_project_mgmt_cost_pct' => 0.1, 'xxx_project_qa_cost_pct' => 0.2, 'xxx_start_date' => '2016-01-26', 'xxx_finish_date' => '2016-06-23', 'xxx_finish_time' => '12:50:00', 'xxx_created' => '2017-04-06 10:34:34', 'xxx_updated' => '2017-04-06 10:35:04'],
    ['id' => 2, 'xxx_project_name' => 'Agile Core', 'xxx_project_code' => 'at02', 'xxx_description' => 'Collection of PHP Traits for designing object-oriented frameworks.', 'xxx_client_name' => 'Agile Toolkit', 'xxx_client_address' => 'Some Street,\nGarden City\nUK', 'xxx_client_country_iso' => 'GB', 'xxx_is_commercial' => 0, 'xxx_currency' => 'GBP', 'xxx_is_completed' => 1, 'xxx_project_budget' => 3000, 'xxx_project_invoiced' => 0, 'xxx_project_paid' => 0, 'xxx_project_hour_cost' => 0, 'xxx_project_hours_est' => 70, 'xxx_project_hours_reported' => 56, 'xxx_project_expenses_est' => 50, 'xxx_project_expenses' => 0, 'xxx_project_mgmt_cost_pct' => 0.1, 'xxx_project_qa_cost_pct' => 0.2, 'xxx_start_date' => '2016-04-27', 'xxx_finish_date' => '2016-05-21', 'xxx_finish_time' => '18:41:00', 'xxx_created' => '2017-04-06 10:21:50', 'xxx_updated' => '2017-04-06 10:35:04'],
    ['id' => 3, 'xxx_project_name' => 'Agile Data', 'xxx_project_code' => 'at03', 'xxx_description' => 'Agile Data implements an entirely new pattern for data abstraction, that is specifically designed for remote databases such as RDS, Cloud SQL, BigQuery and other distributed data storage architectures. It focuses on reducing number of requests your App have to send to the Database by using more sophisticated queries while also offering full Domain Model mapping and Database vendor abstraction.', 'xxx_client_name' => 'Agile Toolkit', 'xxx_client_address' => 'Some Street,\nGarden City\nUK', 'xxx_client_country_iso' => 'GB', 'xxx_is_commercial' => 0, 'xxx_currency' => 'GBP', 'xxx_is_completed' => 1, 'xxx_project_budget' => 12000, 'xxx_project_invoiced' => 0, 'xxx_project_paid' => 0, 'xxx_project_hour_cost' => 0, 'xxx_project_hours_est' => 300, 'xxx_project_hours_reported' => 394, 'xxx_project_expenses_est' => 600, 'xxx_project_expenses' => 430, 'xxx_project_mgmt_cost_pct' => 0.2, 'xxx_project_qa_cost_pct' => 0.3, 'xxx_start_date' => '2016-04-17', 'xxx_finish_date' => '2016-06-20', 'xxx_finish_time' => '03:04:00', 'xxx_created' => '2017-04-06 10:30:15', 'xxx_updated' => '2017-04-06 10:35:04'],
    ['id' => 4, 'xxx_project_name' => 'Agile UI', 'xxx_project_code' => 'at04', 'xxx_description' => 'Web UI Component library.', 'xxx_client_name' => 'Agile Toolkit', 'xxx_client_address' => 'Some Street,\nGarden City\nUK', 'xxx_client_country_iso' => 'GB', 'xxx_is_commercial' => 0, 'xxx_currency' => 'GBP', 'xxx_is_completed' => 0, 'xxx_project_budget' => 20000, 'xxx_project_invoiced' => 0, 'xxx_project_paid' => 0, 'xxx_project_hour_cost' => 0, 'xxx_project_hours_est' => 600, 'xxx_project_hours_reported' => 368, 'xxx_project_expenses_est' => 1200, 'xxx_project_expenses' => 0, 'xxx_project_mgmt_cost_pct' => 0.3, 'xxx_project_qa_cost_pct' => 0.4, 'xxx_start_date' => '2016-09-17', 'xxx_finish_date' => '', 'xxx_finish_time' => '', 'xxx_created' => '2017-04-06 10:30:15', 'xxx_updated' => '2017-04-06 10:35:04'],
]);

$model = new Model($persistence, ['table' => 'product_category']);
$model->addField('xxx3_name', ['type' => 'string']);
(new \Atk4\Schema\Migration($model))->dropIfExists()->create();
$model->import([
    ['id' => 1, 'xxx3_name' => 'Condiments and Gravies'],
    ['id' => 2, 'xxx3_name' => 'Beverages'],
    ['id' => 3, 'xxx3_name' => 'Dairy'],
]);

$model = new Model($persistence, ['table' => 'product_sub_category']);
$model->addField('xxx4_name', ['type' => 'string']);
$model->addField('xxx2_product_category_id', ['type' => 'bigint']);
(new \Atk4\Schema\Migration($model))->dropIfExists()->create();
$model->import([
    ['id' => 1, 'xxx4_name' => 'Gravie', 'xxx2_product_category_id' => 1],
    ['id' => 2, 'xxx4_name' => 'Spread', 'xxx2_product_category_id' => 1],
    ['id' => 3, 'xxx4_name' => 'Salad Dressing', 'xxx2_product_category_id' => 1],
    ['id' => 4, 'xxx4_name' => 'Alcoholic', 'xxx2_product_category_id' => 2],
    ['id' => 5, 'xxx4_name' => 'Coffee and Tea', 'xxx2_product_category_id' => 2],
    ['id' => 6, 'xxx4_name' => 'Lowfat Milk', 'xxx2_product_category_id' => 3],
    ['id' => 7, 'xxx4_name' => 'Yogourt', 'xxx2_product_category_id' => 3],
    ['id' => 8, 'xxx4_name' => 'HighFat', 'xxx2_product_category_id' => 3],
    ['id' => 9, 'xxx4_name' => 'Sugar/Sweetened', 'xxx2_product_category_id' => 2],
]);

$model = new Model($persistence, ['table' => 'product']);
$model->addField('xxx5_name', ['type' => 'string']);
$model->addField('xxx_brand', ['type' => 'string']);
$model->addField('xxx_product_category_id', ['type' => 'bigint']);
$model->addField('xxx_product_sub_category_id', ['type' => 'bigint']);
(new \Atk4\Schema\Migration($model))->dropIfExists()->create();
$model->import([
    ['id' => 1, 'xxx5_name' => 'Mustard', 'xxx_brand' => 'Condiment Corp.', 'xxx_product_category_id' => 1, 'xxx_product_sub_category_id' => 2],
    ['id' => 2, 'xxx5_name' => 'Ketchup', 'xxx_brand' => 'Condiment Corp.', 'xxx_product_category_id' => 1, 'xxx_product_sub_category_id' => 2],
    ['id' => 3, 'xxx5_name' => 'Cola', 'xxx_brand' => 'Beverage Corp.', 'xxx_product_category_id' => 2, 'xxx_product_sub_category_id' => 9],
    ['id' => 4, 'xxx5_name' => 'Soda', 'xxx_brand' => 'Beverage Corp.', 'xxx_product_category_id' => 2, 'xxx_product_sub_category_id' => 9],
    ['id' => 5, 'xxx5_name' => 'Milk 2%', 'xxx_brand' => 'Milk Corp.', 'xxx_product_category_id' => 3, 'xxx_product_sub_category_id' => 8],
    ['id' => 6, 'xxx5_name' => 'Milk 1%', 'xxx_brand' => 'Milk Corp.', 'xxx_product_category_id' => 3, 'xxx_product_sub_category_id' => 6],
    ['id' => 7, 'xxx5_name' => 'Ice Cream', 'xxx_brand' => 'Milk Corp.', 'xxx_product_category_id' => 3, 'xxx_product_sub_category_id' => 8],
]);

echo 'import complete!' . "\n";

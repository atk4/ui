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

$model = new Model($persistence, 'client');
$model->addField('name', ['type' => 'string']);
$model->addField('addresses', ['type' => 'text']);
$model->addField('accounts', ['type' => 'text']);
(new \Atk4\Schema\Migration($model))->dropIfExists()->create();
$model->import([
    ['id' => 1, 'name' => 'John', 'addresses' => null, 'accounts' => null],
    ['id' => 2, 'name' => 'Jane', 'addresses' => null, 'accounts' => null],
]);

$model = new Model($persistence, 'country');
$model->addField('iso', ['type' => 'string']); // should be CHAR(2) NOT NULL
$model->addField('name', ['type' => 'string']);
$model->addField('nicename', ['type' => 'string']);
$model->addField('iso3', ['type' => 'string']); // should be CHAR(3) NOT NULL
$model->addField('numcode', ['type' => 'smallint']);
$model->addField('phonecode', ['type' => 'integer']);
(new \Atk4\Schema\Migration($model))->dropIfExists()->create();
$model->import([
    ['id' => 1, 'iso' => 'AF', 'name' => 'AFGHANISTAN', 'nicename' => 'Afghanistan', 'iso3' => 'AFG', 'numcode' => 4, 'phonecode' => 93],
    ['id' => 2, 'iso' => 'AL', 'name' => 'ALBANIA', 'nicename' => 'Albania', 'iso3' => 'ALB', 'numcode' => 8, 'phonecode' => 355],
    ['id' => 3, 'iso' => 'DZ', 'name' => 'ALGERIA', 'nicename' => 'Algeria', 'iso3' => 'DZA', 'numcode' => 12, 'phonecode' => 213],
    ['id' => 4, 'iso' => 'AS', 'name' => 'AMERICAN SAMOA', 'nicename' => 'American Samoa', 'iso3' => 'ASM', 'numcode' => 16, 'phonecode' => 1684],
    ['id' => 5, 'iso' => 'AD', 'name' => 'ANDORRA', 'nicename' => 'Andorra', 'iso3' => 'AND', 'numcode' => 20, 'phonecode' => 376],
    ['id' => 6, 'iso' => 'AO', 'name' => 'ANGOLA', 'nicename' => 'Angola', 'iso3' => 'AGO', 'numcode' => 24, 'phonecode' => 244],
    ['id' => 7, 'iso' => 'AI', 'name' => 'ANGUILLA', 'nicename' => 'Anguilla', 'iso3' => 'AIA', 'numcode' => 660, 'phonecode' => 1264],
    ['id' => 8, 'iso' => 'AQ', 'name' => 'ANTARCTICA', 'nicename' => 'Antarctica', 'iso3' => '', 'numcode' => '', 'phonecode' => 0],
    ['id' => 9, 'iso' => 'AG', 'name' => 'ANTIGUA AND BARBUDA', 'nicename' => 'Antigua and Barbuda', 'iso3' => 'ATG', 'numcode' => 28, 'phonecode' => 1268],
    ['id' => 10, 'iso' => 'AR', 'name' => 'ARGENTINA', 'nicename' => 'Argentina', 'iso3' => 'ARG', 'numcode' => 32, 'phonecode' => 54],
    ['id' => 11, 'iso' => 'AM', 'name' => 'ARMENIA', 'nicename' => 'Armenia', 'iso3' => 'ARM', 'numcode' => 51, 'phonecode' => 374],
    ['id' => 12, 'iso' => 'AW', 'name' => 'ARUBA', 'nicename' => 'Aruba', 'iso3' => 'ABW', 'numcode' => 533, 'phonecode' => 297],
    ['id' => 13, 'iso' => 'AU', 'name' => 'AUSTRALIA', 'nicename' => 'Australia', 'iso3' => 'AUS', 'numcode' => 36, 'phonecode' => 61],
    ['id' => 14, 'iso' => 'AT', 'name' => 'AUSTRIA', 'nicename' => 'Austria', 'iso3' => 'AUT', 'numcode' => 40, 'phonecode' => 43],
    ['id' => 15, 'iso' => 'AZ', 'name' => 'AZERBAIJAN', 'nicename' => 'Azerbaijan', 'iso3' => 'AZE', 'numcode' => 31, 'phonecode' => 994],
    ['id' => 16, 'iso' => 'BS', 'name' => 'BAHAMAS', 'nicename' => 'Bahamas', 'iso3' => 'BHS', 'numcode' => 44, 'phonecode' => 1242],
    ['id' => 17, 'iso' => 'BH', 'name' => 'BAHRAIN', 'nicename' => 'Bahrain', 'iso3' => 'BHR', 'numcode' => 48, 'phonecode' => 973],
    ['id' => 18, 'iso' => 'BD', 'name' => 'BANGLADESH', 'nicename' => 'Bangladesh', 'iso3' => 'BGD', 'numcode' => 50, 'phonecode' => 880],
    ['id' => 19, 'iso' => 'BB', 'name' => 'BARBADOS', 'nicename' => 'Barbados', 'iso3' => 'BRB', 'numcode' => 52, 'phonecode' => 1246],
    ['id' => 20, 'iso' => 'BY', 'name' => 'BELARUS', 'nicename' => 'Belarus', 'iso3' => 'BLR', 'numcode' => 112, 'phonecode' => 375],
    ['id' => 21, 'iso' => 'BE', 'name' => 'BELGIUM', 'nicename' => 'Belgium', 'iso3' => 'BEL', 'numcode' => 56, 'phonecode' => 32],
    ['id' => 22, 'iso' => 'BZ', 'name' => 'BELIZE', 'nicename' => 'Belize', 'iso3' => 'BLZ', 'numcode' => 84, 'phonecode' => 501],
    ['id' => 23, 'iso' => 'BJ', 'name' => 'BENIN', 'nicename' => 'Benin', 'iso3' => 'BEN', 'numcode' => 204, 'phonecode' => 229],
    ['id' => 24, 'iso' => 'BM', 'name' => 'BERMUDA', 'nicename' => 'Bermuda', 'iso3' => 'BMU', 'numcode' => 60, 'phonecode' => 1441],
    ['id' => 25, 'iso' => 'BT', 'name' => 'BHUTAN', 'nicename' => 'Bhutan', 'iso3' => 'BTN', 'numcode' => 64, 'phonecode' => 975],
    ['id' => 26, 'iso' => 'BO', 'name' => 'BOLIVIA', 'nicename' => 'Bolivia', 'iso3' => 'BOL', 'numcode' => 68, 'phonecode' => 591],
    ['id' => 27, 'iso' => 'BA', 'name' => 'BOSNIA AND HERZEGOVINA', 'nicename' => 'Bosnia and Herzegovina', 'iso3' => 'BIH', 'numcode' => 70, 'phonecode' => 387],
    ['id' => 28, 'iso' => 'BW', 'name' => 'BOTSWANA', 'nicename' => 'Botswana', 'iso3' => 'BWA', 'numcode' => 72, 'phonecode' => 267],
    ['id' => 29, 'iso' => 'BV', 'name' => 'BOUVET ISLAND', 'nicename' => 'Bouvet Island', 'iso3' => '', 'numcode' => '', 'phonecode' => 0],
    ['id' => 30, 'iso' => 'BR', 'name' => 'BRAZIL', 'nicename' => 'Brazil', 'iso3' => 'BRA', 'numcode' => 76, 'phonecode' => 55],
    ['id' => 31, 'iso' => 'IO', 'name' => 'BRITISH INDIAN OCEAN TERRITORY', 'nicename' => 'British Indian Ocean Territory', 'iso3' => '', 'numcode' => '', 'phonecode' => 246],
    ['id' => 32, 'iso' => 'BN', 'name' => 'BRUNEI DARUSSALAM', 'nicename' => 'Brunei Darussalam', 'iso3' => 'BRN', 'numcode' => 96, 'phonecode' => 673],
    ['id' => 33, 'iso' => 'BG', 'name' => 'BULGARIA', 'nicename' => 'Bulgaria', 'iso3' => 'BGR', 'numcode' => 100, 'phonecode' => 359],
    ['id' => 34, 'iso' => 'BF', 'name' => 'BURKINA FASO', 'nicename' => 'Burkina Faso', 'iso3' => 'BFA', 'numcode' => 854, 'phonecode' => 226],
    ['id' => 35, 'iso' => 'BI', 'name' => 'BURUNDI', 'nicename' => 'Burundi', 'iso3' => 'BDI', 'numcode' => 108, 'phonecode' => 257],
    ['id' => 36, 'iso' => 'KH', 'name' => 'CAMBODIA', 'nicename' => 'Cambodia', 'iso3' => 'KHM', 'numcode' => 116, 'phonecode' => 855],
    ['id' => 37, 'iso' => 'CM', 'name' => 'CAMEROON', 'nicename' => 'Cameroon', 'iso3' => 'CMR', 'numcode' => 120, 'phonecode' => 237],
    ['id' => 38, 'iso' => 'CA', 'name' => 'CANADA', 'nicename' => 'Canada', 'iso3' => 'CAN', 'numcode' => 124, 'phonecode' => 1],
    ['id' => 39, 'iso' => 'CV', 'name' => 'CAPE VERDE', 'nicename' => 'Cape Verde', 'iso3' => 'CPV', 'numcode' => 132, 'phonecode' => 238],
    ['id' => 40, 'iso' => 'KY', 'name' => 'CAYMAN ISLANDS', 'nicename' => 'Cayman Islands', 'iso3' => 'CYM', 'numcode' => 136, 'phonecode' => 1345],
    ['id' => 41, 'iso' => 'CF', 'name' => 'CENTRAL AFRICAN REPUBLIC', 'nicename' => 'Central African Republic', 'iso3' => 'CAF', 'numcode' => 140, 'phonecode' => 236],
    ['id' => 42, 'iso' => 'TD', 'name' => 'CHAD', 'nicename' => 'Chad', 'iso3' => 'TCD', 'numcode' => 148, 'phonecode' => 235],
    ['id' => 43, 'iso' => 'CL', 'name' => 'CHILE', 'nicename' => 'Chile', 'iso3' => 'CHL', 'numcode' => 152, 'phonecode' => 56],
    ['id' => 44, 'iso' => 'CN', 'name' => 'CHINA', 'nicename' => 'China', 'iso3' => 'CHN', 'numcode' => 156, 'phonecode' => 86],
    ['id' => 45, 'iso' => 'CX', 'name' => 'CHRISTMAS ISLAND', 'nicename' => 'Christmas Island', 'iso3' => '', 'numcode' => '', 'phonecode' => 61],
    ['id' => 46, 'iso' => 'CC', 'name' => 'COCOS (KEELING) ISLANDS', 'nicename' => 'Cocos (Keeling) Islands', 'iso3' => '', 'numcode' => '', 'phonecode' => 672],
    ['id' => 47, 'iso' => 'CO', 'name' => 'COLOMBIA', 'nicename' => 'Colombia', 'iso3' => 'COL', 'numcode' => 170, 'phonecode' => 57],
    ['id' => 48, 'iso' => 'KM', 'name' => 'COMOROS', 'nicename' => 'Comoros', 'iso3' => 'COM', 'numcode' => 174, 'phonecode' => 269],
    ['id' => 49, 'iso' => 'CG', 'name' => 'CONGO', 'nicename' => 'Congo', 'iso3' => 'COG', 'numcode' => 178, 'phonecode' => 242],
    ['id' => 50, 'iso' => 'CD', 'name' => 'CONGO, THE DEMOCRATIC REPUBLIC OF THE', 'nicename' => 'Congo, the Democratic Republic of the', 'iso3' => 'COD', 'numcode' => 180, 'phonecode' => 243],
    ['id' => 51, 'iso' => 'CK', 'name' => 'COOK ISLANDS', 'nicename' => 'Cook Islands', 'iso3' => 'COK', 'numcode' => 184, 'phonecode' => 682],
    ['id' => 52, 'iso' => 'CR', 'name' => 'COSTA RICA', 'nicename' => 'Costa Rica', 'iso3' => 'CRI', 'numcode' => 188, 'phonecode' => 506],
    ['id' => 53, 'iso' => 'CI', 'name' => 'COTE D\'IVOIRE', 'nicename' => 'Cote D\'Ivoire', 'iso3' => 'CIV', 'numcode' => 384, 'phonecode' => 225],
    ['id' => 54, 'iso' => 'HR', 'name' => 'CROATIA', 'nicename' => 'Croatia', 'iso3' => 'HRV', 'numcode' => 191, 'phonecode' => 385],
    ['id' => 55, 'iso' => 'CU', 'name' => 'CUBA', 'nicename' => 'Cuba', 'iso3' => 'CUB', 'numcode' => 192, 'phonecode' => 53],
    ['id' => 56, 'iso' => 'CY', 'name' => 'CYPRUS', 'nicename' => 'Cyprus', 'iso3' => 'CYP', 'numcode' => 196, 'phonecode' => 357],
    ['id' => 57, 'iso' => 'CZ', 'name' => 'CZECH REPUBLIC', 'nicename' => 'Czech Republic', 'iso3' => 'CZE', 'numcode' => 203, 'phonecode' => 420],
    ['id' => 58, 'iso' => 'DK', 'name' => 'DENMARK', 'nicename' => 'Denmark', 'iso3' => 'DNK', 'numcode' => 208, 'phonecode' => 45],
    ['id' => 59, 'iso' => 'DJ', 'name' => 'DJIBOUTI', 'nicename' => 'Djibouti', 'iso3' => 'DJI', 'numcode' => 262, 'phonecode' => 253],
    ['id' => 60, 'iso' => 'DM', 'name' => 'DOMINICA', 'nicename' => 'Dominica', 'iso3' => 'DMA', 'numcode' => 212, 'phonecode' => 1767],
    ['id' => 61, 'iso' => 'DO', 'name' => 'DOMINICAN REPUBLIC', 'nicename' => 'Dominican Republic', 'iso3' => 'DOM', 'numcode' => 214, 'phonecode' => 1809],
    ['id' => 62, 'iso' => 'EC', 'name' => 'ECUADOR', 'nicename' => 'Ecuador', 'iso3' => 'ECU', 'numcode' => 218, 'phonecode' => 593],
    ['id' => 63, 'iso' => 'EG', 'name' => 'EGYPT', 'nicename' => 'Egypt', 'iso3' => 'EGY', 'numcode' => 818, 'phonecode' => 20],
    ['id' => 64, 'iso' => 'SV', 'name' => 'EL SALVADOR', 'nicename' => 'El Salvador', 'iso3' => 'SLV', 'numcode' => 222, 'phonecode' => 503],
    ['id' => 65, 'iso' => 'GQ', 'name' => 'EQUATORIAL GUINEA', 'nicename' => 'Equatorial Guinea', 'iso3' => 'GNQ', 'numcode' => 226, 'phonecode' => 240],
    ['id' => 66, 'iso' => 'ER', 'name' => 'ERITREA', 'nicename' => 'Eritrea', 'iso3' => 'ERI', 'numcode' => 232, 'phonecode' => 291],
    ['id' => 67, 'iso' => 'EE', 'name' => 'ESTONIA', 'nicename' => 'Estonia', 'iso3' => 'EST', 'numcode' => 233, 'phonecode' => 372],
    ['id' => 68, 'iso' => 'ET', 'name' => 'ETHIOPIA', 'nicename' => 'Ethiopia', 'iso3' => 'ETH', 'numcode' => 231, 'phonecode' => 251],
    ['id' => 69, 'iso' => 'FK', 'name' => 'FALKLAND ISLANDS (MALVINAS)', 'nicename' => 'Falkland Islands (Malvinas)', 'iso3' => 'FLK', 'numcode' => 238, 'phonecode' => 500],
    ['id' => 70, 'iso' => 'FO', 'name' => 'FAROE ISLANDS', 'nicename' => 'Faroe Islands', 'iso3' => 'FRO', 'numcode' => 234, 'phonecode' => 298],
    ['id' => 71, 'iso' => 'FJ', 'name' => 'FIJI', 'nicename' => 'Fiji', 'iso3' => 'FJI', 'numcode' => 242, 'phonecode' => 679],
    ['id' => 72, 'iso' => 'FI', 'name' => 'FINLAND', 'nicename' => 'Finland', 'iso3' => 'FIN', 'numcode' => 246, 'phonecode' => 358],
    ['id' => 73, 'iso' => 'FR', 'name' => 'FRANCE', 'nicename' => 'France', 'iso3' => 'FRA', 'numcode' => 250, 'phonecode' => 33],
    ['id' => 74, 'iso' => 'GF', 'name' => 'FRENCH GUIANA', 'nicename' => 'French Guiana', 'iso3' => 'GUF', 'numcode' => 254, 'phonecode' => 594],
    ['id' => 75, 'iso' => 'PF', 'name' => 'FRENCH POLYNESIA', 'nicename' => 'French Polynesia', 'iso3' => 'PYF', 'numcode' => 258, 'phonecode' => 689],
    ['id' => 76, 'iso' => 'TF', 'name' => 'FRENCH SOUTHERN TERRITORIES', 'nicename' => 'French Southern Territories', 'iso3' => '', 'numcode' => '', 'phonecode' => 0],
    ['id' => 77, 'iso' => 'GA', 'name' => 'GABON', 'nicename' => 'Gabon', 'iso3' => 'GAB', 'numcode' => 266, 'phonecode' => 241],
    ['id' => 78, 'iso' => 'GM', 'name' => 'GAMBIA', 'nicename' => 'Gambia', 'iso3' => 'GMB', 'numcode' => 270, 'phonecode' => 220],
    ['id' => 79, 'iso' => 'GE', 'name' => 'GEORGIA', 'nicename' => 'Georgia', 'iso3' => 'GEO', 'numcode' => 268, 'phonecode' => 995],
    ['id' => 80, 'iso' => 'DE', 'name' => 'GERMANY', 'nicename' => 'Germany', 'iso3' => 'DEU', 'numcode' => 276, 'phonecode' => 49],
    ['id' => 81, 'iso' => 'GH', 'name' => 'GHANA', 'nicename' => 'Ghana', 'iso3' => 'GHA', 'numcode' => 288, 'phonecode' => 233],
    ['id' => 82, 'iso' => 'GI', 'name' => 'GIBRALTAR', 'nicename' => 'Gibraltar', 'iso3' => 'GIB', 'numcode' => 292, 'phonecode' => 350],
    ['id' => 83, 'iso' => 'GR', 'name' => 'GREECE', 'nicename' => 'Greece', 'iso3' => 'GRC', 'numcode' => 300, 'phonecode' => 30],
    ['id' => 84, 'iso' => 'GL', 'name' => 'GREENLAND', 'nicename' => 'Greenland', 'iso3' => 'GRL', 'numcode' => 304, 'phonecode' => 299],
    ['id' => 85, 'iso' => 'GD', 'name' => 'GRENADA', 'nicename' => 'Grenada', 'iso3' => 'GRD', 'numcode' => 308, 'phonecode' => 1473],
    ['id' => 86, 'iso' => 'GP', 'name' => 'GUADELOUPE', 'nicename' => 'Guadeloupe', 'iso3' => 'GLP', 'numcode' => 312, 'phonecode' => 590],
    ['id' => 87, 'iso' => 'GU', 'name' => 'GUAM', 'nicename' => 'Guam', 'iso3' => 'GUM', 'numcode' => 316, 'phonecode' => 1671],
    ['id' => 88, 'iso' => 'GT', 'name' => 'GUATEMALA', 'nicename' => 'Guatemala', 'iso3' => 'GTM', 'numcode' => 320, 'phonecode' => 502],
    ['id' => 89, 'iso' => 'GN', 'name' => 'GUINEA', 'nicename' => 'Guinea', 'iso3' => 'GIN', 'numcode' => 324, 'phonecode' => 224],
    ['id' => 90, 'iso' => 'GW', 'name' => 'GUINEA-BISSAU', 'nicename' => 'Guinea-Bissau', 'iso3' => 'GNB', 'numcode' => 624, 'phonecode' => 245],
    ['id' => 91, 'iso' => 'GY', 'name' => 'GUYANA', 'nicename' => 'Guyana', 'iso3' => 'GUY', 'numcode' => 328, 'phonecode' => 592],
    ['id' => 92, 'iso' => 'HT', 'name' => 'HAITI', 'nicename' => 'Haiti', 'iso3' => 'HTI', 'numcode' => 332, 'phonecode' => 509],
    ['id' => 93, 'iso' => 'HM', 'name' => 'HEARD ISLAND AND MCDONALD ISLANDS', 'nicename' => 'Heard Island and Mcdonald Islands', 'iso3' => '', 'numcode' => '', 'phonecode' => 0],
    ['id' => 94, 'iso' => 'VA', 'name' => 'HOLY SEE (VATICAN CITY STATE)', 'nicename' => 'Holy See (Vatican City State)', 'iso3' => 'VAT', 'numcode' => 336, 'phonecode' => 39],
    ['id' => 95, 'iso' => 'HN', 'name' => 'HONDURAS', 'nicename' => 'Honduras', 'iso3' => 'HND', 'numcode' => 340, 'phonecode' => 504],
    ['id' => 96, 'iso' => 'HK', 'name' => 'HONG KONG', 'nicename' => 'Hong Kong', 'iso3' => 'HKG', 'numcode' => 344, 'phonecode' => 852],
    ['id' => 97, 'iso' => 'HU', 'name' => 'HUNGARY', 'nicename' => 'Hungary', 'iso3' => 'HUN', 'numcode' => 348, 'phonecode' => 36],
    ['id' => 98, 'iso' => 'IS', 'name' => 'ICELAND', 'nicename' => 'Iceland', 'iso3' => 'ISL', 'numcode' => 352, 'phonecode' => 354],
    ['id' => 99, 'iso' => 'IN', 'name' => 'INDIA', 'nicename' => 'India', 'iso3' => 'IND', 'numcode' => 356, 'phonecode' => 91],
    ['id' => 100, 'iso' => 'ID', 'name' => 'INDONESIA', 'nicename' => 'Indonesia', 'iso3' => 'IDN', 'numcode' => 360, 'phonecode' => 62],
    ['id' => 101, 'iso' => 'IR', 'name' => 'IRAN, ISLAMIC REPUBLIC OF', 'nicename' => 'Iran, Islamic Republic of', 'iso3' => 'IRN', 'numcode' => 364, 'phonecode' => 98],
    ['id' => 102, 'iso' => 'IQ', 'name' => 'IRAQ', 'nicename' => 'Iraq', 'iso3' => 'IRQ', 'numcode' => 368, 'phonecode' => 964],
    ['id' => 103, 'iso' => 'IE', 'name' => 'IRELAND', 'nicename' => 'Ireland', 'iso3' => 'IRL', 'numcode' => 372, 'phonecode' => 353],
    ['id' => 104, 'iso' => 'IL', 'name' => 'ISRAEL', 'nicename' => 'Israel', 'iso3' => 'ISR', 'numcode' => 376, 'phonecode' => 972],
    ['id' => 105, 'iso' => 'IT', 'name' => 'ITALY', 'nicename' => 'Italy', 'iso3' => 'ITA', 'numcode' => 380, 'phonecode' => 39],
    ['id' => 106, 'iso' => 'JM', 'name' => 'JAMAICA', 'nicename' => 'Jamaica', 'iso3' => 'JAM', 'numcode' => 388, 'phonecode' => 1876],
    ['id' => 107, 'iso' => 'JP', 'name' => 'JAPAN', 'nicename' => 'Japan', 'iso3' => 'JPN', 'numcode' => 392, 'phonecode' => 81],
    ['id' => 108, 'iso' => 'JO', 'name' => 'JORDAN', 'nicename' => 'Jordan', 'iso3' => 'JOR', 'numcode' => 400, 'phonecode' => 962],
    ['id' => 109, 'iso' => 'KZ', 'name' => 'KAZAKHSTAN', 'nicename' => 'Kazakhstan', 'iso3' => 'KAZ', 'numcode' => 398, 'phonecode' => 7],
    ['id' => 110, 'iso' => 'KE', 'name' => 'KENYA', 'nicename' => 'Kenya', 'iso3' => 'KEN', 'numcode' => 404, 'phonecode' => 254],
    ['id' => 111, 'iso' => 'KI', 'name' => 'KIRIBATI', 'nicename' => 'Kiribati', 'iso3' => 'KIR', 'numcode' => 296, 'phonecode' => 686],
    ['id' => 112, 'iso' => 'KP', 'name' => 'KOREA, DEMOCRATIC PEOPLE\'S REPUBLIC OF', 'nicename' => 'Korea, Democratic People\'s Republic of', 'iso3' => 'PRK', 'numcode' => 408, 'phonecode' => 850],
    ['id' => 113, 'iso' => 'KR', 'name' => 'KOREA, REPUBLIC OF', 'nicename' => 'Korea, Republic of', 'iso3' => 'KOR', 'numcode' => 410, 'phonecode' => 82],
    ['id' => 114, 'iso' => 'KW', 'name' => 'KUWAIT', 'nicename' => 'Kuwait', 'iso3' => 'KWT', 'numcode' => 414, 'phonecode' => 965],
    ['id' => 115, 'iso' => 'KG', 'name' => 'KYRGYZSTAN', 'nicename' => 'Kyrgyzstan', 'iso3' => 'KGZ', 'numcode' => 417, 'phonecode' => 996],
    ['id' => 116, 'iso' => 'LA', 'name' => 'LAO PEOPLE\'S DEMOCRATIC REPUBLIC', 'nicename' => 'Lao People\'s Democratic Republic', 'iso3' => 'LAO', 'numcode' => 418, 'phonecode' => 856],
    ['id' => 117, 'iso' => 'LV', 'name' => 'LATVIA', 'nicename' => 'Latvia', 'iso3' => 'LVA', 'numcode' => 428, 'phonecode' => 371],
    ['id' => 118, 'iso' => 'LB', 'name' => 'LEBANON', 'nicename' => 'Lebanon', 'iso3' => 'LBN', 'numcode' => 422, 'phonecode' => 961],
    ['id' => 119, 'iso' => 'LS', 'name' => 'LESOTHO', 'nicename' => 'Lesotho', 'iso3' => 'LSO', 'numcode' => 426, 'phonecode' => 266],
    ['id' => 120, 'iso' => 'LR', 'name' => 'LIBERIA', 'nicename' => 'Liberia', 'iso3' => 'LBR', 'numcode' => 430, 'phonecode' => 231],
    ['id' => 121, 'iso' => 'LY', 'name' => 'LIBYAN ARAB JAMAHIRIYA', 'nicename' => 'Libyan Arab Jamahiriya', 'iso3' => 'LBY', 'numcode' => 434, 'phonecode' => 218],
    ['id' => 122, 'iso' => 'LI', 'name' => 'LIECHTENSTEIN', 'nicename' => 'Liechtenstein', 'iso3' => 'LIE', 'numcode' => 438, 'phonecode' => 423],
    ['id' => 123, 'iso' => 'LT', 'name' => 'LITHUANIA', 'nicename' => 'Lithuania', 'iso3' => 'LTU', 'numcode' => 440, 'phonecode' => 370],
    ['id' => 124, 'iso' => 'LU', 'name' => 'LUXEMBOURG', 'nicename' => 'Luxembourg', 'iso3' => 'LUX', 'numcode' => 442, 'phonecode' => 352],
    ['id' => 125, 'iso' => 'MO', 'name' => 'MACAO', 'nicename' => 'Macao', 'iso3' => 'MAC', 'numcode' => 446, 'phonecode' => 853],
    ['id' => 126, 'iso' => 'MK', 'name' => 'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF', 'nicename' => 'Macedonia, the Former Yugoslav Republic of', 'iso3' => 'MKD', 'numcode' => 807, 'phonecode' => 389],
    ['id' => 127, 'iso' => 'MG', 'name' => 'MADAGASCAR', 'nicename' => 'Madagascar', 'iso3' => 'MDG', 'numcode' => 450, 'phonecode' => 261],
    ['id' => 128, 'iso' => 'MW', 'name' => 'MALAWI', 'nicename' => 'Malawi', 'iso3' => 'MWI', 'numcode' => 454, 'phonecode' => 265],
    ['id' => 129, 'iso' => 'MY', 'name' => 'MALAYSIA', 'nicename' => 'Malaysia', 'iso3' => 'MYS', 'numcode' => 458, 'phonecode' => 60],
    ['id' => 130, 'iso' => 'MV', 'name' => 'MALDIVES', 'nicename' => 'Maldives', 'iso3' => 'MDV', 'numcode' => 462, 'phonecode' => 960],
    ['id' => 131, 'iso' => 'ML', 'name' => 'MALI', 'nicename' => 'Mali', 'iso3' => 'MLI', 'numcode' => 466, 'phonecode' => 223],
    ['id' => 132, 'iso' => 'MT', 'name' => 'MALTA', 'nicename' => 'Malta', 'iso3' => 'MLT', 'numcode' => 470, 'phonecode' => 356],
    ['id' => 133, 'iso' => 'MH', 'name' => 'MARSHALL ISLANDS', 'nicename' => 'Marshall Islands', 'iso3' => 'MHL', 'numcode' => 584, 'phonecode' => 692],
    ['id' => 134, 'iso' => 'MQ', 'name' => 'MARTINIQUE', 'nicename' => 'Martinique', 'iso3' => 'MTQ', 'numcode' => 474, 'phonecode' => 596],
    ['id' => 135, 'iso' => 'MR', 'name' => 'MAURITANIA', 'nicename' => 'Mauritania', 'iso3' => 'MRT', 'numcode' => 478, 'phonecode' => 222],
    ['id' => 136, 'iso' => 'MU', 'name' => 'MAURITIUS', 'nicename' => 'Mauritius', 'iso3' => 'MUS', 'numcode' => 480, 'phonecode' => 230],
    ['id' => 137, 'iso' => 'YT', 'name' => 'MAYOTTE', 'nicename' => 'Mayotte', 'iso3' => '', 'numcode' => '', 'phonecode' => 269],
    ['id' => 138, 'iso' => 'MX', 'name' => 'MEXICO', 'nicename' => 'Mexico', 'iso3' => 'MEX', 'numcode' => 484, 'phonecode' => 52],
    ['id' => 139, 'iso' => 'FM', 'name' => 'MICRONESIA, FEDERATED STATES OF', 'nicename' => 'Micronesia, Federated States of', 'iso3' => 'FSM', 'numcode' => 583, 'phonecode' => 691],
    ['id' => 140, 'iso' => 'MD', 'name' => 'MOLDOVA, REPUBLIC OF', 'nicename' => 'Moldova, Republic of', 'iso3' => 'MDA', 'numcode' => 498, 'phonecode' => 373],
    ['id' => 141, 'iso' => 'MC', 'name' => 'MONACO', 'nicename' => 'Monaco', 'iso3' => 'MCO', 'numcode' => 492, 'phonecode' => 377],
    ['id' => 142, 'iso' => 'MN', 'name' => 'MONGOLIA', 'nicename' => 'Mongolia', 'iso3' => 'MNG', 'numcode' => 496, 'phonecode' => 976],
    ['id' => 143, 'iso' => 'MS', 'name' => 'MONTSERRAT', 'nicename' => 'Montserrat', 'iso3' => 'MSR', 'numcode' => 500, 'phonecode' => 1664],
    ['id' => 144, 'iso' => 'MA', 'name' => 'MOROCCO', 'nicename' => 'Morocco', 'iso3' => 'MAR', 'numcode' => 504, 'phonecode' => 212],
    ['id' => 145, 'iso' => 'MZ', 'name' => 'MOZAMBIQUE', 'nicename' => 'Mozambique', 'iso3' => 'MOZ', 'numcode' => 508, 'phonecode' => 258],
    ['id' => 146, 'iso' => 'MM', 'name' => 'MYANMAR', 'nicename' => 'Myanmar', 'iso3' => 'MMR', 'numcode' => 104, 'phonecode' => 95],
    ['id' => 147, 'iso' => 'NA', 'name' => 'NAMIBIA', 'nicename' => 'Namibia', 'iso3' => 'NAM', 'numcode' => 516, 'phonecode' => 264],
    ['id' => 148, 'iso' => 'NR', 'name' => 'NAURU', 'nicename' => 'Nauru', 'iso3' => 'NRU', 'numcode' => 520, 'phonecode' => 674],
    ['id' => 149, 'iso' => 'NP', 'name' => 'NEPAL', 'nicename' => 'Nepal', 'iso3' => 'NPL', 'numcode' => 524, 'phonecode' => 977],
    ['id' => 150, 'iso' => 'NL', 'name' => 'NETHERLANDS', 'nicename' => 'Netherlands', 'iso3' => 'NLD', 'numcode' => 528, 'phonecode' => 31],
    ['id' => 151, 'iso' => 'AN', 'name' => 'NETHERLANDS ANTILLES', 'nicename' => 'Netherlands Antilles', 'iso3' => 'ANT', 'numcode' => 530, 'phonecode' => 599],
    ['id' => 152, 'iso' => 'NC', 'name' => 'NEW CALEDONIA', 'nicename' => 'New Caledonia', 'iso3' => 'NCL', 'numcode' => 540, 'phonecode' => 687],
    ['id' => 153, 'iso' => 'NZ', 'name' => 'NEW ZEALAND', 'nicename' => 'New Zealand', 'iso3' => 'NZL', 'numcode' => 554, 'phonecode' => 64],
    ['id' => 154, 'iso' => 'NI', 'name' => 'NICARAGUA', 'nicename' => 'Nicaragua', 'iso3' => 'NIC', 'numcode' => 558, 'phonecode' => 505],
    ['id' => 155, 'iso' => 'NE', 'name' => 'NIGER', 'nicename' => 'Niger', 'iso3' => 'NER', 'numcode' => 562, 'phonecode' => 227],
    ['id' => 156, 'iso' => 'NG', 'name' => 'NIGERIA', 'nicename' => 'Nigeria', 'iso3' => 'NGA', 'numcode' => 566, 'phonecode' => 234],
    ['id' => 157, 'iso' => 'NU', 'name' => 'NIUE', 'nicename' => 'Niue', 'iso3' => 'NIU', 'numcode' => 570, 'phonecode' => 683],
    ['id' => 158, 'iso' => 'NF', 'name' => 'NORFOLK ISLAND', 'nicename' => 'Norfolk Island', 'iso3' => 'NFK', 'numcode' => 574, 'phonecode' => 672],
    ['id' => 159, 'iso' => 'MP', 'name' => 'NORTHERN MARIANA ISLANDS', 'nicename' => 'Northern Mariana Islands', 'iso3' => 'MNP', 'numcode' => 580, 'phonecode' => 1670],
    ['id' => 160, 'iso' => 'NO', 'name' => 'NORWAY', 'nicename' => 'Norway', 'iso3' => 'NOR', 'numcode' => 578, 'phonecode' => 47],
    ['id' => 161, 'iso' => 'OM', 'name' => 'OMAN', 'nicename' => 'Oman', 'iso3' => 'OMN', 'numcode' => 512, 'phonecode' => 968],
    ['id' => 162, 'iso' => 'PK', 'name' => 'PAKISTAN', 'nicename' => 'Pakistan', 'iso3' => 'PAK', 'numcode' => 586, 'phonecode' => 92],
    ['id' => 163, 'iso' => 'PW', 'name' => 'PALAU', 'nicename' => 'Palau', 'iso3' => 'PLW', 'numcode' => 585, 'phonecode' => 680],
    ['id' => 164, 'iso' => 'PS', 'name' => 'PALESTINIAN TERRITORY, OCCUPIED', 'nicename' => 'Palestinian Territory, Occupied', 'iso3' => '', 'numcode' => '', 'phonecode' => 970],
    ['id' => 165, 'iso' => 'PA', 'name' => 'PANAMA', 'nicename' => 'Panama', 'iso3' => 'PAN', 'numcode' => 591, 'phonecode' => 507],
    ['id' => 166, 'iso' => 'PG', 'name' => 'PAPUA NEW GUINEA', 'nicename' => 'Papua New Guinea', 'iso3' => 'PNG', 'numcode' => 598, 'phonecode' => 675],
    ['id' => 167, 'iso' => 'PY', 'name' => 'PARAGUAY', 'nicename' => 'Paraguay', 'iso3' => 'PRY', 'numcode' => 600, 'phonecode' => 595],
    ['id' => 168, 'iso' => 'PE', 'name' => 'PERU', 'nicename' => 'Peru', 'iso3' => 'PER', 'numcode' => 604, 'phonecode' => 51],
    ['id' => 169, 'iso' => 'PH', 'name' => 'PHILIPPINES', 'nicename' => 'Philippines', 'iso3' => 'PHL', 'numcode' => 608, 'phonecode' => 63],
    ['id' => 170, 'iso' => 'PN', 'name' => 'PITCAIRN', 'nicename' => 'Pitcairn', 'iso3' => 'PCN', 'numcode' => 612, 'phonecode' => 0],
    ['id' => 171, 'iso' => 'PL', 'name' => 'POLAND', 'nicename' => 'Poland', 'iso3' => 'POL', 'numcode' => 616, 'phonecode' => 48],
    ['id' => 172, 'iso' => 'PT', 'name' => 'PORTUGAL', 'nicename' => 'Portugal', 'iso3' => 'PRT', 'numcode' => 620, 'phonecode' => 351],
    ['id' => 173, 'iso' => 'PR', 'name' => 'PUERTO RICO', 'nicename' => 'Puerto Rico', 'iso3' => 'PRI', 'numcode' => 630, 'phonecode' => 1787],
    ['id' => 174, 'iso' => 'QA', 'name' => 'QATAR', 'nicename' => 'Qatar', 'iso3' => 'QAT', 'numcode' => 634, 'phonecode' => 974],
    ['id' => 175, 'iso' => 'RE', 'name' => 'REUNION', 'nicename' => 'Reunion', 'iso3' => 'REU', 'numcode' => 638, 'phonecode' => 262],
    ['id' => 176, 'iso' => 'RO', 'name' => 'ROMANIA', 'nicename' => 'Romania', 'iso3' => 'ROM', 'numcode' => 642, 'phonecode' => 40],
    ['id' => 177, 'iso' => 'RU', 'name' => 'RUSSIAN FEDERATION', 'nicename' => 'Russian Federation', 'iso3' => 'RUS', 'numcode' => 643, 'phonecode' => 7],
    ['id' => 178, 'iso' => 'RW', 'name' => 'RWANDA', 'nicename' => 'Rwanda', 'iso3' => 'RWA', 'numcode' => 646, 'phonecode' => 250],
    ['id' => 179, 'iso' => 'SH', 'name' => 'SAINT HELENA', 'nicename' => 'Saint Helena', 'iso3' => 'SHN', 'numcode' => 654, 'phonecode' => 290],
    ['id' => 180, 'iso' => 'KN', 'name' => 'SAINT KITTS AND NEVIS', 'nicename' => 'Saint Kitts and Nevis', 'iso3' => 'KNA', 'numcode' => 659, 'phonecode' => 1869],
    ['id' => 181, 'iso' => 'LC', 'name' => 'SAINT LUCIA', 'nicename' => 'Saint Lucia', 'iso3' => 'LCA', 'numcode' => 662, 'phonecode' => 1758],
    ['id' => 182, 'iso' => 'PM', 'name' => 'SAINT PIERRE AND MIQUELON', 'nicename' => 'Saint Pierre and Miquelon', 'iso3' => 'SPM', 'numcode' => 666, 'phonecode' => 508],
    ['id' => 183, 'iso' => 'VC', 'name' => 'SAINT VINCENT AND THE GRENADINES', 'nicename' => 'Saint Vincent and the Grenadines', 'iso3' => 'VCT', 'numcode' => 670, 'phonecode' => 1784],
    ['id' => 184, 'iso' => 'WS', 'name' => 'SAMOA', 'nicename' => 'Samoa', 'iso3' => 'WSM', 'numcode' => 882, 'phonecode' => 684],
    ['id' => 185, 'iso' => 'SM', 'name' => 'SAN MARINO', 'nicename' => 'San Marino', 'iso3' => 'SMR', 'numcode' => 674, 'phonecode' => 378],
    ['id' => 186, 'iso' => 'ST', 'name' => 'SAO TOME AND PRINCIPE', 'nicename' => 'Sao Tome and Principe', 'iso3' => 'STP', 'numcode' => 678, 'phonecode' => 239],
    ['id' => 187, 'iso' => 'SA', 'name' => 'SAUDI ARABIA', 'nicename' => 'Saudi Arabia', 'iso3' => 'SAU', 'numcode' => 682, 'phonecode' => 966],
    ['id' => 188, 'iso' => 'SN', 'name' => 'SENEGAL', 'nicename' => 'Senegal', 'iso3' => 'SEN', 'numcode' => 686, 'phonecode' => 221],
    ['id' => 190, 'iso' => 'SC', 'name' => 'SEYCHELLES', 'nicename' => 'Seychelles', 'iso3' => 'SYC', 'numcode' => 690, 'phonecode' => 248],
    ['id' => 191, 'iso' => 'SL', 'name' => 'SIERRA LEONE', 'nicename' => 'Sierra Leone', 'iso3' => 'SLE', 'numcode' => 694, 'phonecode' => 232],
    ['id' => 192, 'iso' => 'SG', 'name' => 'SINGAPORE', 'nicename' => 'Singapore', 'iso3' => 'SGP', 'numcode' => 702, 'phonecode' => 65],
    ['id' => 193, 'iso' => 'SK', 'name' => 'SLOVAKIA', 'nicename' => 'Slovakia', 'iso3' => 'SVK', 'numcode' => 703, 'phonecode' => 421],
    ['id' => 194, 'iso' => 'SI', 'name' => 'SLOVENIA', 'nicename' => 'Slovenia', 'iso3' => 'SVN', 'numcode' => 705, 'phonecode' => 386],
    ['id' => 195, 'iso' => 'SB', 'name' => 'SOLOMON ISLANDS', 'nicename' => 'Solomon Islands', 'iso3' => 'SLB', 'numcode' => 90, 'phonecode' => 677],
    ['id' => 196, 'iso' => 'SO', 'name' => 'SOMALIA', 'nicename' => 'Somalia', 'iso3' => 'SOM', 'numcode' => 706, 'phonecode' => 252],
    ['id' => 197, 'iso' => 'ZA', 'name' => 'SOUTH AFRICA', 'nicename' => 'South Africa', 'iso3' => 'ZAF', 'numcode' => 710, 'phonecode' => 27],
    ['id' => 198, 'iso' => 'GS', 'name' => 'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS', 'nicename' => 'South Georgia and the South Sandwich Islands', 'iso3' => '', 'numcode' => '', 'phonecode' => 0],
    ['id' => 199, 'iso' => 'ES', 'name' => 'SPAIN', 'nicename' => 'Spain', 'iso3' => 'ESP', 'numcode' => 724, 'phonecode' => 34],
    ['id' => 200, 'iso' => 'LK', 'name' => 'SRI LANKA', 'nicename' => 'Sri Lanka', 'iso3' => 'LKA', 'numcode' => 144, 'phonecode' => 94],
    ['id' => 201, 'iso' => 'SD', 'name' => 'SUDAN', 'nicename' => 'Sudan', 'iso3' => 'SDN', 'numcode' => 736, 'phonecode' => 249],
    ['id' => 202, 'iso' => 'SR', 'name' => 'SURINAME', 'nicename' => 'Suriname', 'iso3' => 'SUR', 'numcode' => 740, 'phonecode' => 597],
    ['id' => 203, 'iso' => 'SJ', 'name' => 'SVALBARD AND JAN MAYEN', 'nicename' => 'Svalbard and Jan Mayen', 'iso3' => 'SJM', 'numcode' => 744, 'phonecode' => 47],
    ['id' => 204, 'iso' => 'SZ', 'name' => 'SWAZILAND', 'nicename' => 'Swaziland', 'iso3' => 'SWZ', 'numcode' => 748, 'phonecode' => 268],
    ['id' => 205, 'iso' => 'SE', 'name' => 'SWEDEN', 'nicename' => 'Sweden', 'iso3' => 'SWE', 'numcode' => 752, 'phonecode' => 46],
    ['id' => 206, 'iso' => 'CH', 'name' => 'SWITZERLAND', 'nicename' => 'Switzerland', 'iso3' => 'CHE', 'numcode' => 756, 'phonecode' => 41],
    ['id' => 207, 'iso' => 'SY', 'name' => 'SYRIAN ARAB REPUBLIC', 'nicename' => 'Syrian Arab Republic', 'iso3' => 'SYR', 'numcode' => 760, 'phonecode' => 963],
    ['id' => 208, 'iso' => 'TW', 'name' => 'TAIWAN, PROVINCE OF CHINA', 'nicename' => 'Taiwan, Province of China', 'iso3' => 'TWN', 'numcode' => 158, 'phonecode' => 886],
    ['id' => 209, 'iso' => 'TJ', 'name' => 'TAJIKISTAN', 'nicename' => 'Tajikistan', 'iso3' => 'TJK', 'numcode' => 762, 'phonecode' => 992],
    ['id' => 210, 'iso' => 'TZ', 'name' => 'TANZANIA, UNITED REPUBLIC OF', 'nicename' => 'Tanzania, United Republic of', 'iso3' => 'TZA', 'numcode' => 834, 'phonecode' => 255],
    ['id' => 211, 'iso' => 'TH', 'name' => 'THAILAND', 'nicename' => 'Thailand', 'iso3' => 'THA', 'numcode' => 764, 'phonecode' => 66],
    ['id' => 212, 'iso' => 'TL', 'name' => 'TIMOR-LESTE', 'nicename' => 'Timor-Leste', 'iso3' => '', 'numcode' => '', 'phonecode' => 670],
    ['id' => 213, 'iso' => 'TG', 'name' => 'TOGO', 'nicename' => 'Togo', 'iso3' => 'TGO', 'numcode' => 768, 'phonecode' => 228],
    ['id' => 214, 'iso' => 'TK', 'name' => 'TOKELAU', 'nicename' => 'Tokelau', 'iso3' => 'TKL', 'numcode' => 772, 'phonecode' => 690],
    ['id' => 215, 'iso' => 'TO', 'name' => 'TONGA', 'nicename' => 'Tonga', 'iso3' => 'TON', 'numcode' => 776, 'phonecode' => 676],
    ['id' => 216, 'iso' => 'TT', 'name' => 'TRINIDAD AND TOBAGO', 'nicename' => 'Trinidad and Tobago', 'iso3' => 'TTO', 'numcode' => 780, 'phonecode' => 1868],
    ['id' => 217, 'iso' => 'TN', 'name' => 'TUNISIA', 'nicename' => 'Tunisia', 'iso3' => 'TUN', 'numcode' => 788, 'phonecode' => 216],
    ['id' => 218, 'iso' => 'TR', 'name' => 'TURKEY', 'nicename' => 'Turkey', 'iso3' => 'TUR', 'numcode' => 792, 'phonecode' => 90],
    ['id' => 219, 'iso' => 'TM', 'name' => 'TURKMENISTAN', 'nicename' => 'Turkmenistan', 'iso3' => 'TKM', 'numcode' => 795, 'phonecode' => 7370],
    ['id' => 220, 'iso' => 'TC', 'name' => 'TURKS AND CAICOS ISLANDS', 'nicename' => 'Turks and Caicos Islands', 'iso3' => 'TCA', 'numcode' => 796, 'phonecode' => 1649],
    ['id' => 221, 'iso' => 'TV', 'name' => 'TUVALU', 'nicename' => 'Tuvalu', 'iso3' => 'TUV', 'numcode' => 798, 'phonecode' => 688],
    ['id' => 222, 'iso' => 'UG', 'name' => 'UGANDA', 'nicename' => 'Uganda', 'iso3' => 'UGA', 'numcode' => 800, 'phonecode' => 256],
    ['id' => 223, 'iso' => 'UA', 'name' => 'UKRAINE', 'nicename' => 'Ukraine', 'iso3' => 'UKR', 'numcode' => 804, 'phonecode' => 380],
    ['id' => 224, 'iso' => 'AE', 'name' => 'UNITED ARAB EMIRATES', 'nicename' => 'United Arab Emirates', 'iso3' => 'ARE', 'numcode' => 784, 'phonecode' => 971],
    ['id' => 225, 'iso' => 'GB', 'name' => 'UNITED KINGDOM', 'nicename' => 'United Kingdom', 'iso3' => 'GBR', 'numcode' => 826, 'phonecode' => 44],
    ['id' => 226, 'iso' => 'US', 'name' => 'UNITED STATES', 'nicename' => 'United States', 'iso3' => 'USA', 'numcode' => 840, 'phonecode' => 1],
    ['id' => 227, 'iso' => 'UM', 'name' => 'UNITED STATES MINOR OUTLYING ISLANDS', 'nicename' => 'United States Minor Outlying Islands', 'iso3' => '', 'numcode' => '', 'phonecode' => 1],
    ['id' => 228, 'iso' => 'UY', 'name' => 'URUGUAY', 'nicename' => 'Uruguay', 'iso3' => 'URY', 'numcode' => 858, 'phonecode' => 598],
    ['id' => 229, 'iso' => 'UZ', 'name' => 'UZBEKISTAN', 'nicename' => 'Uzbekistan', 'iso3' => 'UZB', 'numcode' => 860, 'phonecode' => 998],
    ['id' => 230, 'iso' => 'VU', 'name' => 'VANUATU', 'nicename' => 'Vanuatu', 'iso3' => 'VUT', 'numcode' => 548, 'phonecode' => 678],
    ['id' => 231, 'iso' => 'VE', 'name' => 'VENEZUELA', 'nicename' => 'Venezuela', 'iso3' => 'VEN', 'numcode' => 862, 'phonecode' => 58],
    ['id' => 232, 'iso' => 'VN', 'name' => 'VIET NAM', 'nicename' => 'Viet Nam', 'iso3' => 'VNM', 'numcode' => 704, 'phonecode' => 84],
    ['id' => 233, 'iso' => 'VG', 'name' => 'VIRGIN ISLANDS, BRITISH', 'nicename' => 'Virgin Islands, British', 'iso3' => 'VGB', 'numcode' => 92, 'phonecode' => 1284],
    ['id' => 234, 'iso' => 'VI', 'name' => 'VIRGIN ISLANDS, U.S.', 'nicename' => 'Virgin Islands, U.s.', 'iso3' => 'VIR', 'numcode' => 850, 'phonecode' => 1340],
    ['id' => 235, 'iso' => 'WF', 'name' => 'WALLIS AND FUTUNA', 'nicename' => 'Wallis and Futuna', 'iso3' => 'WLF', 'numcode' => 876, 'phonecode' => 681],
    ['id' => 236, 'iso' => 'EH', 'name' => 'WESTERN SAHARA', 'nicename' => 'Western Sahara', 'iso3' => 'ESH', 'numcode' => 732, 'phonecode' => 212],
    ['id' => 237, 'iso' => 'YE', 'name' => 'YEMEN', 'nicename' => 'Yemen', 'iso3' => 'YEM', 'numcode' => 887, 'phonecode' => 967],
    ['id' => 238, 'iso' => 'ZM', 'name' => 'ZAMBIA', 'nicename' => 'Zambia', 'iso3' => 'ZMB', 'numcode' => 894, 'phonecode' => 260],
    ['id' => 239, 'iso' => 'ZW', 'name' => 'ZIMBABWE', 'nicename' => 'Zimbabwe', 'iso3' => 'ZWE', 'numcode' => 716, 'phonecode' => 263],
    ['id' => 240, 'iso' => 'RS', 'name' => 'SERBIA', 'nicename' => 'Serbia', 'iso3' => 'SRB', 'numcode' => 688, 'phonecode' => 381],
    ['id' => 241, 'iso' => 'AP', 'name' => 'ASIA PACIFIC REGION', 'nicename' => 'Asia / Pacific Region', 'iso3' => '0', 'numcode' => 0, 'phonecode' => 0],
    ['id' => 242, 'iso' => 'ME', 'name' => 'MONTENEGRO', 'nicename' => 'Montenegro', 'iso3' => 'MNE', 'numcode' => 499, 'phonecode' => 382],
    ['id' => 243, 'iso' => 'AX', 'name' => 'ALAND ISLANDS', 'nicename' => 'Aland Islands', 'iso3' => 'ALA', 'numcode' => 248, 'phonecode' => 358],
    ['id' => 244, 'iso' => 'BQ', 'name' => 'BONAIRE, SINT EUSTATIUS AND SABA', 'nicename' => 'Bonaire, Sint Eustatius and Saba', 'iso3' => 'BES', 'numcode' => 535, 'phonecode' => 599],
    ['id' => 245, 'iso' => 'CW', 'name' => 'CURACAO', 'nicename' => 'Curacao', 'iso3' => 'CUW', 'numcode' => 531, 'phonecode' => 599],
    ['id' => 246, 'iso' => 'GG', 'name' => 'GUERNSEY', 'nicename' => 'Guernsey', 'iso3' => 'GGY', 'numcode' => 831, 'phonecode' => 44],
    ['id' => 247, 'iso' => 'IM', 'name' => 'ISLE OF MAN', 'nicename' => 'Isle of Man', 'iso3' => 'IMN', 'numcode' => 833, 'phonecode' => 44],
    ['id' => 248, 'iso' => 'JE', 'name' => 'JERSEY', 'nicename' => 'Jersey', 'iso3' => 'JEY', 'numcode' => 832, 'phonecode' => 44],
    ['id' => 249, 'iso' => 'XK', 'name' => 'KOSOVO', 'nicename' => 'Kosovo', 'iso3' => '---', 'numcode' => 0, 'phonecode' => 381],
    ['id' => 250, 'iso' => 'BL', 'name' => 'SAINT BARTHELEMY', 'nicename' => 'Saint Barthelemy', 'iso3' => 'BLM', 'numcode' => 652, 'phonecode' => 590],
    ['id' => 251, 'iso' => 'MF', 'name' => 'SAINT MARTIN', 'nicename' => 'Saint Martin', 'iso3' => 'MAF', 'numcode' => 663, 'phonecode' => 590],
    ['id' => 252, 'iso' => 'SX', 'name' => 'SINT MAARTEN', 'nicename' => 'Sint Maarten', 'iso3' => 'SXM', 'numcode' => 534, 'phonecode' => 1],
    ['id' => 253, 'iso' => 'SS', 'name' => 'SOUTH SUDAN', 'nicename' => 'South Sudan', 'iso3' => 'SSD', 'numcode' => 728, 'phonecode' => 211],
]);

$model = new Model($persistence, 'file');
$model->addField('name', ['type' => 'string']);
$model->addField('type', ['type' => 'string']);
$model->addField('is_folder', ['type' => 'boolean']);
$model->addField('parent_folder_id', ['type' => 'bigint']);
// KEY `fk_file_file_idx` (`parent_folder_id`)
(new \Atk4\Schema\Migration($model))->dropIfExists()->create();
$model->import([
    ['id' => 1, 'name' => 'phpunit.xml', 'type' => 'xml', 'is_folder' => 0, 'parent_folder_id' => null],
    ['id' => 2, 'name' => 'LICENSE', 'type' => '', 'is_folder' => 0, 'parent_folder_id' => null],
    ['id' => 3, 'name' => 'Makefile', 'type' => '', 'is_folder' => 0, 'parent_folder_id' => null],
    ['id' => 4, 'name' => 'tests', 'type' => '', 'is_folder' => 1, 'parent_folder_id' => null],
    ['id' => 5, 'name' => 'TemplateTest.php', 'type' => 'php', 'is_folder' => 0, 'parent_folder_id' => 4],
    ['id' => 6, 'name' => 'template', 'type' => '', 'is_folder' => 1, 'parent_folder_id' => null],
    ['id' => 7, 'name' => 'semantic-ui', 'type' => '', 'is_folder' => 1, 'parent_folder_id' => 6],
    ['id' => 8, 'name' => 'tree.html', 'type' => 'html', 'is_folder' => 0, 'parent_folder_id' => 7],
    ['id' => 9, 'name' => 'element.html', 'type' => 'html', 'is_folder' => 0, 'parent_folder_id' => 7],
    ['id' => 10, 'name' => 'button.html', 'type' => 'html', 'is_folder' => 0, 'parent_folder_id' => 7],
    ['id' => 11, 'name' => 'icon.html', 'type' => 'html', 'is_folder' => 0, 'parent_folder_id' => 7],
    ['id' => 12, 'name' => 'element.jade', 'type' => 'jade', 'is_folder' => 0, 'parent_folder_id' => 7],
    ['id' => 13, 'name' => 'tree.jade', 'type' => 'jade', 'is_folder' => 0, 'parent_folder_id' => 7],
    ['id' => 14, 'name' => 'icon.jade', 'type' => 'jade', 'is_folder' => 0, 'parent_folder_id' => 7],
    ['id' => 15, 'name' => 'button.jade', 'type' => 'jade', 'is_folder' => 0, 'parent_folder_id' => 7],
    ['id' => 16, 'name' => 'docs', 'type' => '', 'is_folder' => 1, 'parent_folder_id' => null],
    ['id' => 17, 'name' => 'index.rst', 'type' => 'rst', 'is_folder' => 0, 'parent_folder_id' => 16],
    ['id' => 18, 'name' => 'login.png', 'type' => 'png', 'is_folder' => 0, 'parent_folder_id' => 16],
    ['id' => 19, 'name' => 'requirements.txt', 'type' => 'txt', 'is_folder' => 0, 'parent_folder_id' => 16],
    ['id' => 20, 'name' => 'crud2.png', 'type' => 'png', 'is_folder' => 0, 'parent_folder_id' => 16],
    ['id' => 21, 'name' => 'images', 'type' => '', 'is_folder' => 1, 'parent_folder_id' => 16],
    ['id' => 22, 'name' => 'folders.png', 'type' => 'png', 'is_folder' => 0, 'parent_folder_id' => 21],
    ['id' => 23, 'name' => 'layout.png', 'type' => 'png', 'is_folder' => 0, 'parent_folder_id' => 21],
    ['id' => 24, 'name' => 'menu.png', 'type' => 'png', 'is_folder' => 0, 'parent_folder_id' => 21],
    ['id' => 25, 'name' => 'Makefile', 'type' => '', 'is_folder' => 0, 'parent_folder_id' => 16],
    ['id' => 26, 'name' => 'conf.py', 'type' => 'py', 'is_folder' => 0, 'parent_folder_id' => 16],
    ['id' => 27, 'name' => 'README.md', 'type' => 'md', 'is_folder' => 0, 'parent_folder_id' => 16],
    ['id' => 28, 'name' => 'quickstart.rst', 'type' => 'rst', 'is_folder' => 0, 'parent_folder_id' => 16],
    ['id' => 29, 'name' => 'crud.png', 'type' => 'png', 'is_folder' => 0, 'parent_folder_id' => 16],
    ['id' => 30, 'name' => 'layouts.png', 'type' => 'png', 'is_folder' => 0, 'parent_folder_id' => 16],
    ['id' => 31, 'name' => 'template.rst', 'type' => 'rst', 'is_folder' => 0, 'parent_folder_id' => 16],
    ['id' => 32, 'name' => 'README.md', 'type' => 'md', 'is_folder' => 0, 'parent_folder_id' => null],
    ['id' => 33, 'name' => 'demos', 'type' => '', 'is_folder' => 1, 'parent_folder_id' => null],
    ['id' => 34, 'name' => 'index.php', 'type' => 'php', 'is_folder' => 0, 'parent_folder_id' => 33],
    ['id' => 35, 'name' => 'layout.php', 'type' => 'php', 'is_folder' => 0, 'parent_folder_id' => 33],
    ['id' => 36, 'name' => 'templates', 'type' => '', 'is_folder' => 1, 'parent_folder_id' => 33],
    ['id' => 37, 'name' => 'fixed.jade', 'type' => 'jade', 'is_folder' => 0, 'parent_folder_id' => 36],
    ['id' => 38, 'name' => 'layout1.html', 'type' => 'html', 'is_folder' => 0, 'parent_folder_id' => 36],
    ['id' => 39, 'name' => 'layout2.jade', 'type' => 'jade', 'is_folder' => 0, 'parent_folder_id' => 36],
    ['id' => 40, 'name' => 'layout1.jade', 'type' => 'jade', 'is_folder' => 0, 'parent_folder_id' => 36],
    ['id' => 41, 'name' => 'fixed.html', 'type' => 'html', 'is_folder' => 0, 'parent_folder_id' => 36],
    ['id' => 42, 'name' => 'index1.html', 'type' => 'html', 'is_folder' => 0, 'parent_folder_id' => 36],
    ['id' => 43, 'name' => 'layout2.html', 'type' => 'html', 'is_folder' => 0, 'parent_folder_id' => 36],
    ['id' => 44, 'name' => 'button.php', 'type' => 'php', 'is_folder' => 0, 'parent_folder_id' => 33],
    ['id' => 45, 'name' => 'composer.json', 'type' => 'json', 'is_folder' => 0, 'parent_folder_id' => null],
    ['id' => 46, 'name' => 'src', 'type' => '', 'is_folder' => 1, 'parent_folder_id' => null],
    ['id' => 47, 'name' => 'Icon.php', 'type' => 'php', 'is_folder' => 0, 'parent_folder_id' => 46],
    ['id' => 48, 'name' => 'App.php', 'type' => 'php', 'is_folder' => 0, 'parent_folder_id' => 46],
    ['id' => 49, 'name' => 'Label.php', 'type' => 'php', 'is_folder' => 0, 'parent_folder_id' => 46],
    ['id' => 50, 'name' => 'Layout', 'type' => '', 'is_folder' => 1, 'parent_folder_id' => 46],
    ['id' => 51, 'name' => 'App.php', 'type' => 'php', 'is_folder' => 0, 'parent_folder_id' => 50],
    ['id' => 52, 'name' => 'MiniApp.php', 'type' => 'php', 'is_folder' => 0, 'parent_folder_id' => 46],
    ['id' => 53, 'name' => 'Lister.php', 'type' => 'php', 'is_folder' => 0, 'parent_folder_id' => 46],
    ['id' => 54, 'name' => 'Layout.php', 'type' => 'php', 'is_folder' => 0, 'parent_folder_id' => 46],
    ['id' => 55, 'name' => 'Buttons.php', 'type' => 'php', 'is_folder' => 0, 'parent_folder_id' => 46],
    ['id' => 56, 'name' => 'View.php', 'type' => 'php', 'is_folder' => 0, 'parent_folder_id' => 46],
    ['id' => 57, 'name' => 'Tree.php', 'type' => 'php', 'is_folder' => 0, 'parent_folder_id' => 46],
    ['id' => 58, 'name' => 'Template.php', 'type' => 'php', 'is_folder' => 0, 'parent_folder_id' => 46],
    ['id' => 59, 'name' => 'Exception.php', 'type' => 'php', 'is_folder' => 0, 'parent_folder_id' => 46],
    ['id' => 60, 'name' => 'Text.php', 'type' => 'php', 'is_folder' => 0, 'parent_folder_id' => 46],
    ['id' => 61, 'name' => 'Button.php', 'type' => 'php', 'is_folder' => 0, 'parent_folder_id' => 46],
]);

$model = new Model($persistence, 'stats');
$model->addField('project_name', ['type' => 'string']);
$model->addField('project_code', ['type' => 'string']);
$model->addField('description', ['type' => 'text']);
$model->addField('client_name', ['type' => 'string']);
$model->addField('client_address', ['type' => 'text']);
$model->addField('client_country_iso', ['type' => 'string']); // should be CHAR(2)
$model->addField('is_commercial', ['type' => 'boolean']);
$model->addField('currency', ['type' => 'string']); // should be ENUM('EUR' ,'USD', 'GBP') or CHAR(3)
$model->addField('is_completed', ['type' => 'boolean']);
$model->addField('project_budget', ['type' => 'float']);
$model->addField('project_invoiced', ['type' => 'float']);
$model->addField('project_paid', ['type' => 'float']);
$model->addField('project_hour_cost', ['type' => 'float']);
$model->addField('project_hours_est', ['type' => 'integer']);
$model->addField('project_hours_reported', ['type' => 'integer']);
$model->addField('project_expenses_est', ['type' => 'float']);
$model->addField('project_expenses', ['type' => 'float']);
$model->addField('project_mgmt_cost_pct', ['type' => 'float']);
$model->addField('project_qa_cost_pct', ['type' => 'float']);
$model->addField('start_date', ['type' => 'date']);
$model->addField('finish_date', ['type' => 'date']);
$model->addField('finish_time', ['type' => 'time']);
$model->addField('created', ['type' => 'datetime']);
$model->addField('updated', ['type' => 'datetime']);
(new \Atk4\Schema\Migration($model))->dropIfExists()->create();
$model->import([
    ['id' => 1, 'project_name' => 'Agile DSQL', 'project_code' => 'at01', 'description' => 'DSQL is a composable SQL query builder. You can write multi-vendor queries in PHP profiting from better security, clean syntax and avoid human errors.', 'client_name' => 'Agile Toolkit', 'client_address' => 'Some Street,\nGarden City\nUK', 'client_country_iso' => 'GB', 'is_commercial' => 0, 'currency' => 'GBP', 'is_completed' => 1, 'project_budget' => 7000, 'project_invoiced' => 0, 'project_paid' => 0, 'project_hour_cost' => 0, 'project_hours_est' => 150, 'project_hours_reported' => 125, 'project_expenses_est' => 50, 'project_expenses' => 0, 'project_mgmt_cost_pct' => 0.1, 'project_qa_cost_pct' => 0.2, 'start_date' => '2016-01-26', 'finish_date' => '2016-06-23', 'finish_time' => '12:50:00', 'created' => '2017-04-06 10:34:34', 'updated' => '2017-04-06 10:35:04'],
    ['id' => 2, 'project_name' => 'Agile Core', 'project_code' => 'at02', 'description' => 'Collection of PHP Traits for designing object-oriented frameworks.', 'client_name' => 'Agile Toolkit', 'client_address' => 'Some Street,\nGarden City\nUK', 'client_country_iso' => 'GB', 'is_commercial' => 0, 'currency' => 'GBP', 'is_completed' => 1, 'project_budget' => 3000, 'project_invoiced' => 0, 'project_paid' => 0, 'project_hour_cost' => 0, 'project_hours_est' => 70, 'project_hours_reported' => 56, 'project_expenses_est' => 50, 'project_expenses' => 0, 'project_mgmt_cost_pct' => 0.1, 'project_qa_cost_pct' => 0.2, 'start_date' => '2016-04-27', 'finish_date' => '2016-05-21', 'finish_time' => '18:41:00', 'created' => '2017-04-06 10:21:50', 'updated' => '2017-04-06 10:35:04'],
    ['id' => 3, 'project_name' => 'Agile Data', 'project_code' => 'at03', 'description' => 'Agile Data implements an entirely new pattern for data abstraction, that is specifically designed for remote databases such as RDS, Cloud SQL, BigQuery and other distributed data storage architectures. It focuses on reducing number of requests your App have to send to the Database by using more sophisticated queries while also offering full Domain Model mapping and Database vendor abstraction.', 'client_name' => 'Agile Toolkit', 'client_address' => 'Some Street,\nGarden City\nUK', 'client_country_iso' => 'GB', 'is_commercial' => 0, 'currency' => 'GBP', 'is_completed' => 1, 'project_budget' => 12000, 'project_invoiced' => 0, 'project_paid' => 0, 'project_hour_cost' => 0, 'project_hours_est' => 300, 'project_hours_reported' => 394, 'project_expenses_est' => 600, 'project_expenses' => 430, 'project_mgmt_cost_pct' => 0.2, 'project_qa_cost_pct' => 0.3, 'start_date' => '2016-04-17', 'finish_date' => '2016-06-20', 'finish_time' => '03:04:00', 'created' => '2017-04-06 10:30:15', 'updated' => '2017-04-06 10:35:04'],
    ['id' => 4, 'project_name' => 'Agile UI', 'project_code' => 'at04', 'description' => 'Web UI Component library.', 'client_name' => 'Agile Toolkit', 'client_address' => 'Some Street,\nGarden City\nUK', 'client_country_iso' => 'GB', 'is_commercial' => 0, 'currency' => 'GBP', 'is_completed' => 0, 'project_budget' => 20000, 'project_invoiced' => 0, 'project_paid' => 0, 'project_hour_cost' => 0, 'project_hours_est' => 600, 'project_hours_reported' => 368, 'project_expenses_est' => 1200, 'project_expenses' => 0, 'project_mgmt_cost_pct' => 0.3, 'project_qa_cost_pct' => 0.4, 'start_date' => '2016-09-17', 'finish_date' => '', 'finish_time' => '', 'created' => '2017-04-06 10:30:15', 'updated' => '2017-04-06 10:35:04'],
]);

$model = new Model($persistence, 'product_category');
$model->addField('name', ['type' => 'string']);
(new \Atk4\Schema\Migration($model))->dropIfExists()->create();
$model->import([
    ['id' => 1, 'name' => 'Condiments and Gravies'],
    ['id' => 2, 'name' => 'Beverages'],
    ['id' => 3, 'name' => 'Dairy'],
]);

$model = new Model($persistence, 'product_sub_category');
$model->addField('name', ['type' => 'string']);
$model->addField('product_category_id', ['type' => 'bigint']);
(new \Atk4\Schema\Migration($model))->dropIfExists()->create();
$model->import([
    ['id' => 1, 'name' => 'Gravie', 'product_category_id' => 1],
    ['id' => 2, 'name' => 'Spread', 'product_category_id' => 1],
    ['id' => 3, 'name' => 'Salad Dressing', 'product_category_id' => 1],
    ['id' => 4, 'name' => 'Alcoholic', 'product_category_id' => 2],
    ['id' => 5, 'name' => 'Coffee and Tea', 'product_category_id' => 2],
    ['id' => 6, 'name' => 'Lowfat Milk', 'product_category_id' => 3],
    ['id' => 7, 'name' => 'Yogourt', 'product_category_id' => 3],
    ['id' => 8, 'name' => 'HighFat', 'product_category_id' => 3],
    ['id' => 9, 'name' => 'Sugar/Sweetened', 'product_category_id' => 2],
]);

$model = new Model($persistence, 'product');
$model->addField('name', ['type' => 'string']);
$model->addField('brand', ['type' => 'string']);
$model->addField('product_category_id', ['type' => 'bigint']);
$model->addField('product_sub_category_id', ['type' => 'bigint']);
(new \Atk4\Schema\Migration($model))->dropIfExists()->create();
$model->import([
    ['id' => 1, 'name' => 'Mustard', 'brand' => 'Condiment Corp.', 'product_category_id' => 1, 'product_sub_category_id' => 2],
    ['id' => 2, 'name' => 'Ketchup', 'brand' => 'Condiment Corp.', 'product_category_id' => 1, 'product_sub_category_id' => 2],
    ['id' => 3, 'name' => 'Cola', 'brand' => 'Beverage Corp.', 'product_category_id' => 2, 'product_sub_category_id' => 9],
    ['id' => 4, 'name' => 'Soda', 'brand' => 'Beverage Corp.', 'product_category_id' => 2, 'product_sub_category_id' => 9],
    ['id' => 5, 'name' => 'Milk 2%', 'brand' => 'Milk Corp.', 'product_category_id' => 3, 'product_sub_category_id' => 8],
    ['id' => 6, 'name' => 'Milk 1%', 'brand' => 'Milk Corp.', 'product_category_id' => 3, 'product_sub_category_id' => 6],
    ['id' => 7, 'name' => 'Ice Cream', 'brand' => 'Milk Corp.', 'product_category_id' => 3, 'product_sub_category_id' => 8],
]);

echo 'import complete!' . "\n";

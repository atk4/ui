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
$model->addField('atk_fp_client__name', ['type' => 'string']);
$model->addField('atk_fp__addresses', ['type' => 'text']);
$model->addField('atk_fp__accounts', ['type' => 'text']);
(new \Atk4\Schema\Migration($model))->dropIfExists()->create();
$model->import([
    ['id' => 1, 'atk_fp_client__name' => 'John', 'atk_fp__addresses' => null, 'atk_fp__accounts' => null],
    ['id' => 2, 'atk_fp_client__name' => 'Jane', 'atk_fp__addresses' => null, 'atk_fp__accounts' => null],
]);

$model = new Model($persistence, ['table' => 'country']);
$model->addField('atk_fp__iso', ['type' => 'string']); // should be CHAR(2) NOT NULL
$model->addField('atk_fp_country__name', ['type' => 'string']);
$model->addField('atk_fp_country__nicename', ['type' => 'string']);
$model->addField('atk_fp__iso3', ['type' => 'string']); // should be CHAR(3) NOT NULL
$model->addField('atk_fp__numcode', ['type' => 'smallint']);
$model->addField('atk_fp__phonecode', ['type' => 'integer']);
(new \Atk4\Schema\Migration($model))->dropIfExists()->create();
$model->import([
    ['id' => 1, 'atk_fp__iso' => 'AF', 'atk_fp_country__name' => 'AFGHANISTAN', 'atk_fp_country__nicename' => 'Afghanistan', 'atk_fp__iso3' => 'AFG', 'atk_fp__numcode' => 4, 'atk_fp__phonecode' => 93],
    ['id' => 2, 'atk_fp__iso' => 'AL', 'atk_fp_country__name' => 'ALBANIA', 'atk_fp_country__nicename' => 'Albania', 'atk_fp__iso3' => 'ALB', 'atk_fp__numcode' => 8, 'atk_fp__phonecode' => 355],
    ['id' => 3, 'atk_fp__iso' => 'DZ', 'atk_fp_country__name' => 'ALGERIA', 'atk_fp_country__nicename' => 'Algeria', 'atk_fp__iso3' => 'DZA', 'atk_fp__numcode' => 12, 'atk_fp__phonecode' => 213],
    ['id' => 4, 'atk_fp__iso' => 'AS', 'atk_fp_country__name' => 'AMERICAN SAMOA', 'atk_fp_country__nicename' => 'American Samoa', 'atk_fp__iso3' => 'ASM', 'atk_fp__numcode' => 16, 'atk_fp__phonecode' => 1684],
    ['id' => 5, 'atk_fp__iso' => 'AD', 'atk_fp_country__name' => 'ANDORRA', 'atk_fp_country__nicename' => 'Andorra', 'atk_fp__iso3' => 'AND', 'atk_fp__numcode' => 20, 'atk_fp__phonecode' => 376],
    ['id' => 6, 'atk_fp__iso' => 'AO', 'atk_fp_country__name' => 'ANGOLA', 'atk_fp_country__nicename' => 'Angola', 'atk_fp__iso3' => 'AGO', 'atk_fp__numcode' => 24, 'atk_fp__phonecode' => 244],
    ['id' => 7, 'atk_fp__iso' => 'AI', 'atk_fp_country__name' => 'ANGUILLA', 'atk_fp_country__nicename' => 'Anguilla', 'atk_fp__iso3' => 'AIA', 'atk_fp__numcode' => 660, 'atk_fp__phonecode' => 1264],
    ['id' => 8, 'atk_fp__iso' => 'AQ', 'atk_fp_country__name' => 'ANTARCTICA', 'atk_fp_country__nicename' => 'Antarctica', 'atk_fp__iso3' => '', 'atk_fp__numcode' => '', 'atk_fp__phonecode' => 0],
    ['id' => 9, 'atk_fp__iso' => 'AG', 'atk_fp_country__name' => 'ANTIGUA AND BARBUDA', 'atk_fp_country__nicename' => 'Antigua and Barbuda', 'atk_fp__iso3' => 'ATG', 'atk_fp__numcode' => 28, 'atk_fp__phonecode' => 1268],
    ['id' => 10, 'atk_fp__iso' => 'AR', 'atk_fp_country__name' => 'ARGENTINA', 'atk_fp_country__nicename' => 'Argentina', 'atk_fp__iso3' => 'ARG', 'atk_fp__numcode' => 32, 'atk_fp__phonecode' => 54],
    ['id' => 11, 'atk_fp__iso' => 'AM', 'atk_fp_country__name' => 'ARMENIA', 'atk_fp_country__nicename' => 'Armenia', 'atk_fp__iso3' => 'ARM', 'atk_fp__numcode' => 51, 'atk_fp__phonecode' => 374],
    ['id' => 12, 'atk_fp__iso' => 'AW', 'atk_fp_country__name' => 'ARUBA', 'atk_fp_country__nicename' => 'Aruba', 'atk_fp__iso3' => 'ABW', 'atk_fp__numcode' => 533, 'atk_fp__phonecode' => 297],
    ['id' => 13, 'atk_fp__iso' => 'AU', 'atk_fp_country__name' => 'AUSTRALIA', 'atk_fp_country__nicename' => 'Australia', 'atk_fp__iso3' => 'AUS', 'atk_fp__numcode' => 36, 'atk_fp__phonecode' => 61],
    ['id' => 14, 'atk_fp__iso' => 'AT', 'atk_fp_country__name' => 'AUSTRIA', 'atk_fp_country__nicename' => 'Austria', 'atk_fp__iso3' => 'AUT', 'atk_fp__numcode' => 40, 'atk_fp__phonecode' => 43],
    ['id' => 15, 'atk_fp__iso' => 'AZ', 'atk_fp_country__name' => 'AZERBAIJAN', 'atk_fp_country__nicename' => 'Azerbaijan', 'atk_fp__iso3' => 'AZE', 'atk_fp__numcode' => 31, 'atk_fp__phonecode' => 994],
    ['id' => 16, 'atk_fp__iso' => 'BS', 'atk_fp_country__name' => 'BAHAMAS', 'atk_fp_country__nicename' => 'Bahamas', 'atk_fp__iso3' => 'BHS', 'atk_fp__numcode' => 44, 'atk_fp__phonecode' => 1242],
    ['id' => 17, 'atk_fp__iso' => 'BH', 'atk_fp_country__name' => 'BAHRAIN', 'atk_fp_country__nicename' => 'Bahrain', 'atk_fp__iso3' => 'BHR', 'atk_fp__numcode' => 48, 'atk_fp__phonecode' => 973],
    ['id' => 18, 'atk_fp__iso' => 'BD', 'atk_fp_country__name' => 'BANGLADESH', 'atk_fp_country__nicename' => 'Bangladesh', 'atk_fp__iso3' => 'BGD', 'atk_fp__numcode' => 50, 'atk_fp__phonecode' => 880],
    ['id' => 19, 'atk_fp__iso' => 'BB', 'atk_fp_country__name' => 'BARBADOS', 'atk_fp_country__nicename' => 'Barbados', 'atk_fp__iso3' => 'BRB', 'atk_fp__numcode' => 52, 'atk_fp__phonecode' => 1246],
    ['id' => 20, 'atk_fp__iso' => 'BY', 'atk_fp_country__name' => 'BELARUS', 'atk_fp_country__nicename' => 'Belarus', 'atk_fp__iso3' => 'BLR', 'atk_fp__numcode' => 112, 'atk_fp__phonecode' => 375],
    ['id' => 21, 'atk_fp__iso' => 'BE', 'atk_fp_country__name' => 'BELGIUM', 'atk_fp_country__nicename' => 'Belgium', 'atk_fp__iso3' => 'BEL', 'atk_fp__numcode' => 56, 'atk_fp__phonecode' => 32],
    ['id' => 22, 'atk_fp__iso' => 'BZ', 'atk_fp_country__name' => 'BELIZE', 'atk_fp_country__nicename' => 'Belize', 'atk_fp__iso3' => 'BLZ', 'atk_fp__numcode' => 84, 'atk_fp__phonecode' => 501],
    ['id' => 23, 'atk_fp__iso' => 'BJ', 'atk_fp_country__name' => 'BENIN', 'atk_fp_country__nicename' => 'Benin', 'atk_fp__iso3' => 'BEN', 'atk_fp__numcode' => 204, 'atk_fp__phonecode' => 229],
    ['id' => 24, 'atk_fp__iso' => 'BM', 'atk_fp_country__name' => 'BERMUDA', 'atk_fp_country__nicename' => 'Bermuda', 'atk_fp__iso3' => 'BMU', 'atk_fp__numcode' => 60, 'atk_fp__phonecode' => 1441],
    ['id' => 25, 'atk_fp__iso' => 'BT', 'atk_fp_country__name' => 'BHUTAN', 'atk_fp_country__nicename' => 'Bhutan', 'atk_fp__iso3' => 'BTN', 'atk_fp__numcode' => 64, 'atk_fp__phonecode' => 975],
    ['id' => 26, 'atk_fp__iso' => 'BO', 'atk_fp_country__name' => 'BOLIVIA', 'atk_fp_country__nicename' => 'Bolivia', 'atk_fp__iso3' => 'BOL', 'atk_fp__numcode' => 68, 'atk_fp__phonecode' => 591],
    ['id' => 27, 'atk_fp__iso' => 'BA', 'atk_fp_country__name' => 'BOSNIA AND HERZEGOVINA', 'atk_fp_country__nicename' => 'Bosnia and Herzegovina', 'atk_fp__iso3' => 'BIH', 'atk_fp__numcode' => 70, 'atk_fp__phonecode' => 387],
    ['id' => 28, 'atk_fp__iso' => 'BW', 'atk_fp_country__name' => 'BOTSWANA', 'atk_fp_country__nicename' => 'Botswana', 'atk_fp__iso3' => 'BWA', 'atk_fp__numcode' => 72, 'atk_fp__phonecode' => 267],
    ['id' => 29, 'atk_fp__iso' => 'BV', 'atk_fp_country__name' => 'BOUVET ISLAND', 'atk_fp_country__nicename' => 'Bouvet Island', 'atk_fp__iso3' => '', 'atk_fp__numcode' => '', 'atk_fp__phonecode' => 0],
    ['id' => 30, 'atk_fp__iso' => 'BR', 'atk_fp_country__name' => 'BRAZIL', 'atk_fp_country__nicename' => 'Brazil', 'atk_fp__iso3' => 'BRA', 'atk_fp__numcode' => 76, 'atk_fp__phonecode' => 55],
    ['id' => 31, 'atk_fp__iso' => 'IO', 'atk_fp_country__name' => 'BRITISH INDIAN OCEAN TERRITORY', 'atk_fp_country__nicename' => 'British Indian Ocean Territory', 'atk_fp__iso3' => '', 'atk_fp__numcode' => '', 'atk_fp__phonecode' => 246],
    ['id' => 32, 'atk_fp__iso' => 'BN', 'atk_fp_country__name' => 'BRUNEI DARUSSALAM', 'atk_fp_country__nicename' => 'Brunei Darussalam', 'atk_fp__iso3' => 'BRN', 'atk_fp__numcode' => 96, 'atk_fp__phonecode' => 673],
    ['id' => 33, 'atk_fp__iso' => 'BG', 'atk_fp_country__name' => 'BULGARIA', 'atk_fp_country__nicename' => 'Bulgaria', 'atk_fp__iso3' => 'BGR', 'atk_fp__numcode' => 100, 'atk_fp__phonecode' => 359],
    ['id' => 34, 'atk_fp__iso' => 'BF', 'atk_fp_country__name' => 'BURKINA FASO', 'atk_fp_country__nicename' => 'Burkina Faso', 'atk_fp__iso3' => 'BFA', 'atk_fp__numcode' => 854, 'atk_fp__phonecode' => 226],
    ['id' => 35, 'atk_fp__iso' => 'BI', 'atk_fp_country__name' => 'BURUNDI', 'atk_fp_country__nicename' => 'Burundi', 'atk_fp__iso3' => 'BDI', 'atk_fp__numcode' => 108, 'atk_fp__phonecode' => 257],
    ['id' => 36, 'atk_fp__iso' => 'KH', 'atk_fp_country__name' => 'CAMBODIA', 'atk_fp_country__nicename' => 'Cambodia', 'atk_fp__iso3' => 'KHM', 'atk_fp__numcode' => 116, 'atk_fp__phonecode' => 855],
    ['id' => 37, 'atk_fp__iso' => 'CM', 'atk_fp_country__name' => 'CAMEROON', 'atk_fp_country__nicename' => 'Cameroon', 'atk_fp__iso3' => 'CMR', 'atk_fp__numcode' => 120, 'atk_fp__phonecode' => 237],
    ['id' => 38, 'atk_fp__iso' => 'CA', 'atk_fp_country__name' => 'CANADA', 'atk_fp_country__nicename' => 'Canada', 'atk_fp__iso3' => 'CAN', 'atk_fp__numcode' => 124, 'atk_fp__phonecode' => 1],
    ['id' => 39, 'atk_fp__iso' => 'CV', 'atk_fp_country__name' => 'CAPE VERDE', 'atk_fp_country__nicename' => 'Cape Verde', 'atk_fp__iso3' => 'CPV', 'atk_fp__numcode' => 132, 'atk_fp__phonecode' => 238],
    ['id' => 40, 'atk_fp__iso' => 'KY', 'atk_fp_country__name' => 'CAYMAN ISLANDS', 'atk_fp_country__nicename' => 'Cayman Islands', 'atk_fp__iso3' => 'CYM', 'atk_fp__numcode' => 136, 'atk_fp__phonecode' => 1345],
    ['id' => 41, 'atk_fp__iso' => 'CF', 'atk_fp_country__name' => 'CENTRAL AFRICAN REPUBLIC', 'atk_fp_country__nicename' => 'Central African Republic', 'atk_fp__iso3' => 'CAF', 'atk_fp__numcode' => 140, 'atk_fp__phonecode' => 236],
    ['id' => 42, 'atk_fp__iso' => 'TD', 'atk_fp_country__name' => 'CHAD', 'atk_fp_country__nicename' => 'Chad', 'atk_fp__iso3' => 'TCD', 'atk_fp__numcode' => 148, 'atk_fp__phonecode' => 235],
    ['id' => 43, 'atk_fp__iso' => 'CL', 'atk_fp_country__name' => 'CHILE', 'atk_fp_country__nicename' => 'Chile', 'atk_fp__iso3' => 'CHL', 'atk_fp__numcode' => 152, 'atk_fp__phonecode' => 56],
    ['id' => 44, 'atk_fp__iso' => 'CN', 'atk_fp_country__name' => 'CHINA', 'atk_fp_country__nicename' => 'China', 'atk_fp__iso3' => 'CHN', 'atk_fp__numcode' => 156, 'atk_fp__phonecode' => 86],
    ['id' => 45, 'atk_fp__iso' => 'CX', 'atk_fp_country__name' => 'CHRISTMAS ISLAND', 'atk_fp_country__nicename' => 'Christmas Island', 'atk_fp__iso3' => '', 'atk_fp__numcode' => '', 'atk_fp__phonecode' => 61],
    ['id' => 46, 'atk_fp__iso' => 'CC', 'atk_fp_country__name' => 'COCOS (KEELING) ISLANDS', 'atk_fp_country__nicename' => 'Cocos (Keeling) Islands', 'atk_fp__iso3' => '', 'atk_fp__numcode' => '', 'atk_fp__phonecode' => 672],
    ['id' => 47, 'atk_fp__iso' => 'CO', 'atk_fp_country__name' => 'COLOMBIA', 'atk_fp_country__nicename' => 'Colombia', 'atk_fp__iso3' => 'COL', 'atk_fp__numcode' => 170, 'atk_fp__phonecode' => 57],
    ['id' => 48, 'atk_fp__iso' => 'KM', 'atk_fp_country__name' => 'COMOROS', 'atk_fp_country__nicename' => 'Comoros', 'atk_fp__iso3' => 'COM', 'atk_fp__numcode' => 174, 'atk_fp__phonecode' => 269],
    ['id' => 49, 'atk_fp__iso' => 'CG', 'atk_fp_country__name' => 'CONGO', 'atk_fp_country__nicename' => 'Congo', 'atk_fp__iso3' => 'COG', 'atk_fp__numcode' => 178, 'atk_fp__phonecode' => 242],
    ['id' => 50, 'atk_fp__iso' => 'CD', 'atk_fp_country__name' => 'CONGO, THE DEMOCRATIC REPUBLIC OF THE', 'atk_fp_country__nicename' => 'Congo, the Democratic Republic of the', 'atk_fp__iso3' => 'COD', 'atk_fp__numcode' => 180, 'atk_fp__phonecode' => 243],
    ['id' => 51, 'atk_fp__iso' => 'CK', 'atk_fp_country__name' => 'COOK ISLANDS', 'atk_fp_country__nicename' => 'Cook Islands', 'atk_fp__iso3' => 'COK', 'atk_fp__numcode' => 184, 'atk_fp__phonecode' => 682],
    ['id' => 52, 'atk_fp__iso' => 'CR', 'atk_fp_country__name' => 'COSTA RICA', 'atk_fp_country__nicename' => 'Costa Rica', 'atk_fp__iso3' => 'CRI', 'atk_fp__numcode' => 188, 'atk_fp__phonecode' => 506],
    ['id' => 53, 'atk_fp__iso' => 'CI', 'atk_fp_country__name' => 'COTE D\'IVOIRE', 'atk_fp_country__nicename' => 'Cote D\'Ivoire', 'atk_fp__iso3' => 'CIV', 'atk_fp__numcode' => 384, 'atk_fp__phonecode' => 225],
    ['id' => 54, 'atk_fp__iso' => 'HR', 'atk_fp_country__name' => 'CROATIA', 'atk_fp_country__nicename' => 'Croatia', 'atk_fp__iso3' => 'HRV', 'atk_fp__numcode' => 191, 'atk_fp__phonecode' => 385],
    ['id' => 55, 'atk_fp__iso' => 'CU', 'atk_fp_country__name' => 'CUBA', 'atk_fp_country__nicename' => 'Cuba', 'atk_fp__iso3' => 'CUB', 'atk_fp__numcode' => 192, 'atk_fp__phonecode' => 53],
    ['id' => 56, 'atk_fp__iso' => 'CY', 'atk_fp_country__name' => 'CYPRUS', 'atk_fp_country__nicename' => 'Cyprus', 'atk_fp__iso3' => 'CYP', 'atk_fp__numcode' => 196, 'atk_fp__phonecode' => 357],
    ['id' => 57, 'atk_fp__iso' => 'CZ', 'atk_fp_country__name' => 'CZECH REPUBLIC', 'atk_fp_country__nicename' => 'Czech Republic', 'atk_fp__iso3' => 'CZE', 'atk_fp__numcode' => 203, 'atk_fp__phonecode' => 420],
    ['id' => 58, 'atk_fp__iso' => 'DK', 'atk_fp_country__name' => 'DENMARK', 'atk_fp_country__nicename' => 'Denmark', 'atk_fp__iso3' => 'DNK', 'atk_fp__numcode' => 208, 'atk_fp__phonecode' => 45],
    ['id' => 59, 'atk_fp__iso' => 'DJ', 'atk_fp_country__name' => 'DJIBOUTI', 'atk_fp_country__nicename' => 'Djibouti', 'atk_fp__iso3' => 'DJI', 'atk_fp__numcode' => 262, 'atk_fp__phonecode' => 253],
    ['id' => 60, 'atk_fp__iso' => 'DM', 'atk_fp_country__name' => 'DOMINICA', 'atk_fp_country__nicename' => 'Dominica', 'atk_fp__iso3' => 'DMA', 'atk_fp__numcode' => 212, 'atk_fp__phonecode' => 1767],
    ['id' => 61, 'atk_fp__iso' => 'DO', 'atk_fp_country__name' => 'DOMINICAN REPUBLIC', 'atk_fp_country__nicename' => 'Dominican Republic', 'atk_fp__iso3' => 'DOM', 'atk_fp__numcode' => 214, 'atk_fp__phonecode' => 1809],
    ['id' => 62, 'atk_fp__iso' => 'EC', 'atk_fp_country__name' => 'ECUADOR', 'atk_fp_country__nicename' => 'Ecuador', 'atk_fp__iso3' => 'ECU', 'atk_fp__numcode' => 218, 'atk_fp__phonecode' => 593],
    ['id' => 63, 'atk_fp__iso' => 'EG', 'atk_fp_country__name' => 'EGYPT', 'atk_fp_country__nicename' => 'Egypt', 'atk_fp__iso3' => 'EGY', 'atk_fp__numcode' => 818, 'atk_fp__phonecode' => 20],
    ['id' => 64, 'atk_fp__iso' => 'SV', 'atk_fp_country__name' => 'EL SALVADOR', 'atk_fp_country__nicename' => 'El Salvador', 'atk_fp__iso3' => 'SLV', 'atk_fp__numcode' => 222, 'atk_fp__phonecode' => 503],
    ['id' => 65, 'atk_fp__iso' => 'GQ', 'atk_fp_country__name' => 'EQUATORIAL GUINEA', 'atk_fp_country__nicename' => 'Equatorial Guinea', 'atk_fp__iso3' => 'GNQ', 'atk_fp__numcode' => 226, 'atk_fp__phonecode' => 240],
    ['id' => 66, 'atk_fp__iso' => 'ER', 'atk_fp_country__name' => 'ERITREA', 'atk_fp_country__nicename' => 'Eritrea', 'atk_fp__iso3' => 'ERI', 'atk_fp__numcode' => 232, 'atk_fp__phonecode' => 291],
    ['id' => 67, 'atk_fp__iso' => 'EE', 'atk_fp_country__name' => 'ESTONIA', 'atk_fp_country__nicename' => 'Estonia', 'atk_fp__iso3' => 'EST', 'atk_fp__numcode' => 233, 'atk_fp__phonecode' => 372],
    ['id' => 68, 'atk_fp__iso' => 'ET', 'atk_fp_country__name' => 'ETHIOPIA', 'atk_fp_country__nicename' => 'Ethiopia', 'atk_fp__iso3' => 'ETH', 'atk_fp__numcode' => 231, 'atk_fp__phonecode' => 251],
    ['id' => 69, 'atk_fp__iso' => 'FK', 'atk_fp_country__name' => 'FALKLAND ISLANDS (MALVINAS)', 'atk_fp_country__nicename' => 'Falkland Islands (Malvinas)', 'atk_fp__iso3' => 'FLK', 'atk_fp__numcode' => 238, 'atk_fp__phonecode' => 500],
    ['id' => 70, 'atk_fp__iso' => 'FO', 'atk_fp_country__name' => 'FAROE ISLANDS', 'atk_fp_country__nicename' => 'Faroe Islands', 'atk_fp__iso3' => 'FRO', 'atk_fp__numcode' => 234, 'atk_fp__phonecode' => 298],
    ['id' => 71, 'atk_fp__iso' => 'FJ', 'atk_fp_country__name' => 'FIJI', 'atk_fp_country__nicename' => 'Fiji', 'atk_fp__iso3' => 'FJI', 'atk_fp__numcode' => 242, 'atk_fp__phonecode' => 679],
    ['id' => 72, 'atk_fp__iso' => 'FI', 'atk_fp_country__name' => 'FINLAND', 'atk_fp_country__nicename' => 'Finland', 'atk_fp__iso3' => 'FIN', 'atk_fp__numcode' => 246, 'atk_fp__phonecode' => 358],
    ['id' => 73, 'atk_fp__iso' => 'FR', 'atk_fp_country__name' => 'FRANCE', 'atk_fp_country__nicename' => 'France', 'atk_fp__iso3' => 'FRA', 'atk_fp__numcode' => 250, 'atk_fp__phonecode' => 33],
    ['id' => 74, 'atk_fp__iso' => 'GF', 'atk_fp_country__name' => 'FRENCH GUIANA', 'atk_fp_country__nicename' => 'French Guiana', 'atk_fp__iso3' => 'GUF', 'atk_fp__numcode' => 254, 'atk_fp__phonecode' => 594],
    ['id' => 75, 'atk_fp__iso' => 'PF', 'atk_fp_country__name' => 'FRENCH POLYNESIA', 'atk_fp_country__nicename' => 'French Polynesia', 'atk_fp__iso3' => 'PYF', 'atk_fp__numcode' => 258, 'atk_fp__phonecode' => 689],
    ['id' => 76, 'atk_fp__iso' => 'TF', 'atk_fp_country__name' => 'FRENCH SOUTHERN TERRITORIES', 'atk_fp_country__nicename' => 'French Southern Territories', 'atk_fp__iso3' => '', 'atk_fp__numcode' => '', 'atk_fp__phonecode' => 0],
    ['id' => 77, 'atk_fp__iso' => 'GA', 'atk_fp_country__name' => 'GABON', 'atk_fp_country__nicename' => 'Gabon', 'atk_fp__iso3' => 'GAB', 'atk_fp__numcode' => 266, 'atk_fp__phonecode' => 241],
    ['id' => 78, 'atk_fp__iso' => 'GM', 'atk_fp_country__name' => 'GAMBIA', 'atk_fp_country__nicename' => 'Gambia', 'atk_fp__iso3' => 'GMB', 'atk_fp__numcode' => 270, 'atk_fp__phonecode' => 220],
    ['id' => 79, 'atk_fp__iso' => 'GE', 'atk_fp_country__name' => 'GEORGIA', 'atk_fp_country__nicename' => 'Georgia', 'atk_fp__iso3' => 'GEO', 'atk_fp__numcode' => 268, 'atk_fp__phonecode' => 995],
    ['id' => 80, 'atk_fp__iso' => 'DE', 'atk_fp_country__name' => 'GERMANY', 'atk_fp_country__nicename' => 'Germany', 'atk_fp__iso3' => 'DEU', 'atk_fp__numcode' => 276, 'atk_fp__phonecode' => 49],
    ['id' => 81, 'atk_fp__iso' => 'GH', 'atk_fp_country__name' => 'GHANA', 'atk_fp_country__nicename' => 'Ghana', 'atk_fp__iso3' => 'GHA', 'atk_fp__numcode' => 288, 'atk_fp__phonecode' => 233],
    ['id' => 82, 'atk_fp__iso' => 'GI', 'atk_fp_country__name' => 'GIBRALTAR', 'atk_fp_country__nicename' => 'Gibraltar', 'atk_fp__iso3' => 'GIB', 'atk_fp__numcode' => 292, 'atk_fp__phonecode' => 350],
    ['id' => 83, 'atk_fp__iso' => 'GR', 'atk_fp_country__name' => 'GREECE', 'atk_fp_country__nicename' => 'Greece', 'atk_fp__iso3' => 'GRC', 'atk_fp__numcode' => 300, 'atk_fp__phonecode' => 30],
    ['id' => 84, 'atk_fp__iso' => 'GL', 'atk_fp_country__name' => 'GREENLAND', 'atk_fp_country__nicename' => 'Greenland', 'atk_fp__iso3' => 'GRL', 'atk_fp__numcode' => 304, 'atk_fp__phonecode' => 299],
    ['id' => 85, 'atk_fp__iso' => 'GD', 'atk_fp_country__name' => 'GRENADA', 'atk_fp_country__nicename' => 'Grenada', 'atk_fp__iso3' => 'GRD', 'atk_fp__numcode' => 308, 'atk_fp__phonecode' => 1473],
    ['id' => 86, 'atk_fp__iso' => 'GP', 'atk_fp_country__name' => 'GUADELOUPE', 'atk_fp_country__nicename' => 'Guadeloupe', 'atk_fp__iso3' => 'GLP', 'atk_fp__numcode' => 312, 'atk_fp__phonecode' => 590],
    ['id' => 87, 'atk_fp__iso' => 'GU', 'atk_fp_country__name' => 'GUAM', 'atk_fp_country__nicename' => 'Guam', 'atk_fp__iso3' => 'GUM', 'atk_fp__numcode' => 316, 'atk_fp__phonecode' => 1671],
    ['id' => 88, 'atk_fp__iso' => 'GT', 'atk_fp_country__name' => 'GUATEMALA', 'atk_fp_country__nicename' => 'Guatemala', 'atk_fp__iso3' => 'GTM', 'atk_fp__numcode' => 320, 'atk_fp__phonecode' => 502],
    ['id' => 89, 'atk_fp__iso' => 'GN', 'atk_fp_country__name' => 'GUINEA', 'atk_fp_country__nicename' => 'Guinea', 'atk_fp__iso3' => 'GIN', 'atk_fp__numcode' => 324, 'atk_fp__phonecode' => 224],
    ['id' => 90, 'atk_fp__iso' => 'GW', 'atk_fp_country__name' => 'GUINEA-BISSAU', 'atk_fp_country__nicename' => 'Guinea-Bissau', 'atk_fp__iso3' => 'GNB', 'atk_fp__numcode' => 624, 'atk_fp__phonecode' => 245],
    ['id' => 91, 'atk_fp__iso' => 'GY', 'atk_fp_country__name' => 'GUYANA', 'atk_fp_country__nicename' => 'Guyana', 'atk_fp__iso3' => 'GUY', 'atk_fp__numcode' => 328, 'atk_fp__phonecode' => 592],
    ['id' => 92, 'atk_fp__iso' => 'HT', 'atk_fp_country__name' => 'HAITI', 'atk_fp_country__nicename' => 'Haiti', 'atk_fp__iso3' => 'HTI', 'atk_fp__numcode' => 332, 'atk_fp__phonecode' => 509],
    ['id' => 93, 'atk_fp__iso' => 'HM', 'atk_fp_country__name' => 'HEARD ISLAND AND MCDONALD ISLANDS', 'atk_fp_country__nicename' => 'Heard Island and Mcdonald Islands', 'atk_fp__iso3' => '', 'atk_fp__numcode' => '', 'atk_fp__phonecode' => 0],
    ['id' => 94, 'atk_fp__iso' => 'VA', 'atk_fp_country__name' => 'HOLY SEE (VATICAN CITY STATE)', 'atk_fp_country__nicename' => 'Holy See (Vatican City State)', 'atk_fp__iso3' => 'VAT', 'atk_fp__numcode' => 336, 'atk_fp__phonecode' => 39],
    ['id' => 95, 'atk_fp__iso' => 'HN', 'atk_fp_country__name' => 'HONDURAS', 'atk_fp_country__nicename' => 'Honduras', 'atk_fp__iso3' => 'HND', 'atk_fp__numcode' => 340, 'atk_fp__phonecode' => 504],
    ['id' => 96, 'atk_fp__iso' => 'HK', 'atk_fp_country__name' => 'HONG KONG', 'atk_fp_country__nicename' => 'Hong Kong', 'atk_fp__iso3' => 'HKG', 'atk_fp__numcode' => 344, 'atk_fp__phonecode' => 852],
    ['id' => 97, 'atk_fp__iso' => 'HU', 'atk_fp_country__name' => 'HUNGARY', 'atk_fp_country__nicename' => 'Hungary', 'atk_fp__iso3' => 'HUN', 'atk_fp__numcode' => 348, 'atk_fp__phonecode' => 36],
    ['id' => 98, 'atk_fp__iso' => 'IS', 'atk_fp_country__name' => 'ICELAND', 'atk_fp_country__nicename' => 'Iceland', 'atk_fp__iso3' => 'ISL', 'atk_fp__numcode' => 352, 'atk_fp__phonecode' => 354],
    ['id' => 99, 'atk_fp__iso' => 'IN', 'atk_fp_country__name' => 'INDIA', 'atk_fp_country__nicename' => 'India', 'atk_fp__iso3' => 'IND', 'atk_fp__numcode' => 356, 'atk_fp__phonecode' => 91],
    ['id' => 100, 'atk_fp__iso' => 'ID', 'atk_fp_country__name' => 'INDONESIA', 'atk_fp_country__nicename' => 'Indonesia', 'atk_fp__iso3' => 'IDN', 'atk_fp__numcode' => 360, 'atk_fp__phonecode' => 62],
    ['id' => 101, 'atk_fp__iso' => 'IR', 'atk_fp_country__name' => 'IRAN, ISLAMIC REPUBLIC OF', 'atk_fp_country__nicename' => 'Iran, Islamic Republic of', 'atk_fp__iso3' => 'IRN', 'atk_fp__numcode' => 364, 'atk_fp__phonecode' => 98],
    ['id' => 102, 'atk_fp__iso' => 'IQ', 'atk_fp_country__name' => 'IRAQ', 'atk_fp_country__nicename' => 'Iraq', 'atk_fp__iso3' => 'IRQ', 'atk_fp__numcode' => 368, 'atk_fp__phonecode' => 964],
    ['id' => 103, 'atk_fp__iso' => 'IE', 'atk_fp_country__name' => 'IRELAND', 'atk_fp_country__nicename' => 'Ireland', 'atk_fp__iso3' => 'IRL', 'atk_fp__numcode' => 372, 'atk_fp__phonecode' => 353],
    ['id' => 104, 'atk_fp__iso' => 'IL', 'atk_fp_country__name' => 'ISRAEL', 'atk_fp_country__nicename' => 'Israel', 'atk_fp__iso3' => 'ISR', 'atk_fp__numcode' => 376, 'atk_fp__phonecode' => 972],
    ['id' => 105, 'atk_fp__iso' => 'IT', 'atk_fp_country__name' => 'ITALY', 'atk_fp_country__nicename' => 'Italy', 'atk_fp__iso3' => 'ITA', 'atk_fp__numcode' => 380, 'atk_fp__phonecode' => 39],
    ['id' => 106, 'atk_fp__iso' => 'JM', 'atk_fp_country__name' => 'JAMAICA', 'atk_fp_country__nicename' => 'Jamaica', 'atk_fp__iso3' => 'JAM', 'atk_fp__numcode' => 388, 'atk_fp__phonecode' => 1876],
    ['id' => 107, 'atk_fp__iso' => 'JP', 'atk_fp_country__name' => 'JAPAN', 'atk_fp_country__nicename' => 'Japan', 'atk_fp__iso3' => 'JPN', 'atk_fp__numcode' => 392, 'atk_fp__phonecode' => 81],
    ['id' => 108, 'atk_fp__iso' => 'JO', 'atk_fp_country__name' => 'JORDAN', 'atk_fp_country__nicename' => 'Jordan', 'atk_fp__iso3' => 'JOR', 'atk_fp__numcode' => 400, 'atk_fp__phonecode' => 962],
    ['id' => 109, 'atk_fp__iso' => 'KZ', 'atk_fp_country__name' => 'KAZAKHSTAN', 'atk_fp_country__nicename' => 'Kazakhstan', 'atk_fp__iso3' => 'KAZ', 'atk_fp__numcode' => 398, 'atk_fp__phonecode' => 7],
    ['id' => 110, 'atk_fp__iso' => 'KE', 'atk_fp_country__name' => 'KENYA', 'atk_fp_country__nicename' => 'Kenya', 'atk_fp__iso3' => 'KEN', 'atk_fp__numcode' => 404, 'atk_fp__phonecode' => 254],
    ['id' => 111, 'atk_fp__iso' => 'KI', 'atk_fp_country__name' => 'KIRIBATI', 'atk_fp_country__nicename' => 'Kiribati', 'atk_fp__iso3' => 'KIR', 'atk_fp__numcode' => 296, 'atk_fp__phonecode' => 686],
    ['id' => 112, 'atk_fp__iso' => 'KP', 'atk_fp_country__name' => 'KOREA, DEMOCRATIC PEOPLE\'S REPUBLIC OF', 'atk_fp_country__nicename' => 'Korea, Democratic People\'s Republic of', 'atk_fp__iso3' => 'PRK', 'atk_fp__numcode' => 408, 'atk_fp__phonecode' => 850],
    ['id' => 113, 'atk_fp__iso' => 'KR', 'atk_fp_country__name' => 'KOREA, REPUBLIC OF', 'atk_fp_country__nicename' => 'Korea, Republic of', 'atk_fp__iso3' => 'KOR', 'atk_fp__numcode' => 410, 'atk_fp__phonecode' => 82],
    ['id' => 114, 'atk_fp__iso' => 'KW', 'atk_fp_country__name' => 'KUWAIT', 'atk_fp_country__nicename' => 'Kuwait', 'atk_fp__iso3' => 'KWT', 'atk_fp__numcode' => 414, 'atk_fp__phonecode' => 965],
    ['id' => 115, 'atk_fp__iso' => 'KG', 'atk_fp_country__name' => 'KYRGYZSTAN', 'atk_fp_country__nicename' => 'Kyrgyzstan', 'atk_fp__iso3' => 'KGZ', 'atk_fp__numcode' => 417, 'atk_fp__phonecode' => 996],
    ['id' => 116, 'atk_fp__iso' => 'LA', 'atk_fp_country__name' => 'LAO PEOPLE\'S DEMOCRATIC REPUBLIC', 'atk_fp_country__nicename' => 'Lao People\'s Democratic Republic', 'atk_fp__iso3' => 'LAO', 'atk_fp__numcode' => 418, 'atk_fp__phonecode' => 856],
    ['id' => 117, 'atk_fp__iso' => 'LV', 'atk_fp_country__name' => 'LATVIA', 'atk_fp_country__nicename' => 'Latvia', 'atk_fp__iso3' => 'LVA', 'atk_fp__numcode' => 428, 'atk_fp__phonecode' => 371],
    ['id' => 118, 'atk_fp__iso' => 'LB', 'atk_fp_country__name' => 'LEBANON', 'atk_fp_country__nicename' => 'Lebanon', 'atk_fp__iso3' => 'LBN', 'atk_fp__numcode' => 422, 'atk_fp__phonecode' => 961],
    ['id' => 119, 'atk_fp__iso' => 'LS', 'atk_fp_country__name' => 'LESOTHO', 'atk_fp_country__nicename' => 'Lesotho', 'atk_fp__iso3' => 'LSO', 'atk_fp__numcode' => 426, 'atk_fp__phonecode' => 266],
    ['id' => 120, 'atk_fp__iso' => 'LR', 'atk_fp_country__name' => 'LIBERIA', 'atk_fp_country__nicename' => 'Liberia', 'atk_fp__iso3' => 'LBR', 'atk_fp__numcode' => 430, 'atk_fp__phonecode' => 231],
    ['id' => 121, 'atk_fp__iso' => 'LY', 'atk_fp_country__name' => 'LIBYAN ARAB JAMAHIRIYA', 'atk_fp_country__nicename' => 'Libyan Arab Jamahiriya', 'atk_fp__iso3' => 'LBY', 'atk_fp__numcode' => 434, 'atk_fp__phonecode' => 218],
    ['id' => 122, 'atk_fp__iso' => 'LI', 'atk_fp_country__name' => 'LIECHTENSTEIN', 'atk_fp_country__nicename' => 'Liechtenstein', 'atk_fp__iso3' => 'LIE', 'atk_fp__numcode' => 438, 'atk_fp__phonecode' => 423],
    ['id' => 123, 'atk_fp__iso' => 'LT', 'atk_fp_country__name' => 'LITHUANIA', 'atk_fp_country__nicename' => 'Lithuania', 'atk_fp__iso3' => 'LTU', 'atk_fp__numcode' => 440, 'atk_fp__phonecode' => 370],
    ['id' => 124, 'atk_fp__iso' => 'LU', 'atk_fp_country__name' => 'LUXEMBOURG', 'atk_fp_country__nicename' => 'Luxembourg', 'atk_fp__iso3' => 'LUX', 'atk_fp__numcode' => 442, 'atk_fp__phonecode' => 352],
    ['id' => 125, 'atk_fp__iso' => 'MO', 'atk_fp_country__name' => 'MACAO', 'atk_fp_country__nicename' => 'Macao', 'atk_fp__iso3' => 'MAC', 'atk_fp__numcode' => 446, 'atk_fp__phonecode' => 853],
    ['id' => 126, 'atk_fp__iso' => 'MK', 'atk_fp_country__name' => 'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF', 'atk_fp_country__nicename' => 'Macedonia, the Former Yugoslav Republic of', 'atk_fp__iso3' => 'MKD', 'atk_fp__numcode' => 807, 'atk_fp__phonecode' => 389],
    ['id' => 127, 'atk_fp__iso' => 'MG', 'atk_fp_country__name' => 'MADAGASCAR', 'atk_fp_country__nicename' => 'Madagascar', 'atk_fp__iso3' => 'MDG', 'atk_fp__numcode' => 450, 'atk_fp__phonecode' => 261],
    ['id' => 128, 'atk_fp__iso' => 'MW', 'atk_fp_country__name' => 'MALAWI', 'atk_fp_country__nicename' => 'Malawi', 'atk_fp__iso3' => 'MWI', 'atk_fp__numcode' => 454, 'atk_fp__phonecode' => 265],
    ['id' => 129, 'atk_fp__iso' => 'MY', 'atk_fp_country__name' => 'MALAYSIA', 'atk_fp_country__nicename' => 'Malaysia', 'atk_fp__iso3' => 'MYS', 'atk_fp__numcode' => 458, 'atk_fp__phonecode' => 60],
    ['id' => 130, 'atk_fp__iso' => 'MV', 'atk_fp_country__name' => 'MALDIVES', 'atk_fp_country__nicename' => 'Maldives', 'atk_fp__iso3' => 'MDV', 'atk_fp__numcode' => 462, 'atk_fp__phonecode' => 960],
    ['id' => 131, 'atk_fp__iso' => 'ML', 'atk_fp_country__name' => 'MALI', 'atk_fp_country__nicename' => 'Mali', 'atk_fp__iso3' => 'MLI', 'atk_fp__numcode' => 466, 'atk_fp__phonecode' => 223],
    ['id' => 132, 'atk_fp__iso' => 'MT', 'atk_fp_country__name' => 'MALTA', 'atk_fp_country__nicename' => 'Malta', 'atk_fp__iso3' => 'MLT', 'atk_fp__numcode' => 470, 'atk_fp__phonecode' => 356],
    ['id' => 133, 'atk_fp__iso' => 'MH', 'atk_fp_country__name' => 'MARSHALL ISLANDS', 'atk_fp_country__nicename' => 'Marshall Islands', 'atk_fp__iso3' => 'MHL', 'atk_fp__numcode' => 584, 'atk_fp__phonecode' => 692],
    ['id' => 134, 'atk_fp__iso' => 'MQ', 'atk_fp_country__name' => 'MARTINIQUE', 'atk_fp_country__nicename' => 'Martinique', 'atk_fp__iso3' => 'MTQ', 'atk_fp__numcode' => 474, 'atk_fp__phonecode' => 596],
    ['id' => 135, 'atk_fp__iso' => 'MR', 'atk_fp_country__name' => 'MAURITANIA', 'atk_fp_country__nicename' => 'Mauritania', 'atk_fp__iso3' => 'MRT', 'atk_fp__numcode' => 478, 'atk_fp__phonecode' => 222],
    ['id' => 136, 'atk_fp__iso' => 'MU', 'atk_fp_country__name' => 'MAURITIUS', 'atk_fp_country__nicename' => 'Mauritius', 'atk_fp__iso3' => 'MUS', 'atk_fp__numcode' => 480, 'atk_fp__phonecode' => 230],
    ['id' => 137, 'atk_fp__iso' => 'YT', 'atk_fp_country__name' => 'MAYOTTE', 'atk_fp_country__nicename' => 'Mayotte', 'atk_fp__iso3' => '', 'atk_fp__numcode' => '', 'atk_fp__phonecode' => 269],
    ['id' => 138, 'atk_fp__iso' => 'MX', 'atk_fp_country__name' => 'MEXICO', 'atk_fp_country__nicename' => 'Mexico', 'atk_fp__iso3' => 'MEX', 'atk_fp__numcode' => 484, 'atk_fp__phonecode' => 52],
    ['id' => 139, 'atk_fp__iso' => 'FM', 'atk_fp_country__name' => 'MICRONESIA, FEDERATED STATES OF', 'atk_fp_country__nicename' => 'Micronesia, Federated States of', 'atk_fp__iso3' => 'FSM', 'atk_fp__numcode' => 583, 'atk_fp__phonecode' => 691],
    ['id' => 140, 'atk_fp__iso' => 'MD', 'atk_fp_country__name' => 'MOLDOVA, REPUBLIC OF', 'atk_fp_country__nicename' => 'Moldova, Republic of', 'atk_fp__iso3' => 'MDA', 'atk_fp__numcode' => 498, 'atk_fp__phonecode' => 373],
    ['id' => 141, 'atk_fp__iso' => 'MC', 'atk_fp_country__name' => 'MONACO', 'atk_fp_country__nicename' => 'Monaco', 'atk_fp__iso3' => 'MCO', 'atk_fp__numcode' => 492, 'atk_fp__phonecode' => 377],
    ['id' => 142, 'atk_fp__iso' => 'MN', 'atk_fp_country__name' => 'MONGOLIA', 'atk_fp_country__nicename' => 'Mongolia', 'atk_fp__iso3' => 'MNG', 'atk_fp__numcode' => 496, 'atk_fp__phonecode' => 976],
    ['id' => 143, 'atk_fp__iso' => 'MS', 'atk_fp_country__name' => 'MONTSERRAT', 'atk_fp_country__nicename' => 'Montserrat', 'atk_fp__iso3' => 'MSR', 'atk_fp__numcode' => 500, 'atk_fp__phonecode' => 1664],
    ['id' => 144, 'atk_fp__iso' => 'MA', 'atk_fp_country__name' => 'MOROCCO', 'atk_fp_country__nicename' => 'Morocco', 'atk_fp__iso3' => 'MAR', 'atk_fp__numcode' => 504, 'atk_fp__phonecode' => 212],
    ['id' => 145, 'atk_fp__iso' => 'MZ', 'atk_fp_country__name' => 'MOZAMBIQUE', 'atk_fp_country__nicename' => 'Mozambique', 'atk_fp__iso3' => 'MOZ', 'atk_fp__numcode' => 508, 'atk_fp__phonecode' => 258],
    ['id' => 146, 'atk_fp__iso' => 'MM', 'atk_fp_country__name' => 'MYANMAR', 'atk_fp_country__nicename' => 'Myanmar', 'atk_fp__iso3' => 'MMR', 'atk_fp__numcode' => 104, 'atk_fp__phonecode' => 95],
    ['id' => 147, 'atk_fp__iso' => 'NA', 'atk_fp_country__name' => 'NAMIBIA', 'atk_fp_country__nicename' => 'Namibia', 'atk_fp__iso3' => 'NAM', 'atk_fp__numcode' => 516, 'atk_fp__phonecode' => 264],
    ['id' => 148, 'atk_fp__iso' => 'NR', 'atk_fp_country__name' => 'NAURU', 'atk_fp_country__nicename' => 'Nauru', 'atk_fp__iso3' => 'NRU', 'atk_fp__numcode' => 520, 'atk_fp__phonecode' => 674],
    ['id' => 149, 'atk_fp__iso' => 'NP', 'atk_fp_country__name' => 'NEPAL', 'atk_fp_country__nicename' => 'Nepal', 'atk_fp__iso3' => 'NPL', 'atk_fp__numcode' => 524, 'atk_fp__phonecode' => 977],
    ['id' => 150, 'atk_fp__iso' => 'NL', 'atk_fp_country__name' => 'NETHERLANDS', 'atk_fp_country__nicename' => 'Netherlands', 'atk_fp__iso3' => 'NLD', 'atk_fp__numcode' => 528, 'atk_fp__phonecode' => 31],
    ['id' => 151, 'atk_fp__iso' => 'AN', 'atk_fp_country__name' => 'NETHERLANDS ANTILLES', 'atk_fp_country__nicename' => 'Netherlands Antilles', 'atk_fp__iso3' => 'ANT', 'atk_fp__numcode' => 530, 'atk_fp__phonecode' => 599],
    ['id' => 152, 'atk_fp__iso' => 'NC', 'atk_fp_country__name' => 'NEW CALEDONIA', 'atk_fp_country__nicename' => 'New Caledonia', 'atk_fp__iso3' => 'NCL', 'atk_fp__numcode' => 540, 'atk_fp__phonecode' => 687],
    ['id' => 153, 'atk_fp__iso' => 'NZ', 'atk_fp_country__name' => 'NEW ZEALAND', 'atk_fp_country__nicename' => 'New Zealand', 'atk_fp__iso3' => 'NZL', 'atk_fp__numcode' => 554, 'atk_fp__phonecode' => 64],
    ['id' => 154, 'atk_fp__iso' => 'NI', 'atk_fp_country__name' => 'NICARAGUA', 'atk_fp_country__nicename' => 'Nicaragua', 'atk_fp__iso3' => 'NIC', 'atk_fp__numcode' => 558, 'atk_fp__phonecode' => 505],
    ['id' => 155, 'atk_fp__iso' => 'NE', 'atk_fp_country__name' => 'NIGER', 'atk_fp_country__nicename' => 'Niger', 'atk_fp__iso3' => 'NER', 'atk_fp__numcode' => 562, 'atk_fp__phonecode' => 227],
    ['id' => 156, 'atk_fp__iso' => 'NG', 'atk_fp_country__name' => 'NIGERIA', 'atk_fp_country__nicename' => 'Nigeria', 'atk_fp__iso3' => 'NGA', 'atk_fp__numcode' => 566, 'atk_fp__phonecode' => 234],
    ['id' => 157, 'atk_fp__iso' => 'NU', 'atk_fp_country__name' => 'NIUE', 'atk_fp_country__nicename' => 'Niue', 'atk_fp__iso3' => 'NIU', 'atk_fp__numcode' => 570, 'atk_fp__phonecode' => 683],
    ['id' => 158, 'atk_fp__iso' => 'NF', 'atk_fp_country__name' => 'NORFOLK ISLAND', 'atk_fp_country__nicename' => 'Norfolk Island', 'atk_fp__iso3' => 'NFK', 'atk_fp__numcode' => 574, 'atk_fp__phonecode' => 672],
    ['id' => 159, 'atk_fp__iso' => 'MP', 'atk_fp_country__name' => 'NORTHERN MARIANA ISLANDS', 'atk_fp_country__nicename' => 'Northern Mariana Islands', 'atk_fp__iso3' => 'MNP', 'atk_fp__numcode' => 580, 'atk_fp__phonecode' => 1670],
    ['id' => 160, 'atk_fp__iso' => 'NO', 'atk_fp_country__name' => 'NORWAY', 'atk_fp_country__nicename' => 'Norway', 'atk_fp__iso3' => 'NOR', 'atk_fp__numcode' => 578, 'atk_fp__phonecode' => 47],
    ['id' => 161, 'atk_fp__iso' => 'OM', 'atk_fp_country__name' => 'OMAN', 'atk_fp_country__nicename' => 'Oman', 'atk_fp__iso3' => 'OMN', 'atk_fp__numcode' => 512, 'atk_fp__phonecode' => 968],
    ['id' => 162, 'atk_fp__iso' => 'PK', 'atk_fp_country__name' => 'PAKISTAN', 'atk_fp_country__nicename' => 'Pakistan', 'atk_fp__iso3' => 'PAK', 'atk_fp__numcode' => 586, 'atk_fp__phonecode' => 92],
    ['id' => 163, 'atk_fp__iso' => 'PW', 'atk_fp_country__name' => 'PALAU', 'atk_fp_country__nicename' => 'Palau', 'atk_fp__iso3' => 'PLW', 'atk_fp__numcode' => 585, 'atk_fp__phonecode' => 680],
    ['id' => 164, 'atk_fp__iso' => 'PS', 'atk_fp_country__name' => 'PALESTINIAN TERRITORY, OCCUPIED', 'atk_fp_country__nicename' => 'Palestinian Territory, Occupied', 'atk_fp__iso3' => '', 'atk_fp__numcode' => '', 'atk_fp__phonecode' => 970],
    ['id' => 165, 'atk_fp__iso' => 'PA', 'atk_fp_country__name' => 'PANAMA', 'atk_fp_country__nicename' => 'Panama', 'atk_fp__iso3' => 'PAN', 'atk_fp__numcode' => 591, 'atk_fp__phonecode' => 507],
    ['id' => 166, 'atk_fp__iso' => 'PG', 'atk_fp_country__name' => 'PAPUA NEW GUINEA', 'atk_fp_country__nicename' => 'Papua New Guinea', 'atk_fp__iso3' => 'PNG', 'atk_fp__numcode' => 598, 'atk_fp__phonecode' => 675],
    ['id' => 167, 'atk_fp__iso' => 'PY', 'atk_fp_country__name' => 'PARAGUAY', 'atk_fp_country__nicename' => 'Paraguay', 'atk_fp__iso3' => 'PRY', 'atk_fp__numcode' => 600, 'atk_fp__phonecode' => 595],
    ['id' => 168, 'atk_fp__iso' => 'PE', 'atk_fp_country__name' => 'PERU', 'atk_fp_country__nicename' => 'Peru', 'atk_fp__iso3' => 'PER', 'atk_fp__numcode' => 604, 'atk_fp__phonecode' => 51],
    ['id' => 169, 'atk_fp__iso' => 'PH', 'atk_fp_country__name' => 'PHILIPPINES', 'atk_fp_country__nicename' => 'Philippines', 'atk_fp__iso3' => 'PHL', 'atk_fp__numcode' => 608, 'atk_fp__phonecode' => 63],
    ['id' => 170, 'atk_fp__iso' => 'PN', 'atk_fp_country__name' => 'PITCAIRN', 'atk_fp_country__nicename' => 'Pitcairn', 'atk_fp__iso3' => 'PCN', 'atk_fp__numcode' => 612, 'atk_fp__phonecode' => 0],
    ['id' => 171, 'atk_fp__iso' => 'PL', 'atk_fp_country__name' => 'POLAND', 'atk_fp_country__nicename' => 'Poland', 'atk_fp__iso3' => 'POL', 'atk_fp__numcode' => 616, 'atk_fp__phonecode' => 48],
    ['id' => 172, 'atk_fp__iso' => 'PT', 'atk_fp_country__name' => 'PORTUGAL', 'atk_fp_country__nicename' => 'Portugal', 'atk_fp__iso3' => 'PRT', 'atk_fp__numcode' => 620, 'atk_fp__phonecode' => 351],
    ['id' => 173, 'atk_fp__iso' => 'PR', 'atk_fp_country__name' => 'PUERTO RICO', 'atk_fp_country__nicename' => 'Puerto Rico', 'atk_fp__iso3' => 'PRI', 'atk_fp__numcode' => 630, 'atk_fp__phonecode' => 1787],
    ['id' => 174, 'atk_fp__iso' => 'QA', 'atk_fp_country__name' => 'QATAR', 'atk_fp_country__nicename' => 'Qatar', 'atk_fp__iso3' => 'QAT', 'atk_fp__numcode' => 634, 'atk_fp__phonecode' => 974],
    ['id' => 175, 'atk_fp__iso' => 'RE', 'atk_fp_country__name' => 'REUNION', 'atk_fp_country__nicename' => 'Reunion', 'atk_fp__iso3' => 'REU', 'atk_fp__numcode' => 638, 'atk_fp__phonecode' => 262],
    ['id' => 176, 'atk_fp__iso' => 'RO', 'atk_fp_country__name' => 'ROMANIA', 'atk_fp_country__nicename' => 'Romania', 'atk_fp__iso3' => 'ROM', 'atk_fp__numcode' => 642, 'atk_fp__phonecode' => 40],
    ['id' => 177, 'atk_fp__iso' => 'RU', 'atk_fp_country__name' => 'RUSSIAN FEDERATION', 'atk_fp_country__nicename' => 'Russian Federation', 'atk_fp__iso3' => 'RUS', 'atk_fp__numcode' => 643, 'atk_fp__phonecode' => 7],
    ['id' => 178, 'atk_fp__iso' => 'RW', 'atk_fp_country__name' => 'RWANDA', 'atk_fp_country__nicename' => 'Rwanda', 'atk_fp__iso3' => 'RWA', 'atk_fp__numcode' => 646, 'atk_fp__phonecode' => 250],
    ['id' => 179, 'atk_fp__iso' => 'SH', 'atk_fp_country__name' => 'SAINT HELENA', 'atk_fp_country__nicename' => 'Saint Helena', 'atk_fp__iso3' => 'SHN', 'atk_fp__numcode' => 654, 'atk_fp__phonecode' => 290],
    ['id' => 180, 'atk_fp__iso' => 'KN', 'atk_fp_country__name' => 'SAINT KITTS AND NEVIS', 'atk_fp_country__nicename' => 'Saint Kitts and Nevis', 'atk_fp__iso3' => 'KNA', 'atk_fp__numcode' => 659, 'atk_fp__phonecode' => 1869],
    ['id' => 181, 'atk_fp__iso' => 'LC', 'atk_fp_country__name' => 'SAINT LUCIA', 'atk_fp_country__nicename' => 'Saint Lucia', 'atk_fp__iso3' => 'LCA', 'atk_fp__numcode' => 662, 'atk_fp__phonecode' => 1758],
    ['id' => 182, 'atk_fp__iso' => 'PM', 'atk_fp_country__name' => 'SAINT PIERRE AND MIQUELON', 'atk_fp_country__nicename' => 'Saint Pierre and Miquelon', 'atk_fp__iso3' => 'SPM', 'atk_fp__numcode' => 666, 'atk_fp__phonecode' => 508],
    ['id' => 183, 'atk_fp__iso' => 'VC', 'atk_fp_country__name' => 'SAINT VINCENT AND THE GRENADINES', 'atk_fp_country__nicename' => 'Saint Vincent and the Grenadines', 'atk_fp__iso3' => 'VCT', 'atk_fp__numcode' => 670, 'atk_fp__phonecode' => 1784],
    ['id' => 184, 'atk_fp__iso' => 'WS', 'atk_fp_country__name' => 'SAMOA', 'atk_fp_country__nicename' => 'Samoa', 'atk_fp__iso3' => 'WSM', 'atk_fp__numcode' => 882, 'atk_fp__phonecode' => 684],
    ['id' => 185, 'atk_fp__iso' => 'SM', 'atk_fp_country__name' => 'SAN MARINO', 'atk_fp_country__nicename' => 'San Marino', 'atk_fp__iso3' => 'SMR', 'atk_fp__numcode' => 674, 'atk_fp__phonecode' => 378],
    ['id' => 186, 'atk_fp__iso' => 'ST', 'atk_fp_country__name' => 'SAO TOME AND PRINCIPE', 'atk_fp_country__nicename' => 'Sao Tome and Principe', 'atk_fp__iso3' => 'STP', 'atk_fp__numcode' => 678, 'atk_fp__phonecode' => 239],
    ['id' => 187, 'atk_fp__iso' => 'SA', 'atk_fp_country__name' => 'SAUDI ARABIA', 'atk_fp_country__nicename' => 'Saudi Arabia', 'atk_fp__iso3' => 'SAU', 'atk_fp__numcode' => 682, 'atk_fp__phonecode' => 966],
    ['id' => 188, 'atk_fp__iso' => 'SN', 'atk_fp_country__name' => 'SENEGAL', 'atk_fp_country__nicename' => 'Senegal', 'atk_fp__iso3' => 'SEN', 'atk_fp__numcode' => 686, 'atk_fp__phonecode' => 221],
    ['id' => 190, 'atk_fp__iso' => 'SC', 'atk_fp_country__name' => 'SEYCHELLES', 'atk_fp_country__nicename' => 'Seychelles', 'atk_fp__iso3' => 'SYC', 'atk_fp__numcode' => 690, 'atk_fp__phonecode' => 248],
    ['id' => 191, 'atk_fp__iso' => 'SL', 'atk_fp_country__name' => 'SIERRA LEONE', 'atk_fp_country__nicename' => 'Sierra Leone', 'atk_fp__iso3' => 'SLE', 'atk_fp__numcode' => 694, 'atk_fp__phonecode' => 232],
    ['id' => 192, 'atk_fp__iso' => 'SG', 'atk_fp_country__name' => 'SINGAPORE', 'atk_fp_country__nicename' => 'Singapore', 'atk_fp__iso3' => 'SGP', 'atk_fp__numcode' => 702, 'atk_fp__phonecode' => 65],
    ['id' => 193, 'atk_fp__iso' => 'SK', 'atk_fp_country__name' => 'SLOVAKIA', 'atk_fp_country__nicename' => 'Slovakia', 'atk_fp__iso3' => 'SVK', 'atk_fp__numcode' => 703, 'atk_fp__phonecode' => 421],
    ['id' => 194, 'atk_fp__iso' => 'SI', 'atk_fp_country__name' => 'SLOVENIA', 'atk_fp_country__nicename' => 'Slovenia', 'atk_fp__iso3' => 'SVN', 'atk_fp__numcode' => 705, 'atk_fp__phonecode' => 386],
    ['id' => 195, 'atk_fp__iso' => 'SB', 'atk_fp_country__name' => 'SOLOMON ISLANDS', 'atk_fp_country__nicename' => 'Solomon Islands', 'atk_fp__iso3' => 'SLB', 'atk_fp__numcode' => 90, 'atk_fp__phonecode' => 677],
    ['id' => 196, 'atk_fp__iso' => 'SO', 'atk_fp_country__name' => 'SOMALIA', 'atk_fp_country__nicename' => 'Somalia', 'atk_fp__iso3' => 'SOM', 'atk_fp__numcode' => 706, 'atk_fp__phonecode' => 252],
    ['id' => 197, 'atk_fp__iso' => 'ZA', 'atk_fp_country__name' => 'SOUTH AFRICA', 'atk_fp_country__nicename' => 'South Africa', 'atk_fp__iso3' => 'ZAF', 'atk_fp__numcode' => 710, 'atk_fp__phonecode' => 27],
    ['id' => 198, 'atk_fp__iso' => 'GS', 'atk_fp_country__name' => 'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS', 'atk_fp_country__nicename' => 'South Georgia and the South Sandwich Islands', 'atk_fp__iso3' => '', 'atk_fp__numcode' => '', 'atk_fp__phonecode' => 0],
    ['id' => 199, 'atk_fp__iso' => 'ES', 'atk_fp_country__name' => 'SPAIN', 'atk_fp_country__nicename' => 'Spain', 'atk_fp__iso3' => 'ESP', 'atk_fp__numcode' => 724, 'atk_fp__phonecode' => 34],
    ['id' => 200, 'atk_fp__iso' => 'LK', 'atk_fp_country__name' => 'SRI LANKA', 'atk_fp_country__nicename' => 'Sri Lanka', 'atk_fp__iso3' => 'LKA', 'atk_fp__numcode' => 144, 'atk_fp__phonecode' => 94],
    ['id' => 201, 'atk_fp__iso' => 'SD', 'atk_fp_country__name' => 'SUDAN', 'atk_fp_country__nicename' => 'Sudan', 'atk_fp__iso3' => 'SDN', 'atk_fp__numcode' => 736, 'atk_fp__phonecode' => 249],
    ['id' => 202, 'atk_fp__iso' => 'SR', 'atk_fp_country__name' => 'SURINAME', 'atk_fp_country__nicename' => 'Suriname', 'atk_fp__iso3' => 'SUR', 'atk_fp__numcode' => 740, 'atk_fp__phonecode' => 597],
    ['id' => 203, 'atk_fp__iso' => 'SJ', 'atk_fp_country__name' => 'SVALBARD AND JAN MAYEN', 'atk_fp_country__nicename' => 'Svalbard and Jan Mayen', 'atk_fp__iso3' => 'SJM', 'atk_fp__numcode' => 744, 'atk_fp__phonecode' => 47],
    ['id' => 204, 'atk_fp__iso' => 'SZ', 'atk_fp_country__name' => 'SWAZILAND', 'atk_fp_country__nicename' => 'Swaziland', 'atk_fp__iso3' => 'SWZ', 'atk_fp__numcode' => 748, 'atk_fp__phonecode' => 268],
    ['id' => 205, 'atk_fp__iso' => 'SE', 'atk_fp_country__name' => 'SWEDEN', 'atk_fp_country__nicename' => 'Sweden', 'atk_fp__iso3' => 'SWE', 'atk_fp__numcode' => 752, 'atk_fp__phonecode' => 46],
    ['id' => 206, 'atk_fp__iso' => 'CH', 'atk_fp_country__name' => 'SWITZERLAND', 'atk_fp_country__nicename' => 'Switzerland', 'atk_fp__iso3' => 'CHE', 'atk_fp__numcode' => 756, 'atk_fp__phonecode' => 41],
    ['id' => 207, 'atk_fp__iso' => 'SY', 'atk_fp_country__name' => 'SYRIAN ARAB REPUBLIC', 'atk_fp_country__nicename' => 'Syrian Arab Republic', 'atk_fp__iso3' => 'SYR', 'atk_fp__numcode' => 760, 'atk_fp__phonecode' => 963],
    ['id' => 208, 'atk_fp__iso' => 'TW', 'atk_fp_country__name' => 'TAIWAN, PROVINCE OF CHINA', 'atk_fp_country__nicename' => 'Taiwan, Province of China', 'atk_fp__iso3' => 'TWN', 'atk_fp__numcode' => 158, 'atk_fp__phonecode' => 886],
    ['id' => 209, 'atk_fp__iso' => 'TJ', 'atk_fp_country__name' => 'TAJIKISTAN', 'atk_fp_country__nicename' => 'Tajikistan', 'atk_fp__iso3' => 'TJK', 'atk_fp__numcode' => 762, 'atk_fp__phonecode' => 992],
    ['id' => 210, 'atk_fp__iso' => 'TZ', 'atk_fp_country__name' => 'TANZANIA, UNITED REPUBLIC OF', 'atk_fp_country__nicename' => 'Tanzania, United Republic of', 'atk_fp__iso3' => 'TZA', 'atk_fp__numcode' => 834, 'atk_fp__phonecode' => 255],
    ['id' => 211, 'atk_fp__iso' => 'TH', 'atk_fp_country__name' => 'THAILAND', 'atk_fp_country__nicename' => 'Thailand', 'atk_fp__iso3' => 'THA', 'atk_fp__numcode' => 764, 'atk_fp__phonecode' => 66],
    ['id' => 212, 'atk_fp__iso' => 'TL', 'atk_fp_country__name' => 'TIMOR-LESTE', 'atk_fp_country__nicename' => 'Timor-Leste', 'atk_fp__iso3' => '', 'atk_fp__numcode' => '', 'atk_fp__phonecode' => 670],
    ['id' => 213, 'atk_fp__iso' => 'TG', 'atk_fp_country__name' => 'TOGO', 'atk_fp_country__nicename' => 'Togo', 'atk_fp__iso3' => 'TGO', 'atk_fp__numcode' => 768, 'atk_fp__phonecode' => 228],
    ['id' => 214, 'atk_fp__iso' => 'TK', 'atk_fp_country__name' => 'TOKELAU', 'atk_fp_country__nicename' => 'Tokelau', 'atk_fp__iso3' => 'TKL', 'atk_fp__numcode' => 772, 'atk_fp__phonecode' => 690],
    ['id' => 215, 'atk_fp__iso' => 'TO', 'atk_fp_country__name' => 'TONGA', 'atk_fp_country__nicename' => 'Tonga', 'atk_fp__iso3' => 'TON', 'atk_fp__numcode' => 776, 'atk_fp__phonecode' => 676],
    ['id' => 216, 'atk_fp__iso' => 'TT', 'atk_fp_country__name' => 'TRINIDAD AND TOBAGO', 'atk_fp_country__nicename' => 'Trinidad and Tobago', 'atk_fp__iso3' => 'TTO', 'atk_fp__numcode' => 780, 'atk_fp__phonecode' => 1868],
    ['id' => 217, 'atk_fp__iso' => 'TN', 'atk_fp_country__name' => 'TUNISIA', 'atk_fp_country__nicename' => 'Tunisia', 'atk_fp__iso3' => 'TUN', 'atk_fp__numcode' => 788, 'atk_fp__phonecode' => 216],
    ['id' => 218, 'atk_fp__iso' => 'TR', 'atk_fp_country__name' => 'TURKEY', 'atk_fp_country__nicename' => 'Turkey', 'atk_fp__iso3' => 'TUR', 'atk_fp__numcode' => 792, 'atk_fp__phonecode' => 90],
    ['id' => 219, 'atk_fp__iso' => 'TM', 'atk_fp_country__name' => 'TURKMENISTAN', 'atk_fp_country__nicename' => 'Turkmenistan', 'atk_fp__iso3' => 'TKM', 'atk_fp__numcode' => 795, 'atk_fp__phonecode' => 7370],
    ['id' => 220, 'atk_fp__iso' => 'TC', 'atk_fp_country__name' => 'TURKS AND CAICOS ISLANDS', 'atk_fp_country__nicename' => 'Turks and Caicos Islands', 'atk_fp__iso3' => 'TCA', 'atk_fp__numcode' => 796, 'atk_fp__phonecode' => 1649],
    ['id' => 221, 'atk_fp__iso' => 'TV', 'atk_fp_country__name' => 'TUVALU', 'atk_fp_country__nicename' => 'Tuvalu', 'atk_fp__iso3' => 'TUV', 'atk_fp__numcode' => 798, 'atk_fp__phonecode' => 688],
    ['id' => 222, 'atk_fp__iso' => 'UG', 'atk_fp_country__name' => 'UGANDA', 'atk_fp_country__nicename' => 'Uganda', 'atk_fp__iso3' => 'UGA', 'atk_fp__numcode' => 800, 'atk_fp__phonecode' => 256],
    ['id' => 223, 'atk_fp__iso' => 'UA', 'atk_fp_country__name' => 'UKRAINE', 'atk_fp_country__nicename' => 'Ukraine', 'atk_fp__iso3' => 'UKR', 'atk_fp__numcode' => 804, 'atk_fp__phonecode' => 380],
    ['id' => 224, 'atk_fp__iso' => 'AE', 'atk_fp_country__name' => 'UNITED ARAB EMIRATES', 'atk_fp_country__nicename' => 'United Arab Emirates', 'atk_fp__iso3' => 'ARE', 'atk_fp__numcode' => 784, 'atk_fp__phonecode' => 971],
    ['id' => 225, 'atk_fp__iso' => 'GB', 'atk_fp_country__name' => 'UNITED KINGDOM', 'atk_fp_country__nicename' => 'United Kingdom', 'atk_fp__iso3' => 'GBR', 'atk_fp__numcode' => 826, 'atk_fp__phonecode' => 44],
    ['id' => 226, 'atk_fp__iso' => 'US', 'atk_fp_country__name' => 'UNITED STATES', 'atk_fp_country__nicename' => 'United States', 'atk_fp__iso3' => 'USA', 'atk_fp__numcode' => 840, 'atk_fp__phonecode' => 1],
    ['id' => 227, 'atk_fp__iso' => 'UM', 'atk_fp_country__name' => 'UNITED STATES MINOR OUTLYING ISLANDS', 'atk_fp_country__nicename' => 'United States Minor Outlying Islands', 'atk_fp__iso3' => '', 'atk_fp__numcode' => '', 'atk_fp__phonecode' => 1],
    ['id' => 228, 'atk_fp__iso' => 'UY', 'atk_fp_country__name' => 'URUGUAY', 'atk_fp_country__nicename' => 'Uruguay', 'atk_fp__iso3' => 'URY', 'atk_fp__numcode' => 858, 'atk_fp__phonecode' => 598],
    ['id' => 229, 'atk_fp__iso' => 'UZ', 'atk_fp_country__name' => 'UZBEKISTAN', 'atk_fp_country__nicename' => 'Uzbekistan', 'atk_fp__iso3' => 'UZB', 'atk_fp__numcode' => 860, 'atk_fp__phonecode' => 998],
    ['id' => 230, 'atk_fp__iso' => 'VU', 'atk_fp_country__name' => 'VANUATU', 'atk_fp_country__nicename' => 'Vanuatu', 'atk_fp__iso3' => 'VUT', 'atk_fp__numcode' => 548, 'atk_fp__phonecode' => 678],
    ['id' => 231, 'atk_fp__iso' => 'VE', 'atk_fp_country__name' => 'VENEZUELA', 'atk_fp_country__nicename' => 'Venezuela', 'atk_fp__iso3' => 'VEN', 'atk_fp__numcode' => 862, 'atk_fp__phonecode' => 58],
    ['id' => 232, 'atk_fp__iso' => 'VN', 'atk_fp_country__name' => 'VIET NAM', 'atk_fp_country__nicename' => 'Viet Nam', 'atk_fp__iso3' => 'VNM', 'atk_fp__numcode' => 704, 'atk_fp__phonecode' => 84],
    ['id' => 233, 'atk_fp__iso' => 'VG', 'atk_fp_country__name' => 'VIRGIN ISLANDS, BRITISH', 'atk_fp_country__nicename' => 'Virgin Islands, British', 'atk_fp__iso3' => 'VGB', 'atk_fp__numcode' => 92, 'atk_fp__phonecode' => 1284],
    ['id' => 234, 'atk_fp__iso' => 'VI', 'atk_fp_country__name' => 'VIRGIN ISLANDS, U.S.', 'atk_fp_country__nicename' => 'Virgin Islands, U.s.', 'atk_fp__iso3' => 'VIR', 'atk_fp__numcode' => 850, 'atk_fp__phonecode' => 1340],
    ['id' => 235, 'atk_fp__iso' => 'WF', 'atk_fp_country__name' => 'WALLIS AND FUTUNA', 'atk_fp_country__nicename' => 'Wallis and Futuna', 'atk_fp__iso3' => 'WLF', 'atk_fp__numcode' => 876, 'atk_fp__phonecode' => 681],
    ['id' => 236, 'atk_fp__iso' => 'EH', 'atk_fp_country__name' => 'WESTERN SAHARA', 'atk_fp_country__nicename' => 'Western Sahara', 'atk_fp__iso3' => 'ESH', 'atk_fp__numcode' => 732, 'atk_fp__phonecode' => 212],
    ['id' => 237, 'atk_fp__iso' => 'YE', 'atk_fp_country__name' => 'YEMEN', 'atk_fp_country__nicename' => 'Yemen', 'atk_fp__iso3' => 'YEM', 'atk_fp__numcode' => 887, 'atk_fp__phonecode' => 967],
    ['id' => 238, 'atk_fp__iso' => 'ZM', 'atk_fp_country__name' => 'ZAMBIA', 'atk_fp_country__nicename' => 'Zambia', 'atk_fp__iso3' => 'ZMB', 'atk_fp__numcode' => 894, 'atk_fp__phonecode' => 260],
    ['id' => 239, 'atk_fp__iso' => 'ZW', 'atk_fp_country__name' => 'ZIMBABWE', 'atk_fp_country__nicename' => 'Zimbabwe', 'atk_fp__iso3' => 'ZWE', 'atk_fp__numcode' => 716, 'atk_fp__phonecode' => 263],
    ['id' => 240, 'atk_fp__iso' => 'RS', 'atk_fp_country__name' => 'SERBIA', 'atk_fp_country__nicename' => 'Serbia', 'atk_fp__iso3' => 'SRB', 'atk_fp__numcode' => 688, 'atk_fp__phonecode' => 381],
    ['id' => 241, 'atk_fp__iso' => 'AP', 'atk_fp_country__name' => 'ASIA PACIFIC REGION', 'atk_fp_country__nicename' => 'Asia / Pacific Region', 'atk_fp__iso3' => '0', 'atk_fp__numcode' => 0, 'atk_fp__phonecode' => 0],
    ['id' => 242, 'atk_fp__iso' => 'ME', 'atk_fp_country__name' => 'MONTENEGRO', 'atk_fp_country__nicename' => 'Montenegro', 'atk_fp__iso3' => 'MNE', 'atk_fp__numcode' => 499, 'atk_fp__phonecode' => 382],
    ['id' => 243, 'atk_fp__iso' => 'AX', 'atk_fp_country__name' => 'ALAND ISLANDS', 'atk_fp_country__nicename' => 'Aland Islands', 'atk_fp__iso3' => 'ALA', 'atk_fp__numcode' => 248, 'atk_fp__phonecode' => 358],
    ['id' => 244, 'atk_fp__iso' => 'BQ', 'atk_fp_country__name' => 'BONAIRE, SINT EUSTATIUS AND SABA', 'atk_fp_country__nicename' => 'Bonaire, Sint Eustatius and Saba', 'atk_fp__iso3' => 'BES', 'atk_fp__numcode' => 535, 'atk_fp__phonecode' => 599],
    ['id' => 245, 'atk_fp__iso' => 'CW', 'atk_fp_country__name' => 'CURACAO', 'atk_fp_country__nicename' => 'Curacao', 'atk_fp__iso3' => 'CUW', 'atk_fp__numcode' => 531, 'atk_fp__phonecode' => 599],
    ['id' => 246, 'atk_fp__iso' => 'GG', 'atk_fp_country__name' => 'GUERNSEY', 'atk_fp_country__nicename' => 'Guernsey', 'atk_fp__iso3' => 'GGY', 'atk_fp__numcode' => 831, 'atk_fp__phonecode' => 44],
    ['id' => 247, 'atk_fp__iso' => 'IM', 'atk_fp_country__name' => 'ISLE OF MAN', 'atk_fp_country__nicename' => 'Isle of Man', 'atk_fp__iso3' => 'IMN', 'atk_fp__numcode' => 833, 'atk_fp__phonecode' => 44],
    ['id' => 248, 'atk_fp__iso' => 'JE', 'atk_fp_country__name' => 'JERSEY', 'atk_fp_country__nicename' => 'Jersey', 'atk_fp__iso3' => 'JEY', 'atk_fp__numcode' => 832, 'atk_fp__phonecode' => 44],
    ['id' => 249, 'atk_fp__iso' => 'XK', 'atk_fp_country__name' => 'KOSOVO', 'atk_fp_country__nicename' => 'Kosovo', 'atk_fp__iso3' => '---', 'atk_fp__numcode' => 0, 'atk_fp__phonecode' => 381],
    ['id' => 250, 'atk_fp__iso' => 'BL', 'atk_fp_country__name' => 'SAINT BARTHELEMY', 'atk_fp_country__nicename' => 'Saint Barthelemy', 'atk_fp__iso3' => 'BLM', 'atk_fp__numcode' => 652, 'atk_fp__phonecode' => 590],
    ['id' => 251, 'atk_fp__iso' => 'MF', 'atk_fp_country__name' => 'SAINT MARTIN', 'atk_fp_country__nicename' => 'Saint Martin', 'atk_fp__iso3' => 'MAF', 'atk_fp__numcode' => 663, 'atk_fp__phonecode' => 590],
    ['id' => 252, 'atk_fp__iso' => 'SX', 'atk_fp_country__name' => 'SINT MAARTEN', 'atk_fp_country__nicename' => 'Sint Maarten', 'atk_fp__iso3' => 'SXM', 'atk_fp__numcode' => 534, 'atk_fp__phonecode' => 1],
    ['id' => 253, 'atk_fp__iso' => 'SS', 'atk_fp_country__name' => 'SOUTH SUDAN', 'atk_fp_country__nicename' => 'South Sudan', 'atk_fp__iso3' => 'SSD', 'atk_fp__numcode' => 728, 'atk_fp__phonecode' => 211],
]);

$model = new Model($persistence, ['table' => 'file']);
$model->addField('atk_fp_file__name', ['type' => 'string']);
$model->addField('atk_fp__type', ['type' => 'string']);
$model->addField('atk_fp__is_folder', ['type' => 'boolean']);
$model->addField('atk_fp__parent_folder_id', ['type' => 'bigint']);
// KEY `fk_file_file_idx` (`atk_fp__parent_folder_id`)
(new \Atk4\Schema\Migration($model))->dropIfExists()->create();
$model->import([
    ['id' => 1, 'atk_fp_file__name' => 'phpunit.xml', 'atk_fp__type' => 'xml', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => null],
    ['id' => 2, 'atk_fp_file__name' => 'LICENSE', 'atk_fp__type' => '', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => null],
    ['id' => 3, 'atk_fp_file__name' => 'Makefile', 'atk_fp__type' => '', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => null],
    ['id' => 4, 'atk_fp_file__name' => 'tests', 'atk_fp__type' => '', 'atk_fp__is_folder' => 1, 'atk_fp__parent_folder_id' => null],
    ['id' => 5, 'atk_fp_file__name' => 'TemplateTest.php', 'atk_fp__type' => 'php', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => 4],
    ['id' => 6, 'atk_fp_file__name' => 'template', 'atk_fp__type' => '', 'atk_fp__is_folder' => 1, 'atk_fp__parent_folder_id' => null],
    ['id' => 7, 'atk_fp_file__name' => 'semantic-ui', 'atk_fp__type' => '', 'atk_fp__is_folder' => 1, 'atk_fp__parent_folder_id' => 6],
    ['id' => 8, 'atk_fp_file__name' => 'tree.html', 'atk_fp__type' => 'html', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => 7],
    ['id' => 9, 'atk_fp_file__name' => 'element.html', 'atk_fp__type' => 'html', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => 7],
    ['id' => 10, 'atk_fp_file__name' => 'button.html', 'atk_fp__type' => 'html', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => 7],
    ['id' => 11, 'atk_fp_file__name' => 'icon.html', 'atk_fp__type' => 'html', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => 7],
    ['id' => 12, 'atk_fp_file__name' => 'element.jade', 'atk_fp__type' => 'jade', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => 7],
    ['id' => 13, 'atk_fp_file__name' => 'tree.jade', 'atk_fp__type' => 'jade', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => 7],
    ['id' => 14, 'atk_fp_file__name' => 'icon.jade', 'atk_fp__type' => 'jade', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => 7],
    ['id' => 15, 'atk_fp_file__name' => 'button.jade', 'atk_fp__type' => 'jade', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => 7],
    ['id' => 16, 'atk_fp_file__name' => 'docs', 'atk_fp__type' => '', 'atk_fp__is_folder' => 1, 'atk_fp__parent_folder_id' => null],
    ['id' => 17, 'atk_fp_file__name' => 'index.rst', 'atk_fp__type' => 'rst', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => 16],
    ['id' => 18, 'atk_fp_file__name' => 'login.png', 'atk_fp__type' => 'png', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => 16],
    ['id' => 19, 'atk_fp_file__name' => 'requirements.txt', 'atk_fp__type' => 'txt', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => 16],
    ['id' => 20, 'atk_fp_file__name' => 'crud2.png', 'atk_fp__type' => 'png', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => 16],
    ['id' => 21, 'atk_fp_file__name' => 'images', 'atk_fp__type' => '', 'atk_fp__is_folder' => 1, 'atk_fp__parent_folder_id' => 16],
    ['id' => 22, 'atk_fp_file__name' => 'folders.png', 'atk_fp__type' => 'png', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => 21],
    ['id' => 23, 'atk_fp_file__name' => 'layout.png', 'atk_fp__type' => 'png', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => 21],
    ['id' => 24, 'atk_fp_file__name' => 'menu.png', 'atk_fp__type' => 'png', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => 21],
    ['id' => 25, 'atk_fp_file__name' => 'Makefile', 'atk_fp__type' => '', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => 16],
    ['id' => 26, 'atk_fp_file__name' => 'conf.py', 'atk_fp__type' => 'py', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => 16],
    ['id' => 27, 'atk_fp_file__name' => 'README.md', 'atk_fp__type' => 'md', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => 16],
    ['id' => 28, 'atk_fp_file__name' => 'quickstart.rst', 'atk_fp__type' => 'rst', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => 16],
    ['id' => 29, 'atk_fp_file__name' => 'crud.png', 'atk_fp__type' => 'png', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => 16],
    ['id' => 30, 'atk_fp_file__name' => 'layouts.png', 'atk_fp__type' => 'png', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => 16],
    ['id' => 31, 'atk_fp_file__name' => 'template.rst', 'atk_fp__type' => 'rst', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => 16],
    ['id' => 32, 'atk_fp_file__name' => 'README.md', 'atk_fp__type' => 'md', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => null],
    ['id' => 33, 'atk_fp_file__name' => 'demos', 'atk_fp__type' => '', 'atk_fp__is_folder' => 1, 'atk_fp__parent_folder_id' => null],
    ['id' => 34, 'atk_fp_file__name' => 'index.php', 'atk_fp__type' => 'php', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => 33],
    ['id' => 35, 'atk_fp_file__name' => 'layout.php', 'atk_fp__type' => 'php', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => 33],
    ['id' => 36, 'atk_fp_file__name' => 'templates', 'atk_fp__type' => '', 'atk_fp__is_folder' => 1, 'atk_fp__parent_folder_id' => 33],
    ['id' => 37, 'atk_fp_file__name' => 'fixed.jade', 'atk_fp__type' => 'jade', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => 36],
    ['id' => 38, 'atk_fp_file__name' => 'layout1.html', 'atk_fp__type' => 'html', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => 36],
    ['id' => 39, 'atk_fp_file__name' => 'layout2.jade', 'atk_fp__type' => 'jade', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => 36],
    ['id' => 40, 'atk_fp_file__name' => 'layout1.jade', 'atk_fp__type' => 'jade', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => 36],
    ['id' => 41, 'atk_fp_file__name' => 'fixed.html', 'atk_fp__type' => 'html', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => 36],
    ['id' => 42, 'atk_fp_file__name' => 'index1.html', 'atk_fp__type' => 'html', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => 36],
    ['id' => 43, 'atk_fp_file__name' => 'layout2.html', 'atk_fp__type' => 'html', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => 36],
    ['id' => 44, 'atk_fp_file__name' => 'button.php', 'atk_fp__type' => 'php', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => 33],
    ['id' => 45, 'atk_fp_file__name' => 'composer.json', 'atk_fp__type' => 'json', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => null],
    ['id' => 46, 'atk_fp_file__name' => 'src', 'atk_fp__type' => '', 'atk_fp__is_folder' => 1, 'atk_fp__parent_folder_id' => null],
    ['id' => 47, 'atk_fp_file__name' => 'Icon.php', 'atk_fp__type' => 'php', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => 46],
    ['id' => 48, 'atk_fp_file__name' => 'App.php', 'atk_fp__type' => 'php', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => 46],
    ['id' => 49, 'atk_fp_file__name' => 'Label.php', 'atk_fp__type' => 'php', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => 46],
    ['id' => 50, 'atk_fp_file__name' => 'Layout', 'atk_fp__type' => '', 'atk_fp__is_folder' => 1, 'atk_fp__parent_folder_id' => 46],
    ['id' => 51, 'atk_fp_file__name' => 'App.php', 'atk_fp__type' => 'php', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => 50],
    ['id' => 52, 'atk_fp_file__name' => 'MiniApp.php', 'atk_fp__type' => 'php', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => 46],
    ['id' => 53, 'atk_fp_file__name' => 'Lister.php', 'atk_fp__type' => 'php', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => 46],
    ['id' => 54, 'atk_fp_file__name' => 'Layout.php', 'atk_fp__type' => 'php', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => 46],
    ['id' => 55, 'atk_fp_file__name' => 'Buttons.php', 'atk_fp__type' => 'php', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => 46],
    ['id' => 56, 'atk_fp_file__name' => 'View.php', 'atk_fp__type' => 'php', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => 46],
    ['id' => 57, 'atk_fp_file__name' => 'Tree.php', 'atk_fp__type' => 'php', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => 46],
    ['id' => 58, 'atk_fp_file__name' => 'Template.php', 'atk_fp__type' => 'php', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => 46],
    ['id' => 59, 'atk_fp_file__name' => 'Exception.php', 'atk_fp__type' => 'php', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => 46],
    ['id' => 60, 'atk_fp_file__name' => 'Text.php', 'atk_fp__type' => 'php', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => 46],
    ['id' => 61, 'atk_fp_file__name' => 'Button.php', 'atk_fp__type' => 'php', 'atk_fp__is_folder' => 0, 'atk_fp__parent_folder_id' => 46],
]);

$model = new Model($persistence, ['table' => 'stats']);
$model->addField('atk_fp__project_name', ['type' => 'string']);
$model->addField('atk_fp__project_code', ['type' => 'string']);
$model->addField('atk_fp__description', ['type' => 'text']);
$model->addField('atk_fp__client_name', ['type' => 'string']);
$model->addField('atk_fp__client_address', ['type' => 'text']);
$model->addField('atk_fp__client_country_iso', ['type' => 'string']); // should be CHAR(2)
$model->addField('atk_fp__is_commercial', ['type' => 'boolean']);
$model->addField('atk_fp__currency', ['type' => 'string']); // should be ENUM('EUR' ,'USD', 'GBP') or CHAR(3)
$model->addField('atk_fp__is_completed', ['type' => 'boolean']);
$model->addField('atk_fp__project_budget', ['type' => 'float']);
$model->addField('atk_fp__project_invoiced', ['type' => 'float']);
$model->addField('atk_fp__project_paid', ['type' => 'float']);
$model->addField('atk_fp__project_hour_cost', ['type' => 'float']);
$model->addField('atk_fp__project_hours_est', ['type' => 'integer']);
$model->addField('atk_fp__project_hours_reported', ['type' => 'integer']);
$model->addField('atk_fp__project_expenses_est', ['type' => 'float']);
$model->addField('atk_fp__project_expenses', ['type' => 'float']);
$model->addField('atk_fp__project_mgmt_cost_pct', ['type' => 'float']);
$model->addField('atk_fp__project_qa_cost_pct', ['type' => 'float']);
$model->addField('atk_fp__start_date', ['type' => 'date']);
$model->addField('atk_fp__finish_date', ['type' => 'date']);
$model->addField('atk_fp__finish_time', ['type' => 'time']);
$model->addField('atk_fp__created', ['type' => 'datetime']);
$model->addField('atk_fp__updated', ['type' => 'datetime']);
(new \Atk4\Schema\Migration($model))->dropIfExists()->create();
$model->import([
    ['id' => 1, 'atk_fp__project_name' => 'Agile DSQL', 'atk_fp__project_code' => 'at01', 'atk_fp__description' => 'DSQL is a composable SQL query builder. You can write multi-vendor queries in PHP profiting from better security, clean syntax and avoid human errors.', 'atk_fp__client_name' => 'Agile Toolkit', 'atk_fp__client_address' => 'Some Street,\nGarden City\nUK', 'atk_fp__client_country_iso' => 'GB', 'atk_fp__is_commercial' => 0, 'atk_fp__currency' => 'GBP', 'atk_fp__is_completed' => 1, 'atk_fp__project_budget' => 7000, 'atk_fp__project_invoiced' => 0, 'atk_fp__project_paid' => 0, 'atk_fp__project_hour_cost' => 0, 'atk_fp__project_hours_est' => 150, 'atk_fp__project_hours_reported' => 125, 'atk_fp__project_expenses_est' => 50, 'atk_fp__project_expenses' => 0, 'atk_fp__project_mgmt_cost_pct' => 0.1, 'atk_fp__project_qa_cost_pct' => 0.2, 'atk_fp__start_date' => '2016-01-26', 'atk_fp__finish_date' => '2016-06-23', 'atk_fp__finish_time' => '12:50:00', 'atk_fp__created' => '2017-04-06 10:34:34', 'atk_fp__updated' => '2017-04-06 10:35:04'],
    ['id' => 2, 'atk_fp__project_name' => 'Agile Core', 'atk_fp__project_code' => 'at02', 'atk_fp__description' => 'Collection of PHP Traits for designing object-oriented frameworks.', 'atk_fp__client_name' => 'Agile Toolkit', 'atk_fp__client_address' => 'Some Street,\nGarden City\nUK', 'atk_fp__client_country_iso' => 'GB', 'atk_fp__is_commercial' => 0, 'atk_fp__currency' => 'GBP', 'atk_fp__is_completed' => 1, 'atk_fp__project_budget' => 3000, 'atk_fp__project_invoiced' => 0, 'atk_fp__project_paid' => 0, 'atk_fp__project_hour_cost' => 0, 'atk_fp__project_hours_est' => 70, 'atk_fp__project_hours_reported' => 56, 'atk_fp__project_expenses_est' => 50, 'atk_fp__project_expenses' => 0, 'atk_fp__project_mgmt_cost_pct' => 0.1, 'atk_fp__project_qa_cost_pct' => 0.2, 'atk_fp__start_date' => '2016-04-27', 'atk_fp__finish_date' => '2016-05-21', 'atk_fp__finish_time' => '18:41:00', 'atk_fp__created' => '2017-04-06 10:21:50', 'atk_fp__updated' => '2017-04-06 10:35:04'],
    ['id' => 3, 'atk_fp__project_name' => 'Agile Data', 'atk_fp__project_code' => 'at03', 'atk_fp__description' => 'Agile Data implements an entirely new pattern for data abstraction, that is specifically designed for remote databases such as RDS, Cloud SQL, BigQuery and other distributed data storage architectures. It focuses on reducing number of requests your App have to send to the Database by using more sophisticated queries while also offering full Domain Model mapping and Database vendor abstraction.', 'atk_fp__client_name' => 'Agile Toolkit', 'atk_fp__client_address' => 'Some Street,\nGarden City\nUK', 'atk_fp__client_country_iso' => 'GB', 'atk_fp__is_commercial' => 0, 'atk_fp__currency' => 'GBP', 'atk_fp__is_completed' => 1, 'atk_fp__project_budget' => 12000, 'atk_fp__project_invoiced' => 0, 'atk_fp__project_paid' => 0, 'atk_fp__project_hour_cost' => 0, 'atk_fp__project_hours_est' => 300, 'atk_fp__project_hours_reported' => 394, 'atk_fp__project_expenses_est' => 600, 'atk_fp__project_expenses' => 430, 'atk_fp__project_mgmt_cost_pct' => 0.2, 'atk_fp__project_qa_cost_pct' => 0.3, 'atk_fp__start_date' => '2016-04-17', 'atk_fp__finish_date' => '2016-06-20', 'atk_fp__finish_time' => '03:04:00', 'atk_fp__created' => '2017-04-06 10:30:15', 'atk_fp__updated' => '2017-04-06 10:35:04'],
    ['id' => 4, 'atk_fp__project_name' => 'Agile UI', 'atk_fp__project_code' => 'at04', 'atk_fp__description' => 'Web UI Component library.', 'atk_fp__client_name' => 'Agile Toolkit', 'atk_fp__client_address' => 'Some Street,\nGarden City\nUK', 'atk_fp__client_country_iso' => 'GB', 'atk_fp__is_commercial' => 0, 'atk_fp__currency' => 'GBP', 'atk_fp__is_completed' => 0, 'atk_fp__project_budget' => 20000, 'atk_fp__project_invoiced' => 0, 'atk_fp__project_paid' => 0, 'atk_fp__project_hour_cost' => 0, 'atk_fp__project_hours_est' => 600, 'atk_fp__project_hours_reported' => 368, 'atk_fp__project_expenses_est' => 1200, 'atk_fp__project_expenses' => 0, 'atk_fp__project_mgmt_cost_pct' => 0.3, 'atk_fp__project_qa_cost_pct' => 0.4, 'atk_fp__start_date' => '2016-09-17', 'atk_fp__finish_date' => '', 'atk_fp__finish_time' => '', 'atk_fp__created' => '2017-04-06 10:30:15', 'atk_fp__updated' => '2017-04-06 10:35:04'],
]);

$model = new Model($persistence, ['table' => 'product_category']);
$model->addField('atk_fp_product_category__name', ['type' => 'string']);
(new \Atk4\Schema\Migration($model))->dropIfExists()->create();
$model->import([
    ['id' => 1, 'atk_fp_product_category__name' => 'Condiments and Gravies'],
    ['id' => 2, 'atk_fp_product_category__name' => 'Beverages'],
    ['id' => 3, 'atk_fp_product_category__name' => 'Dairy'],
]);

$model = new Model($persistence, ['table' => 'product_sub_category']);
$model->addField('atk_fp_product_sub_category__name', ['type' => 'string']);
$model->addField('atk_fp_product_sub_category__product_category_id', ['type' => 'bigint']);
(new \Atk4\Schema\Migration($model))->dropIfExists()->create();
$model->import([
    ['id' => 1, 'atk_fp_product_sub_category__name' => 'Gravie', 'atk_fp_product_sub_category__product_category_id' => 1],
    ['id' => 2, 'atk_fp_product_sub_category__name' => 'Spread', 'atk_fp_product_sub_category__product_category_id' => 1],
    ['id' => 3, 'atk_fp_product_sub_category__name' => 'Salad Dressing', 'atk_fp_product_sub_category__product_category_id' => 1],
    ['id' => 4, 'atk_fp_product_sub_category__name' => 'Alcoholic', 'atk_fp_product_sub_category__product_category_id' => 2],
    ['id' => 5, 'atk_fp_product_sub_category__name' => 'Coffee and Tea', 'atk_fp_product_sub_category__product_category_id' => 2],
    ['id' => 6, 'atk_fp_product_sub_category__name' => 'Lowfat Milk', 'atk_fp_product_sub_category__product_category_id' => 3],
    ['id' => 7, 'atk_fp_product_sub_category__name' => 'Yogourt', 'atk_fp_product_sub_category__product_category_id' => 3],
    ['id' => 8, 'atk_fp_product_sub_category__name' => 'HighFat', 'atk_fp_product_sub_category__product_category_id' => 3],
    ['id' => 9, 'atk_fp_product_sub_category__name' => 'Sugar/Sweetened', 'atk_fp_product_sub_category__product_category_id' => 2],
]);

$model = new Model($persistence, ['table' => 'product']);
$model->addField('atk_fp_product__name', ['type' => 'string']);
$model->addField('atk_fp__brand', ['type' => 'string']);
$model->addField('atk_fp__product_category_id', ['type' => 'bigint']);
$model->addField('atk_fp__product_sub_category_id', ['type' => 'bigint']);
(new \Atk4\Schema\Migration($model))->dropIfExists()->create();
$model->import([
    ['id' => 1, 'atk_fp_product__name' => 'Mustard', 'atk_fp__brand' => 'Condiment Corp.', 'atk_fp__product_category_id' => 1, 'atk_fp__product_sub_category_id' => 2],
    ['id' => 2, 'atk_fp_product__name' => 'Ketchup', 'atk_fp__brand' => 'Condiment Corp.', 'atk_fp__product_category_id' => 1, 'atk_fp__product_sub_category_id' => 2],
    ['id' => 3, 'atk_fp_product__name' => 'Cola', 'atk_fp__brand' => 'Beverage Corp.', 'atk_fp__product_category_id' => 2, 'atk_fp__product_sub_category_id' => 9],
    ['id' => 4, 'atk_fp_product__name' => 'Soda', 'atk_fp__brand' => 'Beverage Corp.', 'atk_fp__product_category_id' => 2, 'atk_fp__product_sub_category_id' => 9],
    ['id' => 5, 'atk_fp_product__name' => 'Milk 2%', 'atk_fp__brand' => 'Milk Corp.', 'atk_fp__product_category_id' => 3, 'atk_fp__product_sub_category_id' => 8],
    ['id' => 6, 'atk_fp_product__name' => 'Milk 1%', 'atk_fp__brand' => 'Milk Corp.', 'atk_fp__product_category_id' => 3, 'atk_fp__product_sub_category_id' => 6],
    ['id' => 7, 'atk_fp_product__name' => 'Ice Cream', 'atk_fp__brand' => 'Milk Corp.', 'atk_fp__product_category_id' => 3, 'atk_fp__product_sub_category_id' => 8],
]);

echo 'import complete!' . "\n";

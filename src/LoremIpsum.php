<?php

declare(strict_types=1);

namespace Atk4\Ui;

/**
 * Provides a handy object that generates some amount of random text-filler.
 */
class LoremIpsum extends Text
{
    /**
     * Specifies amount of filler you need. This value affects number of
     * paragraphs and amount of text per-paragraph.
     */
    public int $size = 3;

    /** Base amount of words per paragraph. */
    public int $words = 50;

    /**
     * @param array<string, mixed>|int $defaults
     */
    public function __construct($defaults = [])
    {
        if (is_scalar($defaults)) {
            $defaults = ['size' => $defaults];
        }

        parent::__construct($defaults);
    }

    /**
     * Returns string of LoremIpsum text.
     *
     * @return string "Lorem Ipsum" text
     */
    public function generateLorem(int $words)
    {
        $punctuation = ['. ', '. ', '. ', '. ', '. ', '. ', '. ', '. ', '... ', '! ', '? '];

        $dictionary = ['abbas', 'abdo', 'abico', 'abigo', 'abluo', 'accumsan',
            'acsi', 'ad', 'adipiscing', 'aliquam', 'aliquip', 'amet', 'antehabeo',
            'appellatio', 'aptent', 'at', 'augue', 'autem', 'bene', 'blandit',
            'brevitas', 'caecus', 'camur', 'capto', 'causa', 'cogo', 'comis',
            'commodo', 'commoveo', 'consectetuer', 'consequat', 'conventio', 'cui',
            'damnum', 'decet', 'defui', 'diam', 'dignissim', 'distineo', 'dolor',
            'dolore', 'dolus', 'duis', 'ea', 'eligo', 'elit', 'enim', 'erat',
            'eros', 'esca', 'esse', 'et', 'eu', 'euismod', 'eum', 'ex', 'exerci',
            'exputo', 'facilisi', 'facilisis', 'fere', 'feugiat', 'gemino',
            'genitus', 'gilvus', 'gravis', 'haero', 'hendrerit', 'hos', 'huic',
            'humo', 'iaceo', 'ibidem', 'ideo', 'ille', 'illum', 'immitto',
            'importunus', 'imputo', 'in', 'incassum', 'inhibeo', 'interdico',
            'iriure', 'iusto', 'iustum', 'jugis', 'jumentum', 'jus', 'laoreet',
            'lenis', 'letalis', 'lobortis', 'loquor', 'lucidus', 'luctus', 'ludus',
            'luptatum', 'macto', 'magna', 'mauris', 'melior', 'metuo', 'meus',
            'minim', 'modo', 'molior', 'mos', 'natu', 'neo', 'neque', 'nibh',
            'nimis', 'nisl', 'nobis', 'nostrud', 'nulla', 'nunc', 'nutus', 'obruo',
            'occuro', 'odio', 'olim', 'oppeto', 'os', 'pagus', 'pala', 'paratus',
            'patria', 'paulatim', 'pecus', 'persto', 'pertineo', 'plaga', 'pneum',
            'populus', 'praemitto', 'praesent', 'premo', 'probo', 'proprius',
            'quadrum', 'quae', 'qui', 'quia', 'quibus', 'quidem', 'quidne', 'quis',
            'ratis', 'refero', 'refoveo', 'roto', 'rusticus', 'saepius',
            'sagaciter', 'saluto', 'scisco', 'secundum', 'sed', 'si', 'similis',
            'singularis', 'sino', 'sit', 'sudo', 'suscipere', 'suscipit', 'tamen',
            'tation', 'te', 'tego', 'tincidunt', 'torqueo', 'tum', 'turpis',
            'typicus', 'ulciscor', 'ullamcorper', 'usitas', 'ut', 'utinam',
            'utrum', 'uxor', 'valde', 'valetudo', 'validus', 'vel', 'velit',
            'veniam', 'venio', 'vereor', 'vero', 'verto', 'vicis', 'vindico',
            'virtus', 'voco', 'volutpat', 'vulpes', 'vulputate', 'wisi', 'ymo',
            'zelus'];

        $lorem = '';

        while ($words > 0) {
            $sentenceLength = random_int(3, 10);

            $lorem .= ucfirst($dictionary[array_rand($dictionary)]);
            for ($i = 1; $i < $sentenceLength; ++$i) {
                $lorem .= ' ' . $dictionary[array_rand($dictionary)];
            }

            $lorem .= $punctuation[array_rand($punctuation)];
            $words -= $sentenceLength;
        }

        return $lorem;
    }

    #[\Override]
    protected function init(): void
    {
        parent::init();

        for ($x = 0; $x < $this->size; ++$x) {
            $this->addParagraph($this->generateLorem($this->words * $this->size));
        }
    }
}

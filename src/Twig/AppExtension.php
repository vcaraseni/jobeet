<?php

declare(strict_types=1);

namespace App\Twig;


use App\Entity\LikeNotification;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension implements GlobalsInterface
{
    /** @var string */
    private $locale;

//    /**
//     * @param string $locale
//     */
//    public function __construct(string $locale)
//    {
//        $this->locale = $locale;
//    }

    public function getFilters()
    {
        return [
            new TwigFilter('price', [$this, 'priceFilter']),
        ];
    }

    /**
     * @return array|void
     */
    public function getGlobals()
    {
        return [
            'locale' => $this->locale,
        ];
    }

    /**
     * @param $number
     *
     * @return string
     */
    public function priceFilter($number)
    {
        return '$'.number_format($number, 2, '.', ',');
    }

    public function getTests()
    {
        return [
            new \Twig_SimpleTest('like', function ($obj) {
                return $obj instanceof LikeNotification;
            }),
        ];
    }
}
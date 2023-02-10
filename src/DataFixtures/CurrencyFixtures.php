<?php

namespace App\DataFixtures;

use App\Entity\Currency;
use Doctrine\Persistence\ObjectManager;
use function Sodium\add;

class CurrencyFixtures extends \Doctrine\Bundle\FixturesBundle\Fixture
{

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $currencies = array(
            array('code' => 'DZD','name' => 'Dinar Algérien','symbol' => 'دج'),
            array('code' => 'EUR','name' => 'Euro','symbol' => '€'),
            array('code' => 'CAD','name' => 'Dollar canadien','symbol' => '$'),
            array('code' => 'EGP','name' => 'EGP','symbol' => 'ج.م'),
            array('code' => 'JPY','name' => 'JPY','symbol' => '¥'),
            array('code' => 'LBP','name' => 'LBP','symbol' => '£'),
            array('code' => 'MVR','name' => 'MVR','symbol' => 'Rf'),
            array('code' => 'MAD','name' => 'MAD','symbol' => 'DH'),
            array('code' => 'QAR','name' => 'QAR','symbol' => 'ق.ر'),
            array('code' => 'SAR','name' => 'SAR','symbol' => '﷼'),
            array('code' => 'AED','name' => 'AED','symbol' => 'إ.د'),
            array('code' => 'GBP','name' => 'GBP','symbol' => '£'),
            array('code' => 'TND','name' => 'TND','symbol' => 'ت.د'),
            array('code' => 'USD','name' => 'USD','symbol' => '$'),
        );

        foreach ($currencies as $c){
            $currency = new Currency();
            $currency->setCode($c['code']);
            $currency->setName($c['name']);
            $currency->setSymbol($c['symbol']);
            $this->addReference('Currency_'.$c['code'], $currency);
            $manager->persist($currency);
        }
        $manager->flush();
    }
}
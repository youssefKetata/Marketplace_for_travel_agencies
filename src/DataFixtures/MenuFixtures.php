<?php

namespace App\DataFixtures;

use App\Entity\Currency;
use App\Entity\MenuItemAdmin;
use Doctrine\Persistence\ObjectManager;
use function Sodium\add;

class MenuFixtures extends \Doctrine\Bundle\FixturesBundle\Fixture
{

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $menus = array(
            array('id' => 'ADMIN', 'title' => 'ADMIN', 'route' => '/', 'displayOrder' => 0, 'parent' => ''),
            array('id' => 'MENU', 'title' => 'Menu', 'route' => '/', 'displayOrder' => 1, 'parent' => 'ADMIN'),
            array('id' => 'SOUS_MENU', 'title' => 'Sous Menu', 'route' => '/', 'displayOrder' => 1, 'parent' => 'MENU'),
            array('id' => 'SOUS_MENU_1', 'title' => 'Sous Menu 1', 'route' => '/', 'displayOrder' => 1, 'parent' => 'SOUS_MENU'),

        );

        foreach ($menus as $c) {
            $menu = new MenuItemAdmin();
            $menu->setId($c['id']);
            $menu->setTitle($c['title']);
            $menu->setRoute($c['route']);
            $menu->setDisplayOrder($c['displayOrder']);
            $menu->setParent($c['parent']);
            $manager->persist($menu);
        }
        $manager->flush();
    }
}
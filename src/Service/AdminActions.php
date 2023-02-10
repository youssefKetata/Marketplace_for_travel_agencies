<?php

namespace App\Service;


use Symfony\Contracts\Translation\TranslatorInterface;

class AdminActions
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    private static array $dt_actions = [
        "standard" => array(
            array("action_label" => "Afficher", "action_link" => "/"),
            array("action_label" => "Modifier", "action_link" => "/edit"),
            array("action_label" => "Supprimer", "action_link" => "/delete")
        ),
        "personal" => array(
            array("action_label" => "Afficher", "action_link" => "/"),
            array("action_label" => "Modifier", "action_link" => "/edit"),
            array("action_label" => "Supprimer", "action_link" => "/delete"),
            array("action_label" => "Créer Compte", "action_link" => "/create_account")
        )
    ];

    public function getDataTableAction($entity): array
    {
        if (array_key_exists($entity, self::$dt_actions)) {
            return self::$dt_actions[$entity];
        } else {
            return self::$dt_actions['standard'];
        }
    }

    public function getMenuTabs(): array
    {
        $menu_tabs = [
            "hotel" => [
                ["designation" => $this->translator->trans('Menu.Hotel.Info'), 'route' => 'app_admin_hotel_edit', 'order' => 1, 'key' => 'HOTEL_INFOS'],
                ["designation" => $this->translator->trans('Menu.Hotel.Room'), 'route' => 'app_admin_provider_hotel_room_type_index', 'order' => 2, 'key' => 'HOTEL_ROOM_TYPE'],//app_admin_hotel_list_room_type
                ["designation" => $this->translator->trans('Menu.Hotel.Facilities'), 'route' => 'app_admin_hotel_edit_facilities', 'order' => 3, 'key' => 'HOTEL_FACILITIES'],
                ["designation" => $this->translator->trans('Menu.Hotel.Options'), 'route' => 'app_admin_hotel_edit_options', 'order' => 4, 'key' => 'HOTEL_OPTIONS'],
                ["designation" => $this->translator->trans('Menu.Hotel.Conditions'), 'route' => 'app_admin_hotel_edit_conditions', 'order' => 5, 'key' => 'HOTEL_CONDITIONS'],
                ["designation" => $this->translator->trans('Menu.Hotel.Gallery'), 'route' => 'app_admin_edit_images', 'order' => 6, 'key' => 'HOTEL_IMAGES'],

            ],
            "location" => [
                ["designation" => 'Liste des continents', 'route' => 'app_admin_location_continent_index', 'order' => 1],
                ["designation" => 'Liste des pays', 'route' => 'app_admin_location_country_index', 'order' => 2],
                ["designation" => 'Liste des villes', 'route' => 'app_admin_location_city_index', 'order' => 3],
            ]
        ];
        return $menu_tabs;
    }


    public  array $facilitiesIcons = [
        0 => 'fas fa-swimming-pool',
        1 =>'fas fa-utensils',
        2 =>'fas fa-paw',
        3 =>'fas fa-bed',
        4 =>'fas fa-parking',
        5 =>'fas fa-snowflake',
        6 =>'fas fa-tv',
        7 =>'fas fa-wifi',
        8 =>'fas fa-smoking-ban',
        9 =>'mdi mdi-elevator-passenger',
        10 =>'fas fa-wheelchair',
        11 =>'fas fa-glass-martini-alt',
        12 =>'mdi mdi-flower-tulip',
        13 =>'fas fa-spa',
        14 =>'fas fa-dumbbell',
        15 =>'fas fa-coffee',
        16 =>'fas fa-bath',
        17 =>'fas fa-baby-carriage',
        18 =>'fas fa-briefcase',
        19 =>'fas fa-cocktail',
        20 =>'fas fa-door-closed',
        21 =>'fas fa-door-open',
        22 =>'fas fa-key',
        23 =>'fas fa-luggage-cart',
        24 =>'fas fa-shower',
        25 =>'fas fa-shuttle-van',
        26 =>'fas fa-smoking',
        27 =>'fas fa-umbrella-beach',
        28 =>'fas fa-first-aid'
    ];

    public function getFaciulityIcones(): array
    {
        return $this->facilitiesIcons;
    }
}
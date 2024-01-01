<?php

/**
 * Configuration settings used by all of the examples.
 *
 * Specify your eBay application keys in the appropriate places.
 *
 * Be careful not to commit this file into an SCM repository.
 * You risk exposing your eBay application keys to more people than intended.
 */
return [

  // get form ebay sites
  'environment'         => false,
  'debug'              => false,
  'compatabilityLevel' => 681,
  'findingVer'         => '1.8.0',
  'siteID'             => 0,

  // Sandbox (Test) Environment
  'sandbox' => [
    // credentials
    'devID'       => '3485ac97-486f-409c-888e-7c058a9c4010',             // insert your devID for sandbox
    'client_id'       => 'AAwalHad-FullStro-SBX-df492a7b2-67d99ce2',    // appID, client_id and clientID same
    'client_secret'      => 'SBX-f48985aef73e-419c-46c8-9869-7296',      // certID, client_secret  same// 
    'redirect_uri' => "fullstro_Invent-AAwalHad-FullSt-bhqckh",  // app runnane
    'auth_url'    => 'https://auth.sandbox.ebay.com',    // sandbox auth url
    'api_url'    => 'https://api.sandbox.ebay.com',       // sandbox api_url

    // tokens
    'consent_token' => 'v^1.1#i^1#f^0#r^1#I^3#p^3#t^Ul41XzI6RkY0NjNDNUUyMUQ5Mzk0ODZDQUU0MDk1MEQ1QTcwMkFfMF8xI0VeMTI4NA==',
    // refresh token
    'refresh_token'            => 'v^1.1#i^1#r^1#I^3#p^3#f^0#t^Ul4xMF84OkE2M0JDRUVBMTIxQUJGRDM4MDQxN0IzNTRENkU0NkM5XzNfMSNFXjEyODQ=',
    'refresh_token_expires_in' => '47304000',
    'refresh_token_created_at' => '',
    // user access token
    'user_access_token' => 'v^1.1#i^1#f^0#r^0#I^3#p^3#t^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',
    'expires_in'        => '7200',
    'created_at'        => '',

    //  access token
    'access_token' => 'v^1.1#i^1#f^0#I^3#p^3#r^0#t^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',


    // scopes
    'scopes' => "https://api.ebay.com/oauth/api_scope https://api.ebay.com/oauth/api_scope/buy.order.readonly https://api.ebay.com/oauth/api_scope/buy.guest.order https://api.ebay.com/oauth/api_scope/sell.marketing.readonly https://api.ebay.com/oauth/api_scope/sell.marketing https://api.ebay.com/oauth/api_scope/sell.inventory.readonly https://api.ebay.com/oauth/api_scope/sell.inventory https://api.ebay.com/oauth/api_scope/sell.account.readonly https://api.ebay.com/oauth/api_scope/sell.account https://api.ebay.com/oauth/api_scope/sell.fulfillment.readonly https://api.ebay.com/oauth/api_scope/sell.fulfillment https://api.ebay.com/oauth/api_scope/sell.analytics.readonly https://api.ebay.com/oauth/api_scope/sell.marketplace.insights.readonly https://api.ebay.com/oauth/api_scope/commerce.catalog.readonly https://api.ebay.com/oauth/api_scope/buy.shopping.cart https://api.ebay.com/oauth/api_scope/buy.offer.auction https://api.ebay.com/oauth/api_scope/commerce.identity.readonly https://api.ebay.com/oauth/api_scope/commerce.identity.email.readonly https://api.ebay.com/oauth/api_scope/commerce.identity.phone.readonly https://api.ebay.com/oauth/api_scope/commerce.identity.address.readonly https://api.ebay.com/oauth/api_scope/commerce.identity.name.readonly https://api.ebay.com/oauth/api_scope/commerce.identity.status.readonly https://api.ebay.com/oauth/api_scope/sell.finances https://api.ebay.com/oauth/api_scope/sell.payment.dispute https://api.ebay.com/oauth/api_scope/sell.item.draft https://api.ebay.com/oauth/api_scope/sell.item https://api.ebay.com/oauth/api_scope/sell.reputation https://api.ebay.com/oauth/api_scope/sell.reputation.readonly https://api.ebay.com/oauth/api_scope/commerce.notification.subscription https://api.ebay.com/oauth/api_scope/commerce.notification.subscription.readonly https://api.ebay.com/oauth/api_scope/sell.stores https://api.ebay.com/oauth/api_scope/sell.stores.readonly",
  ],

  // Production Environment
  'production' => [
    // credentials
    'devID'         => '',                                           // these prod keys are different from sandbox keys
    'client_id'     => 'AAwalHad-FullStro-SBX-df492a7b2-67d99ce2',   // appID and clientID same
    'client_secret' => 'SBX-f48985aef73e-419c-46c8-9869-7296',       // certID, client_secret  same// 
    'redirect_uri'  => "fullstro_Invent-AAwalHad-FullSt-bhqckh",     // app runnane
    'auth_url'      => 'https://auth.ebay.com',                      // production auth url
    'api_url'       => 'https://api.ebay.com',                       // production api_url

    // tokens
    'consent_token' => 'v^1.1#i^1#f^0#r^1#I^3#p^3#t^Ul41XzI6RkY0NjNDNUUyMUQ5Mzk0ODZDQUU0MDk1MEQ1QTcwMkFfMF8xI0VeMTI4NA==',
    // refresh token
    'refresh_token'            => 'v^1.1#i^1#r^1#I^3#p^3#f^0#t^Ul4xMF84OkE2M0JDRUVBMTIxQUJGRDM4MDQxN0IzNTRENkU0NkM5XzNfMSNFXjEyODQ=',
    'refresh_token_expires_in' => '47304000',
    'refresh_token_created_at' => '',
    // user access token
    'user_access_token' => 'v^1.1#i^1#f^0#r^0#I^3#p^3#t^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',
    'expires_in'        => '7200',
    'created_at'        => '',

    //  access token
    'access_token'      => 'v^1.1#i^1#f^0#I^3#p^3#r^0#t^H4sIAAAAAAAAAOVZf2wbVx2PEyel2toi6MZAkzBeYYJy9v2KfXeaDU7iNO6axLWTLI1Eo+e7d/Zrznfne+/iWBtbFHXVoNq0dYJNk4Cq/asUbYjBxo/BNEGFKqSNFAQi/NSmTkho4oemsf0B5d05SeOgNrE9UWtYUex79/31+f56v9jFvp2fOjFy4p+7Aju6Ty+yi92BAHcTu7Ovd//unu6P9HaxGwgCpxf3LQaXev58FwZlw1ZyENuWiWFooWyYWPEHE2HXMRULYIQVE5QhVoiq5FOjhxQ+wiq2YxFLtYxwKDOUCIO4HmMBq7G8xsZVng6aayInrERYZ3kYKwC+IMkcJ0jee4xdmDExASZJhHmWFxiOZ3hpguMUVlRELsLL3Ew4NAUdjCyTkkTYcNK3VvF5nQ2mXt9SgDF0CBUSTmZSw/nxVGYoPTZxV3SDrOSqG/IEEBc3Pg1aGgxNAcOF11eDfWol76oqxDgcTdY1NApVUmvGtGC+72lOi7NSjJc1VpdVEFffFVcOW04ZkOvb4Y0gjdF9UgWaBJHaVh6l3igcgypZfRqjIjJDIe/rsAsMpCPoJMLpgdSRyXw6Fw7ls1nHmkca1HykgijInCjHwkkCMXUhdGZBFRgloKFVXXWBq57epGzQMjXk+Q2HxiwyAKnhsNE9rNK/wT2UaNwcd1I68YzaSCesuVGKz3hxrQfSJSXTCy0sU1+E/Metg7CWFVfz4N3KC6iJgibGOAFIgspLm9PCq/VWUiPpRSeVzUY9U2AB1JgycOYgsQ2gQkal3nXL0EGaIvTrvCDpkNFiss6Isq4zhX4txnA6hCyEhYIqS/9nGUKIgwougetZsvmFjzMRzquWDbOWgdRaeDOJ33hWc2IBJ8IlQmwlGq1Wq5GqELGcYpRnWS46PXoor5ZgGYTXadHWxAzy00OFlAsjhdRsas0CTT6q3CyGk4KjZYFDagNujT7noWHQr7UEbrAwuXn0GlAHDUT9MEEVdRbSEQsTqLUFTYPzSIWzSLvhyLxab0DHcG0hM6wiMkchKVk3HlsDLq8nZIbawkY7KCCdhYqLs0KsPy6J/W0hS9l2plx2CSgYMNNhgRM5no+3B8923Q4otQZUcgWJTk2tEafSFjRvmlUQ0BVizUHz+s3Sq/UbgTWXHs6l8yOzE+N3p8faQpuDugNxacLD2ml5mjqcSqfoZ3RoRirmoOBkKvN5tT+esYtVo1YcgnNATvUXDw9Vj0hFeToF5mPykdxCio0RDdjHKvyIWhAPzGQPVhOJtpyUh6oDO6xPDUhoULgnG92P2MGD9oEc/QcrOnaqQrwijh3MDM6PTx86OKpnsNge+Imty+BG4HfqiTvrV+ksfWoLZLq4ZT/zav1/DRLIckGnf5ykskAUNT7OQ0GKa7qua7qsS21PUZ1W8Sm6fxgBGjPsGkaeOBaTH5hmNF2UeRAv8EwsrsmyCvk256736tSFva1MZ0Hz+DEVAGwU8WbWiGqVoxag+3VvaNa3OLQdomjBrVH9GnQiDgSaZRq17fMVXbpBrXNvi8mr9SimO65Ifb9NoTSptZG5CR5kztM9muXUWlG4ztwED1BVyzVJK+pWWZvg0F1DR4bhbcdbUbiBvRkzTWDUCFJx6zH0D1yoezEqlkizcuhYGTqUXwUE0O1cCwmMS5Zte1moAmeb0P160XVaL8BV/bOt5oxFWv2UsVWw6/y0SyCjbSl2yTJh21KAptFVQ0MAvVpvSZZ3KNi2QfVz65ZqAZle38VNsNig5leehrDtzRpNNBYCyxHNAXozdecxNUHuQGoU2H6mbmJqNRSmRZCO1LoM7Baw6iC7hXq5ppxWgotpE28qtHWGdVXtncpADTlQJbOugzprNUH7v0F/W7MZf6JjNq0XmUKpos6Vrg0+uNT9j204wHNwJ564ZVP5/D3jufbO3IbgfKct+wVR6geqHGdEKaYzIiurjCRJkImrbL8EZFVkObYtzO+BU8ZNAxtuNf7rSivaeKuc7PI/3FLgh+xS4HvdgQAbZxluP/vJvp7JYM/NYUwbdQQDUytYCxEE9Ahd5Zh0WnJgZA7WbICc7r4AWvml+vaG++zTn2NvW7/R3tnD3bThepu9/eqbXm7Ph3bxAsfzEsexosjNsHdcfRvkbg3uffE7l+yPLUx2feIDr5zY8f4ro+9knvoSu2udKBDo7QouBboCsYePEuPmsftGg7c8cenM8s+c3rd+cYr7a/Qrp1+VL387feaFB6efdF5//tzTv37klfeF+i4vX3j4zd8tJ9548EBw+Wu3F/AzfY++PH/vffmPSp+O757ad1L+SWLP5Bc+nKk+986x4/d+nfzhzm+WHz/6q6/Oi6cunvnyvpeeP/+D4J8+/tr0nuMXln9Tee6R4LmTU73Hn71Qyd2/Z8dbK/t//tM33zjn/vb7iw/9/da3d0+Zff967bvnjz2zb++ze6MXyz/+Vs8L2Q/eskO8fOmJUwdWls9+/smpu7Xzf1xJPj75AP/F9EPybWePXrnzb8sXn/7Lj37/2GcePbkiTVsvP/WNBwaGrtw/N/Liq8LZO4b//ZLw+mcvnb9ypMLWw/gfm0S492kgAAA=',

    // scopes
    'scopes' => "https://api.ebay.com/oauth/api_scope https://api.ebay.com/oauth/api_scope/buy.order.readonly https://api.ebay.com/oauth/api_scope/buy.guest.order https://api.ebay.com/oauth/api_scope/sell.marketing.readonly https://api.ebay.com/oauth/api_scope/sell.marketing https://api.ebay.com/oauth/api_scope/sell.inventory.readonly https://api.ebay.com/oauth/api_scope/sell.inventory https://api.ebay.com/oauth/api_scope/sell.account.readonly https://api.ebay.com/oauth/api_scope/sell.account https://api.ebay.com/oauth/api_scope/sell.fulfillment.readonly https://api.ebay.com/oauth/api_scope/sell.fulfillment https://api.ebay.com/oauth/api_scope/sell.analytics.readonly https://api.ebay.com/oauth/api_scope/sell.marketplace.insights.readonly https://api.ebay.com/oauth/api_scope/commerce.catalog.readonly https://api.ebay.com/oauth/api_scope/buy.shopping.cart https://api.ebay.com/oauth/api_scope/buy.offer.auction https://api.ebay.com/oauth/api_scope/commerce.identity.readonly https://api.ebay.com/oauth/api_scope/commerce.identity.email.readonly https://api.ebay.com/oauth/api_scope/commerce.identity.phone.readonly https://api.ebay.com/oauth/api_scope/commerce.identity.address.readonly https://api.ebay.com/oauth/api_scope/commerce.identity.name.readonly https://api.ebay.com/oauth/api_scope/commerce.identity.status.readonly https://api.ebay.com/oauth/api_scope/sell.finances https://api.ebay.com/oauth/api_scope/sell.payment.dispute https://api.ebay.com/oauth/api_scope/sell.item.draft https://api.ebay.com/oauth/api_scope/sell.item https://api.ebay.com/oauth/api_scope/sell.reputation https://api.ebay.com/oauth/api_scope/sell.reputation.readonly https://api.ebay.com/oauth/api_scope/commerce.notification.subscription https://api.ebay.com/oauth/api_scope/commerce.notification.subscription.readonly https://api.ebay.com/oauth/api_scope/sell.stores https://api.ebay.com/oauth/api_scope/sell.stores.readonly",
    // 'scopes' => "https://api.ebay.com/oauth/api_scope%20https://api.ebay.com/oauth/api_scope/sell.marketing.readonly%20https://api.ebay.com/oauth/api_scope/sell.marketing%20https://api.ebay.com/oauth/api_scope/sell.inventory.readonly%20https://api.ebay.com/oauth/api_scope/sell.inventory%20https://api.ebay.com/oauth/api_scope/sell.account.readonly%20https://api.ebay.com/oauth/api_scope/sell.account%20https://api.ebay.com/oauth/api_scope/sell.fulfillment.readonly%20https://api.ebay.com/oauth/api_scope/sell.fulfillment%20https://api.ebay.com/oauth/api_scope/sell.analytics.readonly%20https://api.ebay.com/oauth/api_scope/sell.finances%20https://api.ebay.com/oauth/api_scope/sell.payment.dispute%20https://api.ebay.com/oauth/api_scope/commerce.identity.readonly",
  ],




  // marketplaces
  "marketplaces" => [
    'EBAY_US' => [
      'region' => 'United States',
      'site_url' => 'https://www.ebay.com',
      'support_locales' => 'en-US',
    ],
    'EBAY_AT' => [
      'region' => 'Austria',
      'site_url' => 'https://www.ebay.at',
      'support_locales' => 'de-AT',
    ],
    'EBAY_AU' => [
      'region' => 'Australia',
      'site_url' => 'https://www.ebay.com.au',
      'support_locales' => 'en-AU',
    ],
    'EBAY_BE' => [
      'region' => 'Belgium',
      'site_url' => [
        'Française' => 'https://www.befr.ebay.be/',
        'Nederlandse' => 'https://www.benl.ebay.be/',
      ],
      'support_locales' => ['fr-BE', 'nl-BE'],
    ],
    'EBAY_CA' => [
      'region' => 'Canada',
      'site_url' => [
        'English' => 'https://www.ebay.ca',
        'Française' => 'https://www.cafr.ebay.ca/',
      ],
      'support_locales' => ['en-CA', 'fr-CA'],
    ],
    'EBAY_CH' => [
      'region' => 'Switzerland',
      'site_url' => 'https://www.ebay.ch',
      'support_locales' => 'de-CH',
    ],
    'EBAY_DE' => [
      'region' => 'Germany',
      'site_url' => 'https://www.ebay.de',
      'support_locales' => 'de-DE',
    ],
    'EBAY_ES' => [
      'region' => 'Spain',
      'site_url' => 'https://www.ebay.es',
      'support_locales' => 'es-ES',
    ],
    'EBAY_FR' => [
      'region' => 'France',
      'site_url' => 'https://www.ebay.fr',
      'support_locales' => 'fr-FR',
    ],
    'EBAY_GB' => [
      'region' => 'Great Britain',
      'site_url' => 'https://www.ebay.co.uk',
      'support_locales' => 'en-GB',
    ],
    'EBAY_HK' => [
      'region' => 'Hong Kong',
      'site_url' => 'https://www.ebay.com.hk',
      'support_locales' => 'zh-HK',
    ],
    'EBAY_IE' => [
      'region' => 'Ireland',
      'site_url' => 'https://www.ebay.ie',
      'support_locales' => 'en-IE',
    ],
    'EBAY_IT' => [
      'region' => 'Italy',
      'site_url' => 'https://www.ebay.it',
      'support_locales' => 'it-IT',
    ],
    'EBAY_MY' => [
      'region' => 'Malaysia',
      'site_url' => 'https://www.ebay.com.my',
      'support_locales' => 'en-US',
    ],
    'EBAY_NL' => [
      'region' => 'Netherlands',
      'site_url' => 'https://www.ebay.nl',
      'support_locales' => 'nl-NL',
    ],
    'EBAY_PH' => [
      'region' => 'Philippines',
      'site_url' => 'https://www.ebay.ph',
      'support_locales' => 'en-PH',
    ],
    'EBAY_PL' => [
      'region' => 'Poland',
      'site_url' => 'https://www.ebay.pl',
      'support_locales' => 'pl-PL',
    ],
    'EBAY_SG' => [
      'region' => 'Singapore',
      'site_url' => 'https://www.ebay.com.sg',
      'support_locales' => 'en_US',
    ],
    'EBAY_TW' => [
      'region' => 'Taiwan',
      'site_url' => 'https://www.ebay.com.tw',
      'support_locales' => 'zh-TW',
    ],
    'EBAY_MOTORS' => [
      'region' => 'United States',
      'site_url' => 'https://www.ebay.com/motors',
      'support_locales' => 'en-US',
      'Additional Information' => 'Resolves to the parent "Auto Parts and Vehicles" category on the EBAY_US site.',
    ],
  ]

];


// sandbox scops
// https://auth.sandbox.ebay.com/oauth2/authorize?client_id=AAwalHad-FullStro-SBX-df492a7b2-67d99ce2&response_type=code&redirect_uri=A_Awal_Hadi-AAwalHad-FullSt-eqogkgt&scope=https://api.ebay.com/oauth/api_scope https://api.ebay.com/oauth/api_scope/buy.order.readonly https://api.ebay.com/oauth/api_scope/buy.guest.order https://api.ebay.com/oauth/api_scope/sell.marketing.readonly https://api.ebay.com/oauth/api_scope/sell.marketing https://api.ebay.com/oauth/api_scope/sell.inventory.readonly https://api.ebay.com/oauth/api_scope/sell.inventory https://api.ebay.com/oauth/api_scope/sell.account.readonly https://api.ebay.com/oauth/api_scope/sell.account https://api.ebay.com/oauth/api_scope/sell.fulfillment.readonly https://api.ebay.com/oauth/api_scope/sell.fulfillment https://api.ebay.com/oauth/api_scope/sell.analytics.readonly https://api.ebay.com/oauth/api_scope/sell.marketplace.insights.readonly https://api.ebay.com/oauth/api_scope/commerce.catalog.readonly https://api.ebay.com/oauth/api_scope/buy.shopping.cart https://api.ebay.com/oauth/api_scope/buy.offer.auction https://api.ebay.com/oauth/api_scope/commerce.identity.readonly https://api.ebay.com/oauth/api_scope/commerce.identity.email.readonly https://api.ebay.com/oauth/api_scope/commerce.identity.phone.readonly https://api.ebay.com/oauth/api_scope/commerce.identity.address.readonly https://api.ebay.com/oauth/api_scope/commerce.identity.name.readonly https://api.ebay.com/oauth/api_scope/commerce.identity.status.readonly https://api.ebay.com/oauth/api_scope/sell.finances https://api.ebay.com/oauth/api_scope/sell.item.draft https://api.ebay.com/oauth/api_scope/sell.payment.dispute https://api.ebay.com/oauth/api_scope/sell.item https://api.ebay.com/oauth/api_scope/sell.reputation https://api.ebay.com/oauth/api_scope/sell.reputation.readonly https://api.ebay.com/oauth/api_scope/commerce.notification.subscription https://api.ebay.com/oauth/api_scope/commerce.notification.subscription.readonly

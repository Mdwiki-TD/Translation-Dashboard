<?php
header('Content-Type: application/json; charset=UTF-8');

$data = [];

$example_list_0 = [
    "Video:Cancer1" => [
        "via" => "td",
        "target" => "Video:Cancer"
    ],
    "Universal neonatal hearing screening" => [
        "via" => "before",
        "target" => "الفحص السمعي الشامل لحديثي الولادة"
    ]
];

$example_list = [
    "Video:Cancerz" => [
        "via" => "td",
        "target" => "Video:Cancer"
    ],
    "Universal neonatal hearing screening" => [
        "via" => "before",
        "target" => "الفحص السمعي الشامل لحديثي الولادة"
    ],
    "Gout" => [
        "via" => "before",
        "target" => "نقرس"
    ],
    "Gout" => [
        "via" => "before",
        "target" => "نقرس"
    ],
    "WHO AWaRe" => [
        "via" => "before",
        "target" => "تصنيف منظمة الصحة العالمية للمضادات الحيوية"
    ],
    "Vertigo" => [
        "via" => "before",
        "target" => "دوار (عرض)"
    ],
    "Vertigo" => [
        "via" => "before",
        "target" => "دوار (عرض)"
    ],
    "Vestibular schwannoma" => [
        "via" => "before",
        "target" => "ورم العصب السمعي"
    ]
];

$data["array_column"] = array_column($example_list, "via");

$data["array_diff"] = array_diff($example_list, $example_list_0);


// إزالة التكرار من example_list2

$data["array_unique"] = array_unique(["Vestibular schwannoma", "Vestibular schwannoma", "a"]);

$data["array_unique2"] = array_unique(array_keys($example_list));

var_export(json_encode($data, JSON_PRETTY_PRINT));

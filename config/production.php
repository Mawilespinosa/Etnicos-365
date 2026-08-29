<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Production stages
    |--------------------------------------------------------------------------
    |
    | Fixed 8-stage production flow, in order. Each stage has a machine key,
    | a Spanish label for the UI and its position in the flow (1-8).
    |
    */

    'stages' => [
        ['key' => 'fabric_purchase', 'label' => 'Compra de tela', 'order' => 1],
        ['key' => 'cutting', 'label' => 'Corte', 'order' => 2],
        ['key' => 'sewing', 'label' => 'Confección', 'order' => 3],
        ['key' => 'polishing', 'label' => 'Pulido', 'order' => 4],
        ['key' => 'laundry', 'label' => 'Lavandería', 'order' => 5],
        ['key' => 'packaging', 'label' => 'Empaque', 'order' => 6],
        ['key' => 'warehouse', 'label' => 'Bodega', 'order' => 7],
        ['key' => 'distribution', 'label' => 'Distribución', 'order' => 8],
    ],

    'total_stages' => 8,

    'warehouse_stage' => 7,

    'distribution_stage' => 8,

];

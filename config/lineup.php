<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Testing Mode
    |--------------------------------------------------------------------------
    |
    | Cuando está habilitado, deshabilita las restricciones de deadline
    | para permitir testing sin bloqueos temporales.
    |
    */

    'testing_mode' => env('LINEUP_TESTING_MODE', false),

    /*
    |--------------------------------------------------------------------------
    | Allow Editing After Deadline
    |--------------------------------------------------------------------------
    |
    | Solo para development/testing. Permite editar alineaciones incluso
    | después del deadline.
    |
    */

    'allow_edit_after_deadline' => env('LINEUP_ALLOW_EDIT_AFTER_DEADLINE', false),

];